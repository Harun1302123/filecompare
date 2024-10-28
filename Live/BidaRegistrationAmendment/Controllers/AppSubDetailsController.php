<?php

namespace App\Modules\BidaRegistrationAmendment\Controllers;

use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\UtilFunction;
use App\Modules\Apps\Models\pdfSignatureQrcode;
use App\Modules\BidaRegistrationAmendment\Models\ListOfDirectorsAmendment;
use App\Modules\BidaRegistrationAmendment\Models\ProductUnit;
use App\Modules\BidaRegistrationAmendment\Models\AnnualProductionCapacityAmendment;
use App\Modules\BidaRegistrationAmendment\Models\ListOfMachineryImportedAmendment;
use App\Modules\BidaRegistrationAmendment\Models\ListOfMachineryLocalAmendment;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\Users\Models\AreaInfo;
use App\Modules\Users\Models\Countries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\BidaRegistrationAmendment\Models\BidaRegistrationAmendment;
use App\Modules\ImportPermission\Models\MasterMachineryImported;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use Mpdf\Mpdf;
use mysql_xdevapi\Exception;
use Validator;

class AppSubDetailsController extends Controller
{
    protected $process_type_id;
    protected $aclName;

    public function __construct()
    {
        $this->process_type_id = 12;
        $this->aclName = 'BidaRegistrationAmendment';
    }

    /**
     * @param $appId
     * @return mixed
     * list of director and machinery pdf
     */

    public function directorsMachineriesPDF($appId)
    {
        if (!ACL::getAccsessRight($this->aclName, '-V-')) {
            die('You have no access right! Please contact system administration for more information. [BRAC-979]');
        }

        try {
            $decodedAppId = Encryption::decodeId($appId);

            //Generate PDF here....
            UtilFunction::getBRAListOfDirectorsAndMachinery($decodedAppId, $this->process_type_id, 'I');

        } catch (\Exception $e) {
            Log::error('BRAListOfDirectorMachinery : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRAC-1115]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [BRAC-1115]');
            return Redirect::back()->withInput();
        }
    }

    //bra director manage start
    public function directorForm($app_id)
    {
        $encoded_process_type = Encryption::encodeId($this->process_type_id);
        $nationality = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->where('nationality', '!=',
                '')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();
        $passport_nationalities = Countries::orderby('nationality')->where('nationality', '!=',
            '')->where('nationality', '!=', 'Bangladeshi')
            ->lists('nationality', 'id');
        $passport_types = [
            'ordinary' => 'Ordinary',
            'diplomatic' => 'Diplomatic',
            'official' => 'Official',
        ];
        return view("BidaRegistrationAmendment::director.create", compact('app_id', 'encoded_process_type', 'nationality', 'passport_nationalities', 'passport_types'));
    }

    public function directorList(Request $request)
    {
        $app_id = Encryption::decodeId($request->app_id);
        $viewMode = $request->viewMode;
        $limit = $request->limit;
        $query = ListOfDirectorsAmendment::leftJoin('country_info as ex_nationality', 'ex_nationality.id', '=', 'list_of_director_amendment.l_director_nationality')
            ->leftJoin('country_info as pro_nationality', 'pro_nationality.id', '=', 'list_of_director_amendment.n_l_director_nationality')
            ->where('app_id', $app_id)->where('process_type_id', $this->process_type_id)
            ->where('amendment_type', '!=', 'delete')
            ->orderBy('list_of_director_amendment.id', 'ASC');

        if ($limit != 'all') {
            $query->limit($limit);
        }

        $list_of_directors = $query->get([
            'ex_nationality.nationality as ex_nationality',
            'pro_nationality.nationality as pro_nationality',
            'list_of_director_amendment.*',
        ]);
//        $amendment_type = [
//            '' => 'Select One',
//            'add' => 'Add',
//            'edit' => 'Edit',
//            'delete' => 'Delete',
//            'no change' => 'No Change',
//        ];

        $html = strval(view("BidaRegistrationAmendment::director.list", compact('list_of_directors', 'viewMode')));
        return response()->json(['responseCode' => 1, 'html' => $html]);
    }

