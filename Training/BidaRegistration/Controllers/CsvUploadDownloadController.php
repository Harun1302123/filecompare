<?php

namespace App\Modules\BidaRegistration\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\BidaRegistration\Models\ListOfMachineryImported;
use App\Modules\BidaRegistration\Models\ListOfMachineryLocal;
use App\Modules\IrcRecommendationNew\Models\CsvUploadLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Excel;


class CsvUploadDownloadController extends Controller
{
    public function __construct()
    {
        $this->process_type_id = 102;
        $this->aclName = 'BidaRegistration';
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function attachMachineryExcelData($type_name, $app_id)
    {
        return view("BidaRegistration::excel.import", compact('type_name', 'app_id'));
    }

    public function uploadMachineryDataFromExcel(Request $request)
    {
        $this->validate($request, [
            'import_request' => 'required'
        ]);

        try {
            $data = $request->all();
            $file = $data['import_request'];
            $file_mime = $file->getMimeType();
            $mimes = array(
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.oasis.opendocument.spreadsheet',
                'application/vnd.ms-excel',
                'text/plain',
                'text/csv',
                'text/tsv'
            );

            if (in_array($file_mime, $mimes)) {
                $rand = rand(111, 999);
                $onlyFileName = 'BR_' . date("Ymd_") . $rand . time();
                $savedPath = 'uploads/csv-upload/bida-registration/'; // upload path
                if (!file_exists($savedPath)) {
                    mkdir($savedPath, 0777, true);
                }
                $extension = $file->getClientOriginalExtension(); // getting extension
                $fileName = $onlyFileName . '.' . $extension; // renaming
                $path = public_path($savedPath);
                $file->move($path, $fileName);
                $uploadingLog = new CsvUploadLog();
                $uploadingLog->file_name = $onlyFileName;
                $uploadingLog->file_path = '/' . $savedPath . $fileName;
                $uploadingLog->save();

                $filePath = Encryption::encode($savedPath . $fileName);

                return redirect('/bida-registration/request/' . $filePath . '/' . $request->get('type_name') .'/' .$request->get('app_id'));
            } else {
                return response()->json([
                    'error' => true,
                    'status' => 'csv or xls or xlsx file supported only!'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('BRImportedMachineryStore : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [BRCSV-10203]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [BRCSV-10203]'
            ]);
        }
    }

    public function machineryDataPreviewFromExcel($path, $type_name, $app_id, Excel $excel)
    {

        config(['excel.import.startRow' => 1]);
        $getFilePath = Encryption::decode($path);

        try {
            if (!file_exists($getFilePath)) {
                Session::flash('error', 'Sorry! File does not exist.');
                return redirect('bida-registration/list-of/'. $type_name .'/'.Encryption::encodeId($app_id).'/'.Encryption::encodeId($this->process_type_id));
            }

            $excelData = $excel->selectSheetsByIndex(0)->load($getFilePath)->get();

            if (empty($excelData)) {
                Session::flash('error', 'Your file is empty, please upload a valid file');
                return redirect('bida-registration/list-of/'. $type_name .'/'.Encryption::encodeId($app_id).'/'.Encryption::encodeId($this->process_type_id));
            }

            $firstrow = ($excelData->first() != null) ? $excelData->first()->toArray() : $excelData->first();

            if (count($firstrow) == 0) { // Condition for blank data sheet checking
                Session::flash('error', 'This is not a valid data sheet at least the first row of sheet will not be empty.');
                return redirect('bida-registration/list-of/'. $type_name .'/'.$app_id.'/'.Encryption::encodeId($this->process_type_id));
            }

            $tableFields = [
                0 => 'name_of_machineries',
                1 => 'quantity',
                2 => 'unit_prices_tk',
                3 => 'total_value_million_tk',
            ];

            $existFields = [];
            foreach ($firstrow as $csvColumnName => $csvColumnValue) {
                $existFields[] = $csvColumnName;
            }

            if (array_diff($existFields, $tableFields)) {
                Session::flash('error', 'Column mismatched. Please follow the given sample.');
                return redirect('bida-registration/list-of/'. $type_name .'/'.$app_id.'/'.Encryption::encodeId($this->process_type_id));
            }

            $excelData = $excelData->toArray();
            $alterStatus = 'off';
            $page_header = 'Preview Data';
            return view("BidaRegistration::excel.upload-request", compact('page_header', 'excelData', 'path', 'app_id', 'type_name','alterStatus'));
        } catch (\Exception $e) {
            Log::error('BRImportedMachineryStore : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [BRCSV-10201]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [BRCSV-10201]'
            ]);
        }
    }

    public function storeMachineryDataFromExcel(Request $request)
    {
        try {
            $appId = Encryption::decodeId($request->get('app_id'));

            DB::beginTransaction();

            if ($request->get('type_name') === 'imported-machinery') {
                foreach ($request['name_of_machineries'] as $key => $value) {
                    $importedMachinery = new ListOfMachineryImported();
                    $importedMachinery->app_id = $appId;
                    $importedMachinery->process_type_id = $this->process_type_id;
                    $importedMachinery->l_machinery_imported_name = $request->name_of_machineries[$key];
                    $importedMachinery->l_machinery_imported_qty = $request->quantity[$key];
                    $importedMachinery->l_machinery_imported_unit_price = $request->unit_prices_tk[$key];
                    $importedMachinery->l_machinery_imported_total_value = $request->total_value_million_tk[$key];
                    $importedMachinery->save();
                }
            } elseif ($request->get('type_name') === 'local-machinery') {
                foreach ($request['name_of_machineries'] as $key => $value) {
                    $localMachinery = new ListOfMachineryLocal();
                    $localMachinery->app_id = $appId;
                    $localMachinery->process_type_id = $this->process_type_id;
                    $localMachinery->l_machinery_local_name = $request->name_of_machineries[$key];
                    $localMachinery->l_machinery_local_qty = $request->quantity[$key];
                    $localMachinery->l_machinery_local_unit_price = $request->unit_prices_tk[$key];
                    $localMachinery->l_machinery_local_total_value = $request->total_value_million_tk[$key];
                    $localMachinery->save();
                }
            }

            DB::commit();

            Session::flash('success', 'Your data saved successfully');
            return redirect('bida-registration/list-of/'. $request->get('type_name') .'/'.Encryption::encodeId($appId).'/'.Encryption::encodeId($this->process_type_id));
        } catch (\Exception $e) {
           BD::rollback();
            Log::error('BRImportedMachineryStore : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [BRCSV-10201]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [BRCSV-10201]'
            ]);
        }
    }

//*****************************************End of Class********************************************
}
