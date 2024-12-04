<?php

namespace App\Modules\IrcRecommendationThirdAdhoc\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\ImageProcessing;
use App\Libraries\UtilFunction;
use App\Modules\Apps\Models\AppDocuments;
use App\Modules\BasicInformation\Models\EA_OrganizationStatus;
use App\Modules\BasicInformation\Models\EA_OrganizationType;
use App\Modules\BasicInformation\Models\EA_OwnershipStatus;
use App\Modules\BidaRegistration\Models\LaAnnualProductionCapacity;
use App\Modules\BidaRegistration\Models\ListOfDirectors;
use App\Modules\BidaRegistration\Models\SourceOfFinance;
use App\Modules\IrcRecommendationNew\Models\AnnualProductionCapacity;
use App\Modules\IrcRecommendationNew\Models\AnnualProductionSpareParts;
use App\Modules\IrcRecommendationNew\Models\InspectionAnnualProduction;
use App\Modules\IrcRecommendationNew\Models\IrcInspection;
use App\Modules\IrcRecommendationNew\Models\IrcOtherLicenceNocPermission;
use App\Modules\IrcRecommendationNew\Models\IrcSourceOfFinance;
use App\Modules\IrcRecommendationThirdAdhoc\Models\BusinessClass;
use App\Modules\IrcRecommendationThirdAdhoc\Models\IrcBrAnnualProductionCapacity;
use App\Modules\IrcRecommendationThirdAdhoc\Models\IrcProjectStatus;
use App\Modules\IrcRecommendationThirdAdhoc\Models\IrcPurpose;
use App\Modules\IrcRecommendationThirdAdhoc\Models\IrcRecommendationThirdAdhoc;
use App\Modules\IrcRecommendationThirdAdhoc\Models\IrcSalesStatement;
use App\Modules\IrcRecommendationThirdAdhoc\Models\IrcSixMonthsImportRawMaterial;
use App\Modules\IrcRecommendationThirdAdhoc\Models\IrcTypes;
use App\Modules\IrcRecommendationThirdAdhoc\Models\ProductUnit;
use App\Modules\IrcRecommendationThirdAdhoc\Models\ThirdAnnualProductionCapacity;
use App\Modules\IrcRecommendationThirdAdhoc\Models\ThirdRawMaterial;
use App\Modules\IrcRecommendationThirdAdhoc\Models\ThirdAnnualProductionSpareParts;
use App\Modules\IrcRecommendationThirdAdhoc\Models\ThirdInspectionAnnualProduction;
use App\Modules\IrcRecommendationThirdAdhoc\Models\ThirdInspectionAnnualProductionSpareParts;
use App\Modules\IrcRecommendationThirdAdhoc\Models\ThirdIrcInspection;
use App\Modules\IrcRecommendationThirdAdhoc\Models\ThirdIrcOtherLicenceNocPermission;
use App\Modules\IrcRecommendationThirdAdhoc\Models\ThirdIrcSourceOfFinance;
use App\Modules\IrcRecommendationThirdAdhoc\Models\SecondRawMaterial;
use App\Modules\ProcessPath\Models\ProcessHistory;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\Settings\Models\Attachment;
use App\Modules\Settings\Models\Bank;
use App\Modules\Settings\Models\Configuration;
use App\Modules\Settings\Models\Currencies;
use App\Modules\SonaliPayment\Models\PaymentConfiguration;
use App\Modules\SonaliPayment\Models\PaymentDetails;
use App\Modules\SonaliPayment\Models\PaymentDistribution;
use App\Modules\SonaliPayment\Models\SonaliPayment;
use App\Modules\Users\Models\AreaInfo;
use App\Modules\Users\Models\Countries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Validator;
//use mPDF;
use Mpdf\Mpdf;

class AppSubDetailsController extends Controller
{
    protected $process_type_id;
    protected $app_type_id;
    protected $aclName;