    /**
     * store NID, ETIN, Passport information
     * @param Request $request
     * @return JsonResponse|string
     */
    public function storeVerifyDirector(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-1027]';
        }
        $rules = [];
        $messages = [];

        $app_id = Encryption::decodeId($request->app_id);
        $process_type_id = Encryption::decodeId($request->encoded_process_type);

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
        $rules['amendment_type'] = 'required';

        $validation = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validation->errors(),
            ]);
        }
        // check existing director from list

        // $duplicateRowCheck = ListOfDirectorsAmendment::Where('app_id', $app_id)
        //                     ->where('process_type_id', $process_type_id)->where('n_nid_etin_passport', $nid_tin_passport)->first();

        $duplicateRowCheck = ListOfDirectorsAmendment::where('app_id', $app_id)
                                ->where('process_type_id', $process_type_id)
                                ->where(function ($query) use ($nid_tin_passport) {
                                    $query->where('nid_etin_passport', $nid_tin_passport)
                                        ->orWhere('n_nid_etin_passport', $nid_tin_passport);
                                })
                                ->where('amendment_type', '!=', 'delete')
                                ->orderBy('id', 'desc')
                                ->first();

        if ($duplicateRowCheck !== null) {
            return response()->json([
                'error' => true,
                'status' => 'The director information already exists for this application. [ASDC-1083]'
            ]);
        }

        try {
            DB::beginTransaction();

            $amendment_type = $request->get('amendment_type');

            $amendment_director = new ListOfDirectorsAmendment();
            $amendment_director->app_id = $app_id;
            $amendment_director->process_type_id = $process_type_id;

            if ($amendment_type == 'existing') {

                $amendment_director->nationality_type = $request->get('nationality_type');
                $amendment_director->identity_type = $request->get('identity_type');
                $amendment_director->gender = $request->get('gender');

            } elseif ($amendment_type == 'proposed') {

                $amendment_director->n_nationality_type = $request->get('nationality_type');
                $amendment_director->n_identity_type = $request->get('identity_type');
                $amendment_director->n_gender = $request->get('gender');
            }

            if ($request->get('btn_save') == 'NID') {
                //NID session data
                $nid_data = json_decode(Encryption::decode(Session::get('nid_info')));

                if ($amendment_type == 'existing') {
                    // $amendment_director->l_director_name = $nid_data->return->voterInfo->voterInfo->nameEnglish;
                    // $amendment_director->date_of_birth = date('Y-m-d',strtotime($nid_data->return->voterInfo->voterInfo->dateOfBirth));
                    // $amendment_director->nid_etin_passport = $nid_data->return->nid;
                    $amendment_director->l_director_name = $nid_data->nameEn;
                    $amendment_director->date_of_birth = date('Y-m-d', strtotime($nid_data->dateOfBirth));
                    $amendment_director->nid_etin_passport = $nid_data->nationalId;
                    $amendment_director->l_director_designation = $request->get('nid_designation');
                    $amendment_director->l_director_nationality = $request->get('nid_nationality');
                } elseif ($amendment_type == 'proposed') {
                    // $amendment_director->n_l_director_name = $nid_data->return->voterInfo->voterInfo->nameEnglish;
                    // $amendment_director->n_date_of_birth = date('Y-m-d',strtotime($nid_data->return->voterInfo->voterInfo->dateOfBirth));
                    // $amendment_director->n_nid_etin_passport = $nid_data->return->nid;
                    $amendment_director->n_l_director_name = $nid_data->nameEn;
                    $amendment_director->n_date_of_birth = date('Y-m-d', strtotime($nid_data->dateOfBirth));
                    $amendment_director->n_nid_etin_passport = $nid_data->nationalId;
                    $amendment_director->n_l_director_designation = $request->get('nid_designation');
                    $amendment_director->n_l_director_nationality = $request->get('nid_nationality');
                }

            } elseif ($request->get('btn_save') == 'ETIN') {
                //ETIN session data
                $eTin_data = json_decode(Encryption::decode(Session::get('eTin_info')));

                if ($amendment_type == 'existing') {
                    $amendment_director->l_director_name = $eTin_data->assesName;
                    $amendment_director->nid_etin_passport = $eTin_data->etin_number;
                    $amendment_director->date_of_birth = date('Y-m-d', strtotime($eTin_data->dob));
                    $amendment_director->l_director_designation = $request->get('etin_designation');
                    $amendment_director->l_director_nationality = $request->get('etin_nationality');
                } elseif ($amendment_type == 'proposed') {
                    $amendment_director->n_l_director_name = $eTin_data->assesName;
                    $amendment_director->n_nid_etin_passport = $eTin_data->etin_number;
                    $amendment_director->n_date_of_birth = date('Y-m-d', strtotime($eTin_data->dob));
                    $amendment_director->n_l_director_designation = $request->get('etin_designation');
                    $amendment_director->n_l_director_nationality = $request->get('etin_nationality');
                }

            } elseif ($request->get('btn_save') == 'passport') {

                if ($amendment_type == 'existing') {
                    $amendment_director->l_director_name = ucfirst(strtolower($request->get('passport_given_name'))) . ' ' . ucfirst(strtolower($request->get('passport_surname')));
                    $amendment_director->date_of_birth = date('Y-m-d', strtotime($request->get('passport_DOB')));
                    $amendment_director->l_director_nationality = $request->get('passport_nationality');
                    $amendment_director->l_director_designation = $request->get('l_director_designation');
                    $amendment_director->passport_type = $request->get('passport_type');
                    $amendment_director->nid_etin_passport = $request->get('passport_no');
                    $amendment_director->date_of_expiry = date('Y-m-d', strtotime($request->get('passport_date_of_expire')));
                } elseif ($amendment_type == 'proposed') {
                    $amendment_director->n_l_director_name = ucfirst(strtolower($request->get('passport_given_name'))) . ' ' . ucfirst(strtolower($request->get('passport_surname')));
                    $amendment_director->n_date_of_birth = date('Y-m-d', strtotime($request->get('passport_DOB')));
                    $amendment_director->n_l_director_nationality = $request->get('passport_nationality');
                    $amendment_director->n_l_director_designation = $request->get('l_director_designation');
                    $amendment_director->n_passport_type = $request->get('passport_type');
                    $amendment_director->n_nid_etin_passport = $request->get('passport_no');
                    $amendment_director->n_date_of_expiry = date('Y-m-d', strtotime($request->get('passport_date_of_expire')));
                }

                // Passport copy upload
                $yearMonth = date("Y") . "/" . date("m") . "/";
                $path = 'users/upload/' . $yearMonth;
                $passport_pic_name = trim(uniqid('BIDA_PC_PN-' . $request->get('passport_no') . '_', true) . '.' . 'jpeg');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                if (!empty($request->get('passport_upload_manual_file'))) {
                    $passport_split = explode(',', substr($request->get('passport_upload_manual_file'), 5), 2);
                    $passport_image_data = $passport_split[1];
                    $passport_base64_decode = base64_decode($passport_image_data);
                    file_put_contents($path . $passport_pic_name, $passport_base64_decode);
                } else {
                    $passport_split = explode(',', substr($request->get('passport_upload_base_code'), 5), 2);
                    $passport_image_data = $passport_split[1];
                    $passport_base64_decode = base64_decode($passport_image_data);
                    file_put_contents($path . $passport_pic_name, $passport_base64_decode);
                }

                if ($amendment_type == 'existing') {
                    $amendment_director->passport_scan_copy = $passport_pic_name;
                } elseif ($amendment_type == 'proposed') {
                    $amendment_director->n_passport_scan_copy = $passport_pic_name;
                }
            }

            $amendment_director->amendment_type = $amendment_type == 'existing' ? 'no change' : 'add';
            $amendment_director->save();
            DB::commit();

            /*
             * destroy NID session data ...
             */
            if ($amendment_director->identity_type == 'nid') {
                Session::forget('nid_info');
            }

            /*
             * destroy ETIN session data ...
             */
            if ($amendment_director->identity_type == 'tin') {
                Session::forget('eTin_info');
            }

            return response()->json([
                'success' => true,
                'status' => 'Data has been saved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('BRAStoreVerifyDirector : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRASC-1005]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()) . '[BRASC-1005]'
            ]);
        }
    }

    public function directorEdit($director_id)
    {
        $amendment_type = [
            '' => 'Select One',
            'add' => 'Add',
            'edit' => 'Edit',
            'remove' => 'Remove',
//            'no change' => 'No Change',
        ];
        $director_by_id = ListOfDirectorsAmendment::find(Encryption::decodeId($director_id));
        $nationality = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->where('nationality', '!=', '')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();

        return view('BidaRegistrationAmendment::director.edit', compact('director_by_id', 'amendment_type', 'nationality'));
    }

    public function directorUpdate(Request $request)
    {
        try {

            DB::beginTransaction();

            $director_by_id = ListOfDirectorsAmendment::findOrFail(Encryption::decodeId($request->id));

            $director_by_id->l_director_name = !empty($request->get('l_director_name')) ? $request->get('l_director_name') : null;
            $director_by_id->date_of_birth = (!empty($request->get('date_of_birth')) ? date('Y-m-d', strtotime($request->get('date_of_birth'))) : null);
            $director_by_id->gender = !empty($request->get('gender')) ? $request->get('gender') : null;
            $director_by_id->l_director_designation = !empty($request->get('l_director_designation')) ? $request->get('l_director_designation') : null;
            $director_by_id->l_director_nationality = !empty($request->get('l_director_nationality')) ? $request->get('l_director_nationality') : null;
            $director_by_id->passport_type = !empty($request->get('passport_type')) ? $request->get('passport_type') : null;
            $director_by_id->nid_etin_passport = !empty($request->get('nid_etin_passport')) ? $request->get('nid_etin_passport') : null;
            $director_by_id->date_of_expiry = (!empty($request->get('date_of_expiry')) ? date('Y-m-d', strtotime($request->get('date_of_expiry'))) : null);

            $director_by_id->n_l_director_name = !empty($request->get('n_l_director_name')) ? $request->get('n_l_director_name') : null;
            $director_by_id->n_date_of_birth = (!empty($request->get('n_date_of_birth')) ? date('Y-m-d', strtotime($request->get('n_date_of_birth'))) : null);
            $director_by_id->n_gender = !empty($request->get('n_gender')) ? $request->get('gender') : null;
            $director_by_id->n_l_director_designation = !empty($request->get('n_l_director_designation')) ? $request->get('n_l_director_designation') : null;
            $director_by_id->n_l_director_nationality = !empty($request->get('n_l_director_nationality')) ? $request->get('n_l_director_nationality') : null;
            $director_by_id->n_passport_type = !empty($request->get('n_passport_type')) ? $request->get('n_passport_type') : null;
            $director_by_id->n_nid_etin_passport = !empty($request->get('n_nid_etin_passport')) ? $request->get('n_nid_etin_passport') : null;
            $director_by_id->n_date_of_expiry = (!empty($request->get('n_date_of_expiry')) ? date('Y-m-d', strtotime($request->get('n_date_of_expiry'))) : null);

            $director_by_id->amendment_type = !empty($request->get('amendment_type')) ? $request->get('amendment_type') : 'no change';

            $director_by_id->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 'Data has been updated successfully',
            ]);

        } catch (\Exception $e) {

            DB::rollback();
            return response()->json([
                'success' => false,
                'status' => 'Something went wrong!' . $e->getMessage(),
            ]);
        }
    }

    public function directorDelete($director_id)
    {
        try {
            ListOfDirectorsAmendment::where('id', Encryption::decodeId($director_id))->update([
                'amendment_type' => 'delete',
                'status' => 0
            ]);
            return response()->json([
                'responseCode' => 1,
                'msg' => 'Data has been deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'responseCode' => 0,
                'msg' => 'Something went wrong!' . $e->getMessage(),
            ]);
        }
    }
    //bra director manage end

    public function annualProductionCapacityForm($app_id)
    {
        $productUnit = ['0' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->orderBy('name')->lists('name', 'id')->all();
        $amendment_type = [
//            '' => 'Select One',
            'add' => 'Add',
//            'edit' => 'Edit',
//            'delete' => 'Delete',
//            'no change' => 'No Change',
        ];

        return view('BidaRegistrationAmendment::annual_production_capacity.create', compact('productUnit', 'amendment_type', 'app_id', 'apc_data'));
    }

    public function annualProductionCapacityStore(Request $request)
    {
        $app_id = Encryption::decodeId($request->app_id);

        if (!empty($app_id)) {
            foreach ($request->product_name as $proKey => $proData) {
                $annualCapacity = new AnnualProductionCapacityAmendment();
                $annualCapacity->app_id = $app_id;
                $annualCapacity->process_type_id = $this->process_type_id;

                $annualCapacity->product_name = !empty($request->product_name[$proKey]) ? $request->product_name[$proKey] : null;
                $annualCapacity->quantity_unit = !empty($request->quantity_unit[$proKey]) ? $request->quantity_unit[$proKey] : null;
                $annualCapacity->quantity = !empty($request->quantity[$proKey]) ? $request->quantity[$proKey] : null;
                $annualCapacity->price_usd = !empty($request->price_usd[$proKey]) ? $request->price_usd[$proKey] : null;
                $annualCapacity->price_taka = !empty($request->price_taka[$proKey]) ? $request->price_taka[$proKey] : null;

                $annualCapacity->n_product_name = !empty($request->n_product_name[$proKey]) ? $request->n_product_name[$proKey] : null;
                $annualCapacity->n_quantity_unit = !empty($request->n_quantity_unit[$proKey]) ? $request->n_quantity_unit[$proKey] : null;
                $annualCapacity->n_quantity = !empty($request->n_quantity[$proKey]) ? $request->n_quantity[$proKey] : null;
                $annualCapacity->n_price_usd = !empty($request->n_price_usd[$proKey]) ? $request->n_price_usd[$proKey] : null;
                $annualCapacity->n_price_taka = !empty($request->n_price_taka[$proKey]) ? $request->n_price_taka[$proKey] : null;

                $annualCapacity->amendment_type = (
                    empty($annualCapacity->n_product_name) &&
                    empty($annualCapacity->n_quantity_unit) &&
                    empty($annualCapacity->n_quantity) &&
                    empty($annualCapacity->n_price_usd) &&
                    empty($annualCapacity->n_price_taka)
                ) ? 'no change' : ((
                    empty($annualCapacity->product_name) &&
                    empty($annualCapacity->quantity_unit) &&
                    empty($annualCapacity->quantity) &&
                    empty($annualCapacity->price_usd) &&
                    empty($annualCapacity->price_taka)
                ) ? 'add' : 'edit');

                $annualCapacity->save();
            }
        }

        return response()->json([
            'success' => true,
            'status' => 'Data has been saved successfully',
        ]);
    }

    public function loadAannualProductionCapacityData(Request $request)
    {
        $app_id = Encryption::decodeId($request->app_id);
        $limit = $request->limit;
        $viewMode = $request->viewMode;

        DB::statement(DB::raw('set @rownum=0'));
        $query = AnnualProductionCapacityAmendment::leftJoin('product_unit', 'product_unit.id', '=', 'annual_production_capacity_amendment.quantity_unit')
            ->leftJoin('product_unit as proposed_unit', 'proposed_unit.id', '=', 'annual_production_capacity_amendment.n_quantity_unit')
            ->where('app_id', $app_id)
            ->where('process_type_id', $this->process_type_id)
            ->where('amendment_type', '!=', 'delete')
            ->orderBy('annual_production_capacity_amendment.id', 'ASC');

        if ($limit != 'all') {
            $query->limit(20);
        }

        $getData = $query->get([
            DB::raw('@rownum := @rownum+1 AS sl'),
            'product_unit.name as ex_unit_name',
            'proposed_unit.name as pro_unit_name',
            'annual_production_capacity_amendment.*',
        ]);

        $html = strval(view("BidaRegistrationAmendment::annual_production_capacity.load_apc_data", compact('getData', 'viewMode')));
        return response()->json(['responseCode' => 1, 'html' => $html]);
    }

    public function annualProductionCapacityUpdateForm($apc_id)
    {
        $apc_decoded_id = Encryption::decodeId($apc_id);
        $amendment_type = [
            '' => 'Select One',
            'add' => 'Add',
            'edit' => 'Edit',
            'remove' => 'Remove',
//            'no change' => 'No Change',
        ];
        $productUnit = ['0' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->orderBy('name')->lists('name', 'id')->all();
        $get_apc_data = AnnualProductionCapacityAmendment::find($apc_decoded_id);
        return view('BidaRegistrationAmendment::annual_production_capacity.edit', compact('get_apc_data', 'productUnit', 'amendment_type'));
    }

    public function annualProductionCapacityUpdate(Request $request)
    {
        $apcData = AnnualProductionCapacityAmendment::findOrFail($request->apc_id);
        $apcData->product_name = !empty($request->product_name) ? $request->product_name : null;
        $apcData->quantity_unit = !empty($request->quantity_unit) ? $request->quantity_unit : null;
        $apcData->quantity = !empty($request->quantity) ? $request->quantity : null;
        $apcData->price_usd = !empty($request->price_usd) ? $request->price_usd : null;
        $apcData->price_taka = !empty($request->price_taka) ? $request->price_taka : null;

        $apcData->n_product_name = !empty($request->n_product_name) ? $request->n_product_name : null;
        $apcData->n_quantity_unit = !empty($request->n_quantity_unit) ? $request->n_quantity_unit : null;
        $apcData->n_quantity = !empty($request->n_quantity) ? $request->n_quantity : null;
        $apcData->n_price_usd = !empty($request->n_price_usd) ? $request->n_price_usd : null;
        $apcData->n_price_taka = !empty($request->n_price_taka) ? $request->n_price_taka : null;

        $apcData->amendment_type = !empty($request->get('amendment_type')) ? $request->get('amendment_type') : 'no change';
        $apcData->save();

        return response()->json([
            'success' => true,
            'status' => 'Data has been update successfully',
        ]);

    }

    public function annualProductionCapacityDelete($apc_id)
    {
        $decoded_apc_id = Encryption::decodeId($apc_id);
        AnnualProductionCapacityAmendment::where('id', $decoded_apc_id)->where('process_type_id', $this->process_type_id)->update([
            'amendment_type' => 'delete',
            'status' => 0
        ]);

        return response()->json([
            'responseCode' => 1,
            'msg' => 'Data has been deleted successfully',
        ]);
    }

    /**
     * @param $app_id
     * @return \BladeView|bool|\Illuminate\View\View
     * open list of imported machinery form in the modal
     */
    public function importedMachineryForm($app_id)
    {
        try {
            $amendment_type = [
//                '' => 'Select One',
                'add' => 'Add',
//                'edit' => 'Edit',
//                'delete' => 'Delete',
//                'no change' => 'No Change',
            ];

            return view('BidaRegistrationAmendment::imported_machinery.create', compact('app_id', 'amendment_type'));
        } catch (\Exception $e) {
            abort(500, "Something went wrong - [MAC-1000]");
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|string
     * list of imported machinery data store
     */
    public function importedMachineryStore(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }
        
        try {
            $app_id = Encryption::decodeId($request->app_id);

            DB::beginTransaction();
            foreach ($request->l_machinery_imported_name as $key => $value) {
                $listOfMachineryImported = new ListOfMachineryImportedAmendment();
                
                $listOfMachineryImported->app_id = $app_id;
                $listOfMachineryImported->process_type_id = $this->process_type_id;

                $listOfMachineryImported->l_machinery_imported_name = !empty($request->l_machinery_imported_name[$key]) ? $request->l_machinery_imported_name[$key] : null;
                $listOfMachineryImported->l_machinery_imported_qty = !empty($request->l_machinery_imported_qty[$key]) ? $request->l_machinery_imported_qty[$key] : null;
                $listOfMachineryImported->l_machinery_imported_unit_price = !empty($request->l_machinery_imported_unit_price[$key]) ? $request->l_machinery_imported_unit_price[$key] : null;
                $listOfMachineryImported->l_machinery_imported_total_value = !empty($request->l_machinery_imported_total_value[$key]) ? $request->l_machinery_imported_total_value[$key] : null;

                $listOfMachineryImported->n_l_machinery_imported_name = !empty($request->n_l_machinery_imported_name[$key]) ? $request->n_l_machinery_imported_name[$key] : null;
                $listOfMachineryImported->n_l_machinery_imported_qty = !empty($request->n_l_machinery_imported_qty[$key]) ? $request->n_l_machinery_imported_qty[$key] : null;
                $listOfMachineryImported->n_l_machinery_imported_unit_price = !empty($request->n_l_machinery_imported_unit_price[$key]) ? $request->n_l_machinery_imported_unit_price[$key] : null;
                $listOfMachineryImported->n_l_machinery_imported_total_value = !empty($request->n_l_machinery_imported_total_value[$key]) ? $request->n_l_machinery_imported_total_value[$key] : null;

                $listOfMachineryImported->total_million = !empty($request->n_l_machinery_imported_total_value[$key]) ? $request->n_l_machinery_imported_total_value[$key] : $request->l_machinery_imported_total_value[$key];

                $listOfMachineryImported->amendment_type = (
                    empty($listOfMachineryImported->n_l_machinery_imported_name) &&
                    empty($listOfMachineryImported->n_l_machinery_imported_qty) &&
                    empty($listOfMachineryImported->n_l_machinery_imported_unit_price) &&
                    empty($listOfMachineryImported->n_l_machinery_imported_total_value)
                ) ? 'no change' : ((
                    empty($listOfMachineryImported->l_machinery_imported_name) &&
                    empty($listOfMachineryImported->l_machinery_imported_qty) &&
                    empty($listOfMachineryImported->l_machinery_imported_unit_price) &&
                    empty($listOfMachineryImported->l_machinery_imported_total_value)
                ) ? 'add' : 'edit');

                $listOfMachineryImported->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 'Data has been saved successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => true,
                'status' => 'Data has been saved not successfully-[MAC-1001]',
            ]);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|string
     * Load list of imported machinery data when page loaded
     */
    public function loadImportedMachineryData(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }

        try {
            $app_id = Encryption::decodeId($request->app_id);
            $viewMode = $request->viewMode;
            $importedMachineryData = self::getListOfImportedMachineryData($app_id, $this->process_type_id, $request->limit, $viewMode);

            $html = strval(view("BidaRegistrationAmendment::imported_machinery.load_imported_machinery", compact('importedMachineryData', 'viewMode')));
            return response()->json(['responseCode' => 1, 'html' => $html]);
        } catch (\Exception $e) {
            return response()->json(['responseCode' => 0, 'msg' => "Something went wrong -[MAC-1002]"]);
        }

    }

    /**
     * @param $im_id
     * @return \BladeView|bool|\Illuminate\View\View
     * Open imported machinery edit modal
     */
    public function importedMachineryEditForm($im_id)
    {
        try {
            $decoded_id = $app_id = Encryption::decodeId($im_id);
            $amendment_type = [
                '' => 'Select One',
                'add' => 'Add',
                'edit' => 'Edit',
//                'delete' => 'Delete',
                'remove' => 'Remove',
            ];
            $getImportedMachinery = ListOfMachineryImportedAmendment::where(['id' => $decoded_id, 'process_type_id' => $this->process_type_id])->first();

            return view('BidaRegistrationAmendment::imported_machinery.edit', compact('getImportedMachinery', 'amendment_type'));
        } catch (\Exception $e) {
            abort(500, "Something went wrong - [MAC-1003]");
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|string
     * imported machinery data update
     */
    public function importedMachineryUpdate(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }
        try {
            DB::beginTransaction();
            $refAppTrackingNo = trim($request->get('ref_app_tracking_no'));

            $listOfMachineryImported = ListOfMachineryImportedAmendment::find($request->im_id);
            
            $ref_app_tracking_no = BidaRegistrationAmendment::where('id', $listOfMachineryImported->app_id)->value('ref_app_tracking_no');
            
            $listOfMachineryImportedMaster = MasterMachineryImported::where('ref_app_tracking_no', $ref_app_tracking_no)
                ->whereNotIn('amendment_type', ['delete', 'remove'])
                ->where('total_imported', '>', 0)
                ->where('status', 1)
                ->select('name', 'total_imported')
                ->get();

            if(count($listOfMachineryImportedMaster) > 0 && !empty($request->l_machinery_imported_qty) || !empty($request->n_l_machinery_imported_qty)) {
                foreach ($listOfMachineryImportedMaster as $machineryImportedMaster) {
                    if (MasterMachineryImported::processName($machineryImportedMaster->name) == MasterMachineryImported::processName($listOfMachineryImported->l_machinery_imported_name)) {                        
                        $request_imported_qty = !empty($request->n_l_machinery_imported_qty) ? $request->n_l_machinery_imported_qty : $request->l_machinery_imported_qty;
                        if ($request_imported_qty < $machineryImportedMaster->total_imported) {
                            return response()->json([
                                'error' => true,
                                'status' => 'Bida Reg Amendment machinery quantity cannot be less than the already imported machinery quantity!',
                            ]);
                        }
                    }
                }
            }

            $listOfMachineryImported->l_machinery_imported_name = $request->l_machinery_imported_name;
            $listOfMachineryImported->l_machinery_imported_name = !empty($request->l_machinery_imported_name) ? $request->l_machinery_imported_name : null;
            $listOfMachineryImported->l_machinery_imported_qty = !empty($request->l_machinery_imported_qty) ? $request->l_machinery_imported_qty : null;
            $listOfMachineryImported->l_machinery_imported_unit_price = !empty($request->l_machinery_imported_unit_price) ? $request->l_machinery_imported_unit_price : null;
            $listOfMachineryImported->l_machinery_imported_total_value = !empty($request->l_machinery_imported_total_value) ? $request->l_machinery_imported_total_value : null;

            $listOfMachineryImported->n_l_machinery_imported_name = !empty($request->n_l_machinery_imported_name) ? $request->n_l_machinery_imported_name : null;
            $listOfMachineryImported->n_l_machinery_imported_qty = !empty($request->n_l_machinery_imported_qty) ? $request->n_l_machinery_imported_qty : null;
            $listOfMachineryImported->n_l_machinery_imported_unit_price = !empty($request->n_l_machinery_imported_unit_price) ? $request->n_l_machinery_imported_unit_price : null;
            $listOfMachineryImported->n_l_machinery_imported_total_value = !empty($request->n_l_machinery_imported_total_value) ? $request->n_l_machinery_imported_total_value : null;
            
            $listOfMachineryImported->amendment_type = !empty($request->get('amendment_type')) ? $request->get('amendment_type') : 'no change';
            $listOfMachineryImported->total_million = !empty($request->n_l_machinery_imported_total_value) ? $request->n_l_machinery_imported_total_value : $request->l_machinery_imported_total_value;
            $listOfMachineryImported->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 'Data has been update successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => true,
                'status' => 'Data has not been update successfully. [MAC-1004]',
            ]);
        }
    }


    // public function importedMachineryDelete($im_id)
    // {
    //     $decoded_id = Encryption::decodeId($im_id);
    //     ListOfMachineryImportedAmendment::where(['id' => $decoded_id, 'process_type_id' => $this->process_type_id])->update([
    //         'amendment_type' => 'delete',
    //         'status' => 0
    //     ]);

    //     return response()->json([
    //         'responseCode' => 1,
    //         'msg' => 'Data has been deleted successfully',
    //     ]);
    // }


    public function importedMachineryDelete($im_id)
    {
        $decoded_id = Encryption::decodeId($im_id);
        
        $delete_machinery = ListOfMachineryImportedAmendment::where(['id' => $decoded_id, 'process_type_id' => $this->process_type_id])->first();
        
        $ref_app_tracking_no = BidaRegistrationAmendment::where('id', $delete_machinery->app_id)->value('ref_app_tracking_no');

        $listOfMachineryImportedMaster = MasterMachineryImported::where('ref_app_tracking_no', $ref_app_tracking_no)
            ->whereNotIn('amendment_type', ['delete', 'remove'])
            ->where('total_imported', '>', 0)
            ->select('name')
            ->get();

        if(count($listOfMachineryImportedMaster) > 0) {
            foreach ($listOfMachineryImportedMaster as $machineryImportedMaster) {
                if (MasterMachineryImported::processName($machineryImportedMaster->name) == MasterMachineryImported::processName($delete_machinery->l_machinery_imported_name)) {
                    return response()->json([
                        'responseCode' => 0,
                        'msg' => 'Already imported machinery cannot be deleted.',
                    ]);
                }
            }
        }
        

        // Update ListOfMachineryImportedAmendment data
        ListOfMachineryImportedAmendment::where(['id' => $decoded_id, 'process_type_id' => $this->process_type_id])->update([
            'amendment_type' => 'delete',
            'status' => 0
        ]);

        return response()->json([
            'responseCode' => 1,
            'msg' => 'Data has been deleted successfully',
        ]);
    }

    public function importedMachineryBatchDelete(Request $request){

        $ids = $request->input('ids');
        $decodedIds = array_map('Encryption::decodeId', $ids);

        $delete_machinery = ListOfMachineryImportedAmendment::whereIn('id', $decodedIds)->where('process_type_id', $this->process_type_id)->get();

        foreach($delete_machinery as $machinery){
            $ref_app_tracking_no = BidaRegistrationAmendment::where('id', $machinery->app_id)->value('ref_app_tracking_no');

            $listOfMachineryImportedMaster = MasterMachineryImported::where('ref_app_tracking_no', $ref_app_tracking_no)
                ->whereNotIn('amendment_type', ['delete', 'remove'])
                ->where('total_imported', '>', 0)
                ->select('name')
                ->get();

            if(count($listOfMachineryImportedMaster) > 0) {
                foreach ($listOfMachineryImportedMaster as $machineryImportedMaster) {
                    if (MasterMachineryImported::processName($machineryImportedMaster->name) == MasterMachineryImported::processName($machinery->l_machinery_imported_name)) {
                        return response()->json([
                            'responseCode' => 0,
                            'msg' => 'Already imported machinery cannot be deleted.',
                        ]);
                    }
                }
            }
        }
        
        // Update ListOfMachineryImportedAmendment data
        ListOfMachineryImportedAmendment::whereIn('id', $decodedIds)->where('process_type_id', $this->process_type_id)->update([
            'amendment_type' => 'delete',
            'status' => 0
        ]);

        return response()->json([
            'responseCode' => 1,
            'msg' => 'Data has been deleted successfully',
        ]);

    }

    /**
     * @param $app_id
     * @return \BladeView|bool|\Illuminate\View\View
     * open local machinery form modal
     */
    public function localMachineryForm($app_id)
    {
        try {
            $amendment_type = [
//                '' => 'Select One',
                'add' => 'Add',
//                'edit' => 'Edit',
//                'delete' => 'Delete',
//                'no change' => 'No Change',
            ];

            return view('BidaRegistrationAmendment::local_machinery.create', compact('app_id', 'amendment_type'));
        } catch (Exception $e) {
            abort(500, "Something went wrong - [MAC-1005]");
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|string
     * Load list of local machinery data when page loaded
     */
    public function loadLocalMachineryData(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }
        try {
            $app_id = Encryption::decodeId($request->app_id);
            $viewMode = $request->viewMode;
            $localMachineryData = self::getListOfLocalMachineryData($app_id, $this->process_type_id, $request->limit, $viewMode);

            $html = strval(view("BidaRegistrationAmendment::local_machinery.load_local_machinery", compact('localMachineryData', 'viewMode')));
            return response()->json(['responseCode' => 1, 'html' => $html]);
        } catch (\Exception $e) {
            return response()->json(['responseCode' => 0, 'msg' => "Something went wrong -[MAC-1006]"]);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * Local machinery data store
     */

    public function localMachineryStore(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }
        try {
            $app_id = Encryption::decodeId($request->app_id);

            DB::beginTransaction();
            foreach ($request->l_machinery_local_name as $key => $value) {
                $listOfMachineryLocal = new ListOfMachineryLocalAmendment();
                $listOfMachineryLocal->app_id = $app_id;
                $listOfMachineryLocal->process_type_id = $this->process_type_id;

                $listOfMachineryLocal->l_machinery_local_name = !empty($request->l_machinery_local_name[$key]) ? $request->l_machinery_local_name[$key] : null;
                $listOfMachineryLocal->l_machinery_local_qty = !empty($request->l_machinery_local_qty[$key]) ? $request->l_machinery_local_qty[$key] : null;
                $listOfMachineryLocal->l_machinery_local_unit_price = !empty($request->l_machinery_local_unit_price[$key]) ? $request->l_machinery_local_unit_price[$key] : null;
                $listOfMachineryLocal->l_machinery_local_total_value = !empty($request->l_machinery_local_total_value[$key]) ? $request->l_machinery_local_total_value[$key] : null;

                $listOfMachineryLocal->n_l_machinery_local_name = !empty($request->n_l_machinery_local_name[$key]) ? $request->n_l_machinery_local_name[$key] : null;
                $listOfMachineryLocal->n_l_machinery_local_qty = !empty($request->n_l_machinery_local_qty[$key]) ? $request->n_l_machinery_local_qty[$key] : null;
                $listOfMachineryLocal->n_l_machinery_local_unit_price = !empty($request->n_l_machinery_local_unit_price[$key]) ? $request->n_l_machinery_local_unit_price[$key] : null;
                $listOfMachineryLocal->n_l_machinery_local_total_value = !empty($request->n_l_machinery_local_total_value[$key]) ? $request->n_l_machinery_local_total_value[$key] : null;

                $listOfMachineryLocal->total_million = !empty($request->n_l_machinery_local_total_value[$key]) ? $request->n_l_machinery_local_total_value[$key] : $request->l_machinery_local_total_value[$key];

                $listOfMachineryLocal->amendment_type = (
                    empty($listOfMachineryLocal->n_l_machinery_local_name) &&
                    empty($listOfMachineryLocal->n_l_machinery_local_qty) &&
                    empty($listOfMachineryLocal->n_l_machinery_local_unit_price) &&
                    empty($listOfMachineryLocal->n_l_machinery_local_total_value)
                ) ? 'no change' : ((
                    empty($listOfMachineryLocal->l_machinery_local_name) &&
                    empty($listOfMachineryLocal->l_machinery_local_qty) &&
                    empty($listOfMachineryLocal->l_machinery_local_unit_price) &&
                    empty($listOfMachineryLocal->l_machinery_local_total_value)
                ) ? 'add' : 'edit');

                $listOfMachineryLocal->save();
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 'Data has been saved successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => true,
                'status' => 'Data has been saved not successfully-[MAC-1001]',
            ]);
        }
    }

    /**
     * @param $lm_id
     * @return \BladeView|bool|\Illuminate\View\View
     * Open local machinery edit form in modal
     */
    public function localMachineryEditForm($lm_id)
    {
        try {
            $decoded_id = $app_id = Encryption::decodeId($lm_id);
            $amendment_type = [
                '' => 'Select One',
                'add' => 'Add',
                'edit' => 'Edit',
                'remove' => 'Remove',
//                'no change' => 'No Change',
            ];
            $getlocalMachinery = ListOfMachineryLocalAmendment::where(['id' => $decoded_id, 'process_type_id' => $this->process_type_id])->first();

            return view('BidaRegistrationAmendment::local_machinery.edit', compact('getlocalMachinery', 'amendment_type'));
        } catch (\Exception $e) {
            abort(500, "Something went wrong - [MAC-1008]");
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * Local machinery data update
     */
    public function localMachineryUpdate(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }

        try {
            DB::beginTransaction();
            $listOfMachineryLocal = ListOfMachineryLocalAmendment::find($request->lm_id);


            $listOfMachineryLocal->l_machinery_local_name = !empty($request->l_machinery_local_name) ? $request->l_machinery_local_name : null;
            $listOfMachineryLocal->l_machinery_local_qty = !empty($request->l_machinery_local_qty) ? $request->l_machinery_local_qty : null;
            $listOfMachineryLocal->l_machinery_local_unit_price = !empty($request->l_machinery_local_unit_price) ? $request->l_machinery_local_unit_price : null;
            $listOfMachineryLocal->l_machinery_local_total_value = !empty($request->l_machinery_local_total_value) ? $request->l_machinery_local_total_value : null;

            $listOfMachineryLocal->n_l_machinery_local_name = !empty($request->n_l_machinery_local_name) ? $request->n_l_machinery_local_name : null;
            $listOfMachineryLocal->n_l_machinery_local_qty = !empty($request->n_l_machinery_local_qty) ? $request->n_l_machinery_local_qty : null;
            $listOfMachineryLocal->n_l_machinery_local_unit_price = !empty($request->n_l_machinery_local_unit_price) ? $request->n_l_machinery_local_unit_price : null;
            $listOfMachineryLocal->n_l_machinery_local_total_value = !empty($request->n_l_machinery_local_total_value) ? $request->n_l_machinery_local_total_value : null;

            // $listOfMachineryLocal->l_machinery_local_name = $request->l_machinery_local_name;
            // $listOfMachineryLocal->l_machinery_local_qty = $request->l_machinery_local_qty;
            // $listOfMachineryLocal->l_machinery_local_unit_price = $request->l_machinery_local_unit_price;
            // $listOfMachineryLocal->l_machinery_local_total_value = $request->l_machinery_local_total_value;

            // $listOfMachineryLocal->n_l_machinery_local_name = $request->n_l_machinery_local_name;
            // $listOfMachineryLocal->n_l_machinery_local_qty = $request->n_l_machinery_local_qty;
            // $listOfMachineryLocal->n_l_machinery_local_unit_price = $request->n_l_machinery_local_unit_price;
            // $listOfMachineryLocal->n_l_machinery_local_total_value = $request->n_l_machinery_local_total_value;


            $listOfMachineryLocal->amendment_type = !empty($request->get('amendment_type')) ? $request->get('amendment_type') : 'no change';
            $listOfMachineryLocal->total_million = !empty($request->n_l_machinery_local_total_value) ? $request->n_l_machinery_local_total_value : $request->l_machinery_local_total_value;
            $listOfMachineryLocal->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 'Data has been update successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => true,
                'status' => 'Data has not been update successfully. [MAC-1009]',
            ]);
        }
    }

    public function localMachineryDelete($lm_id)
    {
        $decoded_id = Encryption::decodeId($lm_id);
        ListOfMachineryLocalAmendment::where(['id' => $decoded_id, 'process_type_id' => $this->process_type_id])->update([
            'amendment_type' => 'delete',
            'status' => 0
        ]);

        return response()->json([
            'responseCode' => 1,
            'msg' => 'Data has been deleted successfully',
        ]);
    }

    public function localMachineryBatchDelete(Request $request){

        $ids = array_map('Encryption::decodeId', $request->ids);
        ListOfMachineryLocalAmendment::whereIn('id', $ids)->where('process_type_id', $this->process_type_id)->update([
            'amendment_type' => 'delete',
            'status' => 0
        ]);
        return response()->json([
            'responseCode' => 1,
            'msg' => 'Data has been deleted successfully',
        ]);

    }

    /**
     * @param $app_id
     * @param $process_type_id
     * @return array
     * fetch imported machinery data with limit
     */
    public static function getListOfImportedMachineryData($app_id, $process_type_id, $limit, $viewMode)
    {
        $importedMachineryData = []; //getData
        DB::statement(DB::raw('set @rownum=0'));
        $query = ListOfMachineryImportedAmendment::where('app_id', $app_id)
            ->where('process_type_id', $process_type_id)
            ->where('amendment_type', '!=', 'delete')
            ->orderBy('id', 'ASC');

        if ($limit != "all") {
            $query->limit($limit);
        }

        $importedMachineryData['getData'] = $query->get([
            DB::raw('@rownum := @rownum+1 AS sl'),
            'list_of_machinery_imported_amendment.id',
            'list_of_machinery_imported_amendment.l_machinery_imported_name',
            'list_of_machinery_imported_amendment.l_machinery_imported_qty',
            'list_of_machinery_imported_amendment.l_machinery_imported_unit_price',
            'list_of_machinery_imported_amendment.l_machinery_imported_total_value',

            'list_of_machinery_imported_amendment.n_l_machinery_imported_name',
            'list_of_machinery_imported_amendment.n_l_machinery_imported_qty',
            'list_of_machinery_imported_amendment.n_l_machinery_imported_unit_price',
            'list_of_machinery_imported_amendment.n_l_machinery_imported_total_value',
            'list_of_machinery_imported_amendment.amendment_type'
        ]);

        $importedMachineryData['ex_imported_machinery_total'] = ListOfMachineryImportedAmendment::where('app_id', $app_id)
            ->where('process_type_id', $process_type_id)
            ->whereNotIn('amendment_type', ['delete'])
            // ->whereNotIn('amendment_type', ['delete', 'remove'])
            ->sum('l_machinery_imported_total_value');
        $importedMachineryData['pro_imported_machinery_total'] = ListOfMachineryImportedAmendment::where('app_id', $app_id)
            ->where('process_type_id', $process_type_id)
            ->whereNotIn('amendment_type', ['delete', 'remove'])
            ->sum('n_l_machinery_imported_total_value');
        $importedMachineryData['grand_total'] = ListOfMachineryImportedAmendment::where('app_id', $app_id)
            ->where('process_type_id', $process_type_id)
            ->whereNotIn('amendment_type', ['delete', 'remove'])
            ->sum('total_million');

        return $importedMachineryData;
    }

    /**
     * @param $app_id
     * @param $process_type_id
     * @return array
     * fetch local machinery data with limit
     */
    public static function getListOfLocalMachineryData($app_id, $process_type_id, $limit, $viewMode)
    {
        $localMachineryData = [];
        DB::statement(DB::raw('set @rownum=0'));
        $query = ListOfMachineryLocalAmendment::where('app_id', $app_id)
            ->where('process_type_id', $process_type_id)
            ->where('amendment_type', '!=', 'delete')
            ->orderBy('id', 'ASC');

        if ($limit != "all") {
            $query->limit($limit);
        }

        $localMachineryData['getData'] = $query->get([
            DB::raw('@rownum := @rownum+1 AS sl'),
            'list_of_machinery_local_amendment.id',
            'list_of_machinery_local_amendment.l_machinery_local_name',
            'list_of_machinery_local_amendment.l_machinery_local_qty',
            'list_of_machinery_local_amendment.l_machinery_local_unit_price',
            'list_of_machinery_local_amendment.l_machinery_local_total_value',

            'list_of_machinery_local_amendment.n_l_machinery_local_name',
            'list_of_machinery_local_amendment.n_l_machinery_local_qty',
            'list_of_machinery_local_amendment.n_l_machinery_local_unit_price',
            'list_of_machinery_local_amendment.n_l_machinery_local_total_value',
            'list_of_machinery_local_amendment.amendment_type'
        ]);

        $localMachineryData['ex_local_machinery_total'] = ListOfMachineryLocalAmendment::where('app_id', $app_id)
            ->where('process_type_id', $process_type_id)
            // ->whereNotIn('amendment_type', ['delete', 'remove'])
            ->whereNotIn('amendment_type', ['delete'])
            ->sum('l_machinery_local_total_value');

        $localMachineryData['pro_local_machinery_total'] = ListOfMachineryLocalAmendment::where('app_id', $app_id)
            ->where('process_type_id', $process_type_id)
            ->whereNotIn('amendment_type', ['delete', 'remove'])
            ->sum('n_l_machinery_local_total_value');

        $localMachineryData['grand_total'] = ListOfMachineryLocalAmendment::where('app_id', $app_id)
            ->where('process_type_id', $process_type_id)
            ->whereNotIn('amendment_type', ['delete', 'remove'])
            ->sum('total_million');

        return $localMachineryData;
    }

    function countDirector(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-10102]';
        }

        $app_id = Encryption::decodeId($request->application_id);
        $process_type_id = Encryption::decodeId($request->process_type_id);

        $total_list_of_dirctors = ListOfDirectorsAmendment::where('app_id', $app_id)
            ->where('process_type_id', $process_type_id)
            ->count();

        return response()->json([
            'total_director' => $total_list_of_dirctors,
            'success' => 200
        ]);
    }

}
