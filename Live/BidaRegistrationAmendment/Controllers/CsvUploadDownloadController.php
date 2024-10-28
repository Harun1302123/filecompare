<?php

namespace App\Modules\BidaRegistrationAmendment\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\BidaRegistrationAmendment\Models\ListOfMachineryImportedAmendment;
use App\Modules\BidaRegistrationAmendment\Models\ListOfMachineryLocalAmendment;
use App\Modules\IrcRecommendationNew\Models\CsvUploadLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Excel;


class CsvUploadDownloadController extends Controller
{
    protected $process_type_id;
    protected $aclName;
    public function __construct()
    {
        $this->process_type_id = 12;
        $this->aclName = 'BidaRegistrationAmendment';
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function attachMachineryExcelData($type_name, $app_id)
    {
        $app_id = Encryption::decodeId($app_id);
        return view("BidaRegistrationAmendment::excel.import", compact('type_name', 'app_id'));
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
                $onlyFileName = 'BRA_' . date("Ymd_") . $rand . time();
                $savedPath = 'uploads/csv-upload/bida-registration-amendment/'; // upload path
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

                return redirect('/bida-registration-amendment/request/' . $filePath . '/' . $request->get('type_name') .'/' .$request->get('app_id'));
            } else {
                return response()->json([
                    'error' => true,
                    'status' => 'csv or xls or xlsx file supported only!'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('BRAImportedMachineryStore : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [BRACSV-10203]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [BRACSV-10203]'
            ]);
        }
    }

    public function machineryDataPreviewFromExcel($path, $type_name, $app_id, Excel $excel)
    {
        config(['excel.import.startRow' => 2]);
        $getFilePath = Encryption::decode($path);

        try {
            if (!file_exists($getFilePath)) {
                Session::flash('error', 'Sorry! File does not exist.');
                return redirect('process/bida-registration-amendment/edit-app/'.Encryption::encodeId($app_id).'/'.Encryption::encodeId($this->process_type_id));
            }

            $excelData = $excel->selectSheetsByIndex(0)->load($getFilePath)->get();

            if (empty($excelData)) {
                Session::flash('error', 'Your file is empty, please upload a valid file');
                return redirect('process/bida-registration-amendment/edit-app/'.Encryption::encodeId($app_id).'/'.Encryption::encodeId($this->process_type_id));
            }

            $firstrow = ($excelData->first() != null) ? $excelData->first()->toArray() : $excelData->first();

            if (count($firstrow) == 0) { // Condition for blank data sheet checking
                Session::flash('error', 'This is not a valid data sheet at least the first row of sheet will not be empty.');
                return redirect('process/bida-registration-amendment/edit-app/'.Encryption::encodeId($app_id).'/'.Encryption::encodeId($this->process_type_id));
            }

            $tableFields = [
                0 => 'ex_name_of_machineries',
                1 => 'ex_quantity',
                2 => 'ex_unit_prices_tk',
                3 => 'ex_total_value_million_tk',
                4 => 'pro_name_of_machineries',
                5 => 'pro_quantity',
                6 => 'pro_unit_prices_tk',
                7 => 'pro_total_value_million_tk',
                8 => 'action',
            ];

            $existFields = [];
            foreach ($firstrow as $csvColumnName => $csvColumnValue) {
                $existFields[] = $csvColumnName;
            }

            if (array_diff($existFields, $tableFields)) {
                Session::flash('error', 'Column mismatched. Please follow the given sample.');
                return redirect('process/bida-registration-amendment/edit-app/'.Encryption::encodeId($app_id).'/'.Encryption::encodeId($this->process_type_id));
            }

            $excelData = $excelData->toArray();
            $alterStatus = 'off';
            $page_header = 'Preview Data';
            return view("BidaRegistrationAmendment::excel.upload-request", compact('page_header', 'excelData', 'path', 'app_id', 'type_name','alterStatus'));
        } catch (\Exception $e) {
            Log::error('BRAImportedMachineryStore : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [BRACSV-10201]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [BRACSV-10201]'
            ]);
        }
    }

