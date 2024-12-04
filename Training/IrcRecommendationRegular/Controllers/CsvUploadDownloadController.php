<?php

namespace App\Modules\IrcRecommendationRegular\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\IrcRecommendationRegular\Models\ProductUnit;
use App\Modules\IrcRecommendationRegular\Models\RegularAnnualProductionCapacity;
use App\Modules\IrcRecommendationRegular\Models\CsvUploadLog;
use App\Modules\IrcRecommendationRegular\Models\RegularRawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Excel;

class CsvUploadDownloadController extends Controller
{
    public function __construct()
    {
        $this->process_type_id = 16;
        $this->aclName = 'IRCRecommendationRegular';
    }

    public function importRequest($app_id, $apc_id)
    {
        $annual_product_name = RegularAnnualProductionCapacity::where('id', Encryption::decodeId($apc_id))->first(['product_name']);
        return view("IrcRecommendationRegular::excel.import", compact('app_id', 'apc_id', 'annual_product_name'));
    }

    public function uploadCsvFile(Request $request)
    {
        $this->validate($request, [
            'unit_of_product' => 'required',
            'import_request' => 'required'
        ]);

        try {
            $data = $request->all();
            $file = $data['import_request'];
            $file_mime = $file->getClientMimeType();
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
                $onlyFileName = 'IRCR_Raw_' . date("Ymd_") . $rand . time();
                $savedPath = 'uploads/csv-upload/'; // upload path
                $extension = $file->getClientOriginalExtension(); // getting extension
                $fileName = $onlyFileName . '.' . $extension; // renaming
                $path = public_path($savedPath);
                $file->move($path, $fileName);
                $uploadingLog = new CsvUploadLog();
                $uploadingLog->file_name = $onlyFileName;
                $uploadingLog->file_path = '/' . $savedPath . $fileName;
                $uploadingLog->save();
                $filePath = Encryption::encode($savedPath . $fileName);

                return redirect('/irc-recommendation-regular/request/' . $filePath . '/' . $request->get('app_id') . '/' . $request->get('apc_id') . '/' . $request->get('unit_of_product'));
            } else {
                return response()->json([
                    'error' => true,
                    'status' => 'csv or xls or xlsx file supported only!'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('IRCRegularRawMaterialStore : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [IRCRNCSV-100]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [IRC3NCSV-100]'
            ]);
        }
    }

    public function previewDataFromCsv($path, $app_id, $apc_id, $unit_of_product, Excel $excel)
    {
        config(['excel.import.startRow' => 1]);
        $getFilePath = Encryption::decode($path);

        try {
            if (!file_exists($getFilePath)) {
                Session::flash('error', 'Sorry! File does not exist.');
                return redirect()->back();
            }

            $excelData = $excel->selectSheetsByIndex(0)->load($getFilePath)->get();

            if (empty($excelData)) {
                Session::flash('error', 'Your file is empty, please upload a valid file');
                return redirect()->back();
            }

            $firstrow = ($excelData->first() != null) ? $excelData->first()->toArray() : $excelData->first();

            if (count($firstrow) == 0) { // Condition for blank data sheet checking
                Session::flash('error', 'This is not a valid data sheet at least the first row of sheet will not be empty.');
                return redirect()->back();
            }

            $tableFields = [
                0 => 'name',
                1 => 'hs_code',
                2 => 'quantity',
                3 => 'unit_of_quantity',
                4 => 'percentage',
                5 => 'price_bd'
            ];

            $existFields = [];
            foreach ($firstrow as $csvColumnName => $csvColumnValue) {
                $existFields[] = $csvColumnName;
            }

            if (array_diff($existFields, $tableFields)) {
                Session::flash('error', 'Column mismatched. Please follow the given sample.');
                return redirect()->back();
            }

            $excelData = $excelData->toArray();
            $alterStatus = 'off';

            $page_header = 'Preview Data';
            return view("IrcRecommendationRegular::excel.upload-request", compact('page_header', 'excelData', 'path', 'app_id', 'apc_id', 'unit_of_product', 'alterStatus'));
        } catch (\Exception $e) {
            Log::error('IRCRawMaterialStore : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [IRC3NCSV-101]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [IRC3NCSV-101]'
            ]);
        }
    }

    public function saveDataFromCsv(Request $request)
    {
        try {
            $appId = Encryption::decodeId($request->get('app_id'));
            $apcId = Encryption::decodeId($request->get('apc_id'));
            $productUnit = ProductUnit::where('status', 1)->where('is_archive', 0)->orderBy('name')->lists('name', 'id')->all();

            $arrayData = [];

//            $total_price_bd = 0;
            for ($i = 0; $i < count($request->get('name')); $i++) {
                $app_id[] = $appId;
                $apc_id[] = $apcId;

//                $total_price_bd += $request->get('price_bd')[$i];
            }

            $arrayData['app_id'] = $app_id;
            $arrayData['apc_product_id'] = $apc_id;
            $arrayData['product_name'] = $request->get('name');
            $arrayData['hs_code'] = $request->get('hs_code');
            $arrayData['quantity'] = $request->get('quantity');
            $arrayData['quantity_unit'] = $request->get('unit_of_quantity');
            $arrayData['percent'] = $request->get('percentage');
            $arrayData['price_taka'] = $request->get('price_bd');

            $dataArray = [];
            $i = 0;

            foreach (current($arrayData) as $valueIndex => $info) {
                foreach ($arrayData as $fieldName => $values) {
                    if ($fieldName == 'quantity_unit') {
                        $values[$valueIndex] = array_search($values[$valueIndex], $productUnit);
                    }

                    if (in_array($values[$valueIndex], ['', 'n/a', 'N/A', 'n/A', 'N/a', false])) {
                        $values[$valueIndex] = '';
                    }

                    $dataArray[$i][$fieldName] = $values[$valueIndex];
                }
                $i++;
            }

            DB::beginTransaction();

            foreach ($dataArray as $data) {
                RegularRawMaterial::create($data);
            }

            $total_price_bd = RegularRawMaterial::where('app_id', $appId)->where('apc_product_id',$apcId)->sum('price_taka');

            RegularAnnualProductionCapacity::where('app_id', $appId)->where('id', $apcId)
                ->update([
                    'unit_of_product' => $request->unit_of_product,
                    'raw_material_total_price' => $total_price_bd
                ]);

            DB::commit();

            Session::flash('success', 'Your data saved successfully');
            return redirect('process/irc-recommendation-regular/edit-app/'.Encryption::encodeId($appId).'/'.Encryption::encodeId($this->process_type_id));
        } catch (\Exception $e) {
            Log::error('IRC3RawMaterialStore : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [IRCRNCSV-102]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [IRCRNCSV-102]'
            ]);
        }
    }

//*****************************************End of Class********************************************
}