    public function __construct()
    {
        $this->process_type_id = 15;
        $this->app_type_id = 2;
        $this->aclName = 'IRCRecommendationThirdAdhoc';
    }

    
    public function annualProductionCapacityForm($app_id)
    {
        $productUnit = ['' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->orderBy('name')->lists('name', 'id')->all();
        return \view('IrcRecommendationThirdAdhoc::annual_production_capacity.create-annual-production', compact('productUnit', 'app_id'));
    }

    public function annualProductionCapacityStore(Request $request)
    {
        //dd($request->all());
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-10210]';
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
                    $annualProduction = ThirdAnnualProductionCapacity::findOrNew($request->apc_id);
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
            Log::error('IRCRecommendationThirdAdhoc : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [IRC-3-10022]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [IRC-3-10023]'
            ]);
        }
    }

    public function loadAannualProductionCapacityData(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-10101]';
        }
        $app_id = Encryption::decodeId($request->app_id);
        DB::statement(DB::raw('set @rownum=0'));
        $getData = ThirdAnnualProductionCapacity::leftJoin('product_unit', 'product_unit.id', '=','irc_3rd_annual_production_capacity.quantity_unit')->where('app_id', $app_id)
            ->orderBy('irc_3rd_annual_production_capacity.id', 'DESC')
            ->get([DB::raw('@rownum := @rownum+1 AS sl'), 'irc_3rd_annual_production_capacity.*', 'product_unit.name as unit_name']);

                
        $html = strval(view("IrcRecommendationThirdAdhoc::annual_production_capacity.load_apc_data", compact('getData')));
        return response()->json(['responseCode' => 1, 'html' => $html]);
    }

    public function annualProductionCapacityEditForm($app_id)
    {
        $apcData = ThirdAnnualProductionCapacity::find(Encryption::decodeId($app_id));
        $productUnit = ['' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->orderBy('name')->lists('name', 'id')->all();
        return \view('IrcRecommendationThirdAdhoc::annual_production_capacity.edit-annual-production', compact('productUnit', 'app_id', 'apcData'));
    }

    public function annualProductionCapacityDelete($app_id)
    {
        $decoded_id = Encryption::decodeId($app_id);
        ThirdAnnualProductionCapacity::where(['id' => $decoded_id])->delete();

        return response()->json([
            'responseCode' => 1,
        ]);
    }

    public function directorForm($app_id)
    {
        $nationality = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->where('nationality', '!=', '')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();
        $passport_nationalities = Countries::orderby('nationality')->where('nationality', '!=', '')->where('nationality', '!=', 'Bangladeshi')
            ->lists('nationality', 'id');
        $passport_types = [
            'ordinary' => 'Ordinary',
            'diplomatic' => 'Diplomatic',
            'official' => 'Official',
        ];

        $process_type_id = $this->process_type_id;

        return view('IrcRecommendationThirdAdhoc::director.create',
            compact('nationality', 'app_id', 'process_type_id', 'passport_nationalities','passport_types'));
    }

    /**
     * store NID, ETIN, Passport information
     * @param  Request  $request
     * @return JsonResponse|string
     */
    public function storeVerifyDirector(Request $request)
    {
       
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [IRC-3-1027]';
        }
        $rules = [];
        $messages = [];

        $app_id = Encryption::decodeId($request->app_id);
        $process_type_id = Encryption::decodeId($request->process_type_id);
       
        if ($request->btn_save == 'NID') {
            $nid_tin_passport = $request->user_nid;

            $rules['user_nid'] = 'required|numeric';
            $rules['nid_dob'] = 'required|date|date_format:d-M-Y';
            $rules['nid_name'] = 'required';
            $rules['nid_designation'] = 'required';
            $rules['nid_nationality'] = 'required';

        } elseif ($request->btn_save == 'ETIN') {
            $nid_tin_passport = $request->user_tin;

            $rules['user_etin'] = 'required|numeric';
            $rules['etin_dob'] = 'required|date|date_format:d-M-Y';
            $rules['etin_name'] = 'required';
            $rules['etin_designation'] = 'required';
            $rules['etin_nationality'] = 'required';

        } elseif ($request->btn_save == 'passport') {
            $nid_tin_passport = $request->passport_no;

            $rules['passport_surname'] = 'required';
            $rules['passport_given_name'] = 'required';
            $rules['passport_DOB'] = 'required';
            $rules['passport_type'] = 'required';
            $rules['passport_no'] = 'required';
            $rules['passport_date_of_expire'] = 'required';
        }

        $rules['nationality_type'] = 'required|in:bangladeshi,foreign';
        $rules['identity_type'] = 'required|in:nid,tin,passport';
        $rules['gender'] = 'required|in:male,female,other';


        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validation->errors(),
            ]);
        }
        //check existing director from list
        $duplicateRowCheck = ListOfDirectors::Where('app_id', $app_id)->where('process_type_id',
            $process_type_id)->where('nid_etin_passport', $nid_tin_passport)->first();
        if (count($duplicateRowCheck) > 0) {
            return response()->json([
                'error' => true,
                'status' => 'The director information already exists for this application. [ASDC-1083]'
            ]);
        }

        try {
            DB::beginTransaction();
            $directorInfo = new ListOfDirectors();
            $directorInfo->app_id = $app_id;
            $directorInfo->process_type_id = $process_type_id;
            $directorInfo->nationality_type = $request->nationality_type;
            $directorInfo->identity_type = $request->identity_type;

            if ($request->btn_save == 'NID') {
                //NID session data
                $nid_data = json_decode(Encryption::decode(Session::get('nid_info')));

                $directorInfo->l_director_name = $nid_data->return->voterInfo->voterInfo->nameEnglish;
                $directorInfo->date_of_birth = date('Y-m-d',
                    strtotime($nid_data->return->voterInfo->voterInfo->dateOfBirth));
                $directorInfo->nid_etin_passport = $nid_data->return->nid;
                $directorInfo->l_director_designation = $request->nid_designation;
                $directorInfo->l_director_nationality = $request->nid_nationality;

            } elseif ($request->btn_save == 'ETIN') {
                //ETIN session data
                $eTin_data = json_decode(Encryption::decode(Session::get('eTin_info')));

                $directorInfo->l_director_name = $eTin_data->assesName;
                $directorInfo->nid_etin_passport = $eTin_data->etin_number;
                $directorInfo->date_of_birth = date('Y-m-d', strtotime($eTin_data->dob));
                $directorInfo->l_director_designation = $request->etin_designation;
                $directorInfo->l_director_nationality = $request->etin_nationality;

            } elseif ($request->btn_save == 'passport') {
                $directorInfo->l_director_name = ucfirst(strtolower($request->passport_given_name)).' '.ucfirst(strtolower($request->passport_surname));
                $directorInfo->date_of_birth = date('Y-m-d', strtotime($request->passport_DOB));
                $directorInfo->l_director_nationality = $request->passport_nationality;
                $directorInfo->l_director_designation = $request->l_director_designation;
                $directorInfo->passport_type = $request->passport_type;
                $directorInfo->nid_etin_passport = $request->passport_no;
                $directorInfo->date_of_expiry = date('Y-m-d', strtotime($request->passport_date_of_expire));

                // Passport copy upload
                $yearMonth = date("Y")."/".date("m")."/";
                $path = 'users/upload/'.$yearMonth;
                $passport_pic_name = trim(uniqid('BIDA_PC_PN-'.$request->passport_no.'_', true).'.'.'jpeg');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                if (!empty($request->get('passport_upload_manual_file'))) {
                    $passport_split = explode(',', substr($request->get('passport_upload_manual_file'), 5), 2);
                    $passport_image_data = $passport_split[1];
                    $passport_base64_decode = base64_decode($passport_image_data);
                    file_put_contents($path.$passport_pic_name, $passport_base64_decode);
                } else {
                    $passport_split = explode(',', substr($request->get('passport_upload_base_code'), 5), 2);
                    $passport_image_data = $passport_split[1];
                    $passport_base64_decode = base64_decode($passport_image_data);
                    file_put_contents($path.$passport_pic_name, $passport_base64_decode);
                }

                $directorInfo->passport_scan_copy = $passport_pic_name;
            }

            $directorInfo->gender = $request->gender;
            $directorInfo->save();
            DB::commit();

            /*
             * destroy NID session data ...
             */
            if ($directorInfo->identity_type == 'nid') {
                Session::forget('nid_info');
            }

            /*
             * destroy ETIN session data ...
             */
            if ($directorInfo->identity_type == 'tin') {
                Session::forget('eTin_info');
            }

            return response()->json([
                'success' => true,
                'status' => 'Data has been saved successfully',
                //'link' => '/bida-registration/list-of/director/'.$request->app_id.'/'.$request->encoded_process_type
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('IRCRecommendationThirdAdhoc : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [ASDC-1050]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).'[IRC-3-1050]'
            ]);
        }
    }

    public function loadListOfDirectiors(Request $request)
    {
        $app_id = Encryption::decodeId($request->app_id);
        $process_type_id = Encryption::decodeId($request->process_type_id);

        DB::statement(DB::raw('set @rownum=0'));
        $getData = ListOfDirectors::leftJoin('country_info', 'list_of_directors.l_director_nationality', '=',
            'country_info.id')
            ->where('app_id', $app_id)
            ->where('process_type_id', $process_type_id)
            ->orderBy('list_of_directors.id', 'DESC')
            ->get([
                DB::raw('@rownum := @rownum+1 AS sl'),
                'list_of_directors.id',
                'list_of_directors.l_director_name',
                'list_of_directors.l_director_designation',
                'country_info.nationality as nationality',
                'list_of_directors.nid_etin_passport'
            ]);

        $html = strval(view("IrcRecommendationThirdAdhoc::director.load-director-data", compact('getData')));
        return response()->json(['responseCode' => 1, 'html' => $html]);
    }


    public function directorEditForm($id)
    {
        $nationality = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->where('nationality', '!=', '')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();
        $directorInfo= ListOfDirectors::find(Encryption::decodeId($id));
        return view("IrcRecommendationThirdAdhoc::director.edit", compact('directorInfo', 'nationality'));
    }

    public function directorUpdate(Request $request)
    {
        try {
            DB::beginTransaction();

            $directorInfo = ListOfDirectors::findOrFail(Encryption::decodeId($request->id));
            $directorInfo->l_director_name = $request->l_director_name;
            $directorInfo->date_of_birth = (!empty($request->get('date_of_birth')) ? date('Y-m-d', strtotime($request->get('date_of_birth'))) : null);
            $directorInfo->gender = $request->gender;
            $directorInfo->l_director_designation = $request->l_director_designation;
            $directorInfo->l_director_nationality = $request->l_director_nationality;
            $directorInfo->passport_type = $request->passport_type;
            $directorInfo->nid_etin_passport = $request->nid_etin_passport;
            $directorInfo->date_of_expiry = (!empty($request->get('date_of_expiry')) ? date('Y-m-d', strtotime($request->get('date_of_expiry'))) : null);
            $directorInfo->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 'Data has been updated successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('IRCRecommendationThirdAdhoc : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [IRC-3-10022]');
            return response()->json([
                'success' => false,
                'status' => 'Something went wrong!' . $e->getMessage(),
            ]);
        }
    }

    public function directorDelete($id)
    {
        $decoded_id = Encryption::decodeId($id);
        ListOfDirectors::where(['id' => $decoded_id])->delete();

        return response()->json([
            'responseCode' => 1,
        ]);
    }

    public function rawMaterialForm($app_id, $id)
    {
        $apc_product_id = Encryption::decodeId($id);
        $annual_production_capacity = ThirdAnnualProductionCapacity::where('id', $apc_product_id)->first(['unit_of_product', 'product_name']);
        $raw_material = ThirdRawMaterial::where('apc_product_id', $apc_product_id)->get();
        $total_price = ThirdRawMaterial::where('apc_product_id', $apc_product_id)->sum('price_taka');

        $productUnit = ['' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->orderBy('name')->lists('name', 'id')->all();
        
        return \view('IrcRecommendationThirdAdhoc::raw_material.create-raw-material',
            compact('app_id', 'id', 'annual_production_capacity', 'raw_material', 'total_price' , 'productUnit'));
    }

    /**
     * store raw material
     * @param  Request  $request
     * @return JsonResponse|string
     */
    public function storeRawMaterial(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-10210]';
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
                        $raw_material = ThirdRawMaterial::findOrNew($raw_material_id);
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
                        ThirdRawMaterial::where('apc_product_id', $raw_material->apc_product_id)->whereNotIn('id',$raw_material_ids)->delete();
                    }

                    ThirdAnnualProductionCapacity::where('app_id', $app_id)->where('id', $apc_product_id)
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
            Log::error('IRCRecommendationThirdAdhoc : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [IRC-3-10020]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [ASDC-10021]'
            ]);
        }
    }

}