    public function storeMachineryDataFromExcel(Request $request)
    {

        try {
            $appId = Encryption::decodeId($request->get('app_id'));
            DB::beginTransaction();
            if ($request->get('type_name') === 'imported-machinery') {
                foreach ($request['ex_name_of_machineries'] as $key => $value) {
                    $listOfMachineryImported = new ListOfMachineryImportedAmendment();
                    $listOfMachineryImported->app_id = $appId;
                    $listOfMachineryImported->process_type_id = $this->process_type_id;
                    $listOfMachineryImported->l_machinery_imported_name = !empty($request->ex_name_of_machineries[$key]) ? $request->ex_name_of_machineries[$key] : null;
                    $listOfMachineryImported->l_machinery_imported_qty = !empty($request->ex_quantity[$key]) ? $request->ex_quantity[$key] : null;
                    $listOfMachineryImported->l_machinery_imported_unit_price = !empty($request->ex_unit_prices_tk[$key]) ? $request->ex_unit_prices_tk[$key] : null;
                    $listOfMachineryImported->l_machinery_imported_total_value = !empty($request->ex_total_value_million_tk[$key]) ? $request->ex_total_value_million_tk[$key] : null;

                    $listOfMachineryImported->n_l_machinery_imported_name = !empty($request->pro_name_of_machineries[$key]) ? $request->pro_name_of_machineries[$key] : null;
                    $listOfMachineryImported->n_l_machinery_imported_qty = !empty($request->pro_quantity[$key]) ? $request->pro_quantity[$key] : null;
                    $listOfMachineryImported->n_l_machinery_imported_unit_price = !empty($request->pro_unit_prices_tk[$key]) ? $request->pro_unit_prices_tk[$key] : null;
                    $listOfMachineryImported->n_l_machinery_imported_total_value = !empty($request->pro_total_value_million_tk[$key]) ? $request->pro_total_value_million_tk[$key] : null;
                    // $listOfMachineryImported->amendment_type = !empty($request->action[$key]) ? strtolower($request->action[$key]) : "no change";
                    $listOfMachineryImported->total_million = !empty($request->pro_total_value_million_tk[$key]) ? $request->pro_total_value_million_tk[$key] : $request->ex_total_value_million_tk[$key];
                    $listOfMachineryImported->amendment_type = (
                        empty($request->pro_name_of_machineries[$key]) &&
                        empty($request->pro_quantity[$key]) &&
                        empty($request->pro_unit_prices_tk[$key]) &&
                        empty($request->pro_total_value_million_tk[$key])
                    ) ? 'no change' : ((
                        empty($request->ex_name_of_machineries[$key]) &&
                        empty($request->ex_quantity[$key]) &&
                        empty($request->ex_unit_prices_tk[$key]) &&
                        empty($request->ex_total_value_million_tk[$key])
                    ) ? 'add' : 'edit');
                    $listOfMachineryImported->save();
                }
            } elseif ($request->get('type_name') === 'local-machinery') {
                foreach ($request->ex_name_of_machineries as $key => $value) {
                    $listOfMachineryLocal = new ListOfMachineryLocalAmendment();
                    $listOfMachineryLocal->app_id = $appId;
                    $listOfMachineryLocal->process_type_id = $this->process_type_id;
                    $listOfMachineryLocal->l_machinery_local_name = !empty($request->ex_name_of_machineries[$key]) ? $request->ex_name_of_machineries[$key] : null;
                    $listOfMachineryLocal->l_machinery_local_qty = !empty($request->ex_quantity[$key]) ? $request->ex_quantity[$key] : null;
                    $listOfMachineryLocal->l_machinery_local_unit_price = !empty($request->ex_unit_prices_tk[$key]) ? $request->ex_unit_prices_tk[$key] : null;
                    $listOfMachineryLocal->l_machinery_local_total_value = !empty($request->ex_total_value_million_tk[$key]) ? $request->ex_total_value_million_tk[$key] : null;

                    $listOfMachineryLocal->n_l_machinery_local_name = !empty($request->pro_name_of_machineries[$key]) ? $request->pro_name_of_machineries[$key] : null;
                    $listOfMachineryLocal->n_l_machinery_local_qty = !empty($request->pro_quantity[$key]) ? $request->pro_quantity[$key] : null;
                    $listOfMachineryLocal->n_l_machinery_local_unit_price = !empty($request->pro_unit_prices_tk[$key]) ? $request->pro_unit_prices_tk[$key] : null;
                    $listOfMachineryLocal->n_l_machinery_local_total_value = !empty($request->pro_total_value_million_tk[$key]) ? $request->pro_total_value_million_tk[$key] : null;
                    // $listOfMachineryLocal->amendment_type = !empty($request->action[$key]) ? strtolower($request->action[$key]) : "no change";
                    $listOfMachineryLocal->total_million = !empty($request->pro_total_value_million_tk[$key]) ? $request->pro_total_value_million_tk[$key] : $request->ex_total_value_million_tk[$key];
                    $listOfMachineryLocal->amendment_type = (
                        empty($request->pro_name_of_machineries[$key]) &&
                        empty($request->pro_quantity[$key]) &&
                        empty($request->pro_unit_prices_tk[$key]) &&
                        empty($request->pro_total_value_million_tk[$key])
                    ) ? 'no change' : ((
                        empty($request->ex_name_of_machineries[$key]) &&
                        empty($request->ex_quantity[$key]) &&
                        empty($request->ex_unit_prices_tk[$key]) &&
                        empty($request->ex_total_value_million_tk[$key])
                    ) ? 'add' : 'edit');
                    $listOfMachineryLocal->save();
                }
            }

            DB::commit();

            Session::flash('success', 'Your data saved successfully');
            return redirect('process/bida-registration-amendment/edit-app/'.Encryption::encodeId($appId).'/'.Encryption::encodeId($this->process_type_id));
        } catch (\Exception $e) {
           BD::rollback();
            Log::error('BRAImportedMachineryStore : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [BRACSV-10201]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [BRACSV-10201]'
            ]);
        }
    }

//*****************************************End of Class********************************************
}
