<?php


namespace App\Modules\IrcRecommendationSecondAdhoc\Controllers;



use App\Http\Controllers\Controller;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\IrcRecommendationSecondAdhoc\Models\ProductUnit;
use App\Modules\IrcRecommendationSecondAdhoc\Models\SecondAnnualProductionCapacity;
use App\Modules\IrcRecommendationSecondAdhoc\Models\SecondRawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AppSubDetailsController extends Controller
{
    protected $process_type_id;
    protected $app_type_id;
    protected $aclName;

    public function __construct()
    {
        $this->process_type_id = 14;
        $this->app_type_id = 2;
        $this->aclName = 'IRCRecommendationSecondAdhoc';
    }

    public function annualProductionCapacityForm($app_id)
    {
        $productUnit = ['' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->orderBy('name')->lists('name', 'id')->all();
        return \view('IrcRecommendationSecondAdhoc::annual_production_capacity.create-annual-production', compact('productUnit', 'app_id'));
    }

    public function annualProductionCapacityStore(Request $request)
    {
        //dd($request->all());
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [IRC_2ND-ASDC-10210]';
        }
        /**
         * Multiple validation
         */
        $rules = [];
        $messages = [];
        foreach ($request->em_product_name as $k => $val) {
            $rules["em_product_name.$k"] = 'required';
            $messages["em_product_name.$k.required"] = 'Product name field is required';
            $rules["em_quantity_unit.$k"] = 'required';
            $messages["em_quantity_unit.$k.required"] = 'Quantity unit field is required';
            $rules["em_quantity.$k"] = 'required';
            $messages["em_quantity.$k.required"] = 'Quantity field is required';
            $rules["em_price_usd.$k"] = 'required';
            $messages["em_price_usd.$k.required"] = 'Price (USD) field is required';
            $rules["em_value_taka.$k"] = 'required';
            $messages["em_value_taka.$k.required"] = 'Price (BD) field is required';
        }

        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validation->errors(),
            ]);
        }
        $app_id = Encryption::decodeId($request->app_id);
        try {
            DB::beginTransaction();
            if (!empty($app_id)) {
                foreach ($request->em_product_name as $proKey => $proData) {
                    $annualProduction = SecondAnnualProductionCapacity::findOrNew($request->apc_id);
                    $annualProduction->app_id = $app_id;
                    $annualProduction->product_name = $proData;
                    $annualProduction->quantity_unit = $request->em_quantity_unit[$proKey];
                    $annualProduction->quantity = $request->em_quantity[$proKey];
                    $annualProduction->price_usd = $request->em_price_usd[$proKey];
                    $annualProduction->price_taka = $request->em_value_taka[$proKey];
                    $annualProduction->save();
                }

            }
            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 'Data has been saved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('IrcRecommendationSecondAdhoc : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [IRC-2-10022]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [IRC-2-10023]'
            ]);
        }
    }

    public function loadAannualProductionCapacityData(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [IRC_2ND-ASDC-10101]';
        }
        $app_id = Encryption::decodeId($request->app_id);
        DB::statement(DB::raw('set @rownum=0'));
        $getData = SecondAnnualProductionCapacity::leftJoin('product_unit', 'product_unit.id', '=','irc_2nd_annual_production_capacity.quantity_unit')->where('app_id', $app_id)
            ->orderBy('irc_2nd_annual_production_capacity.id', 'DESC')
            ->get([DB::raw('@rownum := @rownum+1 AS sl'), 'irc_2nd_annual_production_capacity.*', 'product_unit.name as unit_name']);

        $html = strval(view("IrcRecommendationSecondAdhoc::annual_production_capacity.load_apc_data", compact('getData')));
        return response()->json(['responseCode' => 1, 'html' => $html]);
    }

    public function annualProductionCapacityEditForm($app_id)
    {
        $apcData = SecondAnnualProductionCapacity::find(Encryption::decodeId($app_id));
        $productUnit = ['' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->orderBy('name')->lists('name', 'id')->all();
        return \view('IrcRecommendationSecondAdhoc::annual_production_capacity.edit-annual-production', compact('productUnit', 'app_id', 'apcData'));
    }

    public function annualProductionCapacityDelete($app_id)
    {
        $decoded_id = Encryption::decodeId($app_id);
        SecondAnnualProductionCapacity::where(['id' => $decoded_id])->delete();

        return response()->json([
            'responseCode' => 1,
        ]);
    }


    public function rawMaterialForm($app_id, $id)
    {
        $apc_product_id = Encryption::decodeId($id);
        $annual_production_capacity = SecondAnnualProductionCapacity::where('id', $apc_product_id)->first(['unit_of_product', 'product_name']);
        $raw_material = SecondRawMaterial::where('apc_product_id', $apc_product_id)->get();
        $total_price = SecondRawMaterial::where('apc_product_id', $apc_product_id)->sum('price_taka');

        $productUnit = ['' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->orderBy('name')->lists('name', 'id')->all();

        return \view('IrcRecommendationSecondAdhoc::raw_material.create-raw-material',
            compact('app_id', 'id', 'annual_production_capacity', 'raw_material', 'total_price' , 'productUnit'));
    }

    public function storeRawMaterial(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [IRC_2ND-ASDC-10210]';
        }
        /**
         * Multiple validation
         */
        $rules = [];
        $messages = [];
        foreach ($request->get('product_name') as $k => $val) {
            $rules["product_name.$k"] = 'required';
            $messages["product_name.$k.required"] = 'Product name field is required';

            $rules["hs_code.$k"] = 'required';
            $messages["hs_code.$k.required"] = 'HS code field is required';

            $rules["quantity.$k"] = 'required';
            $messages["quantity.$k.required"] = 'Quantity field is required';

            $rules["quantity_unit.$k"] = 'required';
            $messages["quantity_unit.$k.required"] = 'Unit of quantity field is required';

            $rules["percent.$k"] = 'required';
            $messages["percent.$k.required"] = 'Percent field is required';

            $rules["price_taka.$k"] = 'required';
            $messages["price_taka.$k.required"] = 'Price (BD) field is required';
        }

        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validation->errors(),
            ]);
        }

        try {
            $app_id = Encryption::decodeId($request->app_id);
            $apc_product_id = Encryption::decodeId($request->apc_product_id);

            DB::beginTransaction();

            if (!empty($app_id)) {
                $raw_material_ids = [];

                foreach ($request->product_name as $proKey => $proData) {
                    $raw_material_id = $request->get('raw_material_id')[$proKey];
                    $raw_material = SecondRawMaterial::findOrNew($raw_material_id);
                    $raw_material->app_id = $app_id;
                    $raw_material->apc_product_id = $apc_product_id;
                    $raw_material->product_name = $proData;
                    $raw_material->hs_code = $request->get('hs_code')[$proKey];
                    $raw_material->quantity = $request->get('quantity')[$proKey];
                    $raw_material->quantity_unit = $request->get('quantity_unit')[$proKey];
                    $raw_material->percent = $request->get('percent')[$proKey];
                    $raw_material->price_taka = $request->get('price_taka')[$proKey];
                    $raw_material->save();
                    $raw_material_ids[] = $raw_material->id;
                }

                if (count($raw_material_ids) > 0) {
                    SecondRawMaterial::where('apc_product_id', $raw_material->apc_product_id)->whereNotIn('id',$raw_material_ids)->delete();
                }

                SecondAnnualProductionCapacity::where('app_id', $app_id)->where('id', $apc_product_id)
                    ->update([
                        'unit_of_product' => $request->get('unit_of_product'),
                        'raw_material_total_price' => $request->get('raw_material_total_price')
                    ]);

            }
            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 'Data has been saved successfully',
                //'link' => '/bida-registration/list-of/annual-production/'.$request->get('app_id').'/'.Encryption::encodeId($request->process_type_id)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('IrcRecommendationSecondAdhoc : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [IRC-2-10020]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [IRC_2ND-ASDC-10021]'
            ]);
        }
    }

}