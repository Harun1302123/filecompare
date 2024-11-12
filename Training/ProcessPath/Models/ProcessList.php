<?php

namespace App\Modules\ProcessPath\Models;

use App\Libraries\CommonFunction;
use App\Libraries\UtilFunction;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ProcessList extends Model
{
    protected $table = 'process_list';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($post) {
            $post->created_by = CommonFunction::getUserId();
            $post->updated_by = CommonFunction::getUserId();
        });

        static::updating(function ($post) {
            $isVerifyComplete = Session::get('payment.verifyComplete');
            if ($isVerifyComplete) {
                $post->updated_by = Session::get('payment.updated_by');
                Session::forget('payment.verifyComplete');
                Session::forget('payment.updated_by');
            } else {
                $post->updated_by = CommonFunction::getUserId();
            }
        });
    }

    public static function getApplicationList($process_type_id = 0, $request, $desk)
    {
        $userType = CommonFunction::getUserType();
        $working_company_id = CommonFunction::getUserWorkingCompany();
        $userDeskIds = CommonFunction::getUserDeskIds();
        $user_id = CommonFunction::getUserId();
        $userDepartmentIds = CommonFunction::getUserDepartmentIds();
        $userSubDepartmentIds = CommonFunction::getUserSubDepartmentIds();
        $divisionIds = UtilFunction::getUserDivisionIds();
        $delegatedUserDeskDepartmentIds = CommonFunction::getDelegatedUserDeskDepartmentIds();

        $query = ProcessList::leftJoin('user_desk', 'process_list.desk_id', '=', 'user_desk.id')
            ->leftjoin('process_status', function ($on) {
                $on->on('process_list.status_id', '=', 'process_status.id')
                    ->on('process_list.process_type_id', '=', 'process_status.process_type_id', 'and');
            })
            ->leftJoin('process_type', 'process_list.process_type_id', '=', 'process_type.id')
            ->leftJoin('company_info', 'process_list.company_id', '=', 'company_info.id')
            ->leftJoin('users', 'process_list.user_id', '=', 'users.id');

        // System admin can only view the application without Draft and and Waiting for Payment Confirmation status
        if ($userType == '1x101' || $userType == '2x202') {

            if ($request->get('search_type') == false) {
                $query->whereNotIn('process_list.status_id', [-1, 3]);
            }

            // if the process type is IRC then the officer will view only application those are in his desk
//            if ($process_type_id and $process_type_id == 13) {
//                $query->where('process_list.user_id', $user_id);
//            }
        } // General users can only view the applications related to their company
        elseif ($userType == '5x505') {
            $query->where('process_list.company_id', $working_company_id);

            if (!empty($request->is_feedback_row)) {
                $query->where('process_list.status_id', 25); // completed status all application for feedback system
            }
        }
        // Desk User can only view the applications related to their desk and department
        // and status id is not Draft or Shortfall
        else {

            //stack holder user list
            if (in_array($userType, ['9x901', '9x902', '9x903', '9x904'])) {
                if ($desk === 'my-desk') {
                    //Condition applied for my-desk data only
                    $query->where(function ($query1) use ($userDeskIds, $user_id) {
                        $query1->whereIn('process_list.desk_id', $userDeskIds)
                            ->where(function ($query2) use ($user_id) {
                                $query2->where('process_list.user_id', $user_id)
                                    ->orWhere('process_list.user_id', 0);
                            })
                            ->where('process_list.desk_id', '!=', 0)
                            ->whereNotIn('process_list.status_id', [-1, 19]);
                    });
                }
                
            } 
            // elseif ($userType == '4x404') {
            elseif (in_array($userType, ['4x404', '6x606'])) {
                //Condition applied for my-desk data only
                if ($desk === 'my-desk') {
                    $query->where(function ($query1) use ($user_id, $userDeskIds, $userDepartmentIds, $userSubDepartmentIds, $divisionIds) {
                        $query1->where(function ($query2) use ($user_id, $userDeskIds, $userDepartmentIds, $userSubDepartmentIds, $divisionIds) {
                            $query2->whereIn('process_list.desk_id', $userDeskIds)
                                ->where(function ($query2) use ($userDepartmentIds) {
                                    $query2->whereIn('process_list.department_id', $userDepartmentIds)
                                        ->orWhere('process_list.department_id', 0);
                                })
                                ->where(function ($query2) use ($userSubDepartmentIds) {
                                    $query2->whereIn('process_list.sub_department_id', $userSubDepartmentIds)
                                        ->orWhere('process_list.sub_department_id', 1);
                                })
                                ->where(function ($query2) use ($divisionIds) {
                                    $query2->whereIn('process_list.approval_center_id', $divisionIds)
                                        ->orWhere('process_list.approval_center_id', 0);
                                })
                                ->where(function ($query2) use ($user_id) {
                                    $query2->where('process_list.user_id', $user_id)
                                        ->orWhere('process_list.user_id', 0);
                                });
                        })
                            ->orWhere('process_list.user_id', $user_id);
                    });

                    $query->whereNotIn('process_list.status_id', [-1, 19]);
                } //Condition applied for my-delegated desk data only
                else if ($desk === 'my-delg-desk') {
                    $query->where(function ($query) use ($delegatedUserDeskDepartmentIds) {
                        if (empty($delegatedUserDeskDepartmentIds)) {
                            $query->where('process_list.desk_id', 555555);
                        } else {
                            $i = 0;
                            foreach ($delegatedUserDeskDepartmentIds as $data) {
                                $queryInc = '$query' . $i;

                                if ($i == 0) {
                                    $query->where(function ($query1) use ($data) {
                                        $query1->where(function ($queryInc) use ($data) {
                                            $queryInc->whereIn('process_list.desk_id', $data['desk_ids'])
                                                ->where(function ($query3) use ($data) {
                                                    $query3->whereIn('process_list.department_id', $data['department_ids'])
                                                        ->orWhere('process_list.department_id', 0);
                                                })
                                                ->where(function ($query2) use ($data) {
                                                    $query2->whereIn('process_list.sub_department_id', $data['sub_department_ids'])
                                                        ->orWhere('process_list.sub_department_id', 1);
                                                })
                                                ->where(function ($query2) use ($data) {
                                                    $query2->whereIn('process_list.approval_center_id', $data['division_ids'])
                                                        ->orWhere('process_list.approval_center_id', 0);
                                                })
                                                ->where(function ($query3) use ($data) {
                                                    $query3->where('process_list.user_id', $data['user_id'])
                                                        ->orWhere('process_list.user_id', 0);
                                                });
                                        })
                                            ->orWhere('process_list.user_id', $data['user_id']);
                                    });
                                } else {
                                    $query->orWhere(function ($query1) use ($data) {
                                        $query1->where(function ($queryInc) use ($data) {
                                            $queryInc->whereIn('process_list.desk_id', $data['desk_ids'])
                                                ->where(function ($query3) use ($data) {
                                                    $query3->whereIn('process_list.department_id', $data['department_ids'])
                                                        ->orWhere('process_list.department_id', 0);
                                                })
                                                ->where(function ($query2) use ($data) {
                                                    $query2->whereIn('process_list.sub_department_id', $data['sub_department_ids'])
                                                        ->orWhere('process_list.sub_department_id', 1);
                                                })
                                                ->where(function ($query2) use ($data) {
                                                    $query2->whereIn('process_list.approval_center_id', $data['division_ids'])
                                                        ->orWhere('process_list.approval_center_id', 0);
                                                })
                                                ->where(function ($query3) use ($data) {
                                                    $query3->where('process_list.user_id', $data['user_id'])
                                                        ->orWhere('process_list.user_id', 0);
                                                });
                                        })
                                            ->orWhere('process_list.user_id', $data['user_id']);
                                    });
                                }
                                $i++;
                            }
                        }
                    })
                        ->whereNotIn('process_list.status_id', [-1]);
                }
            }
        }

        if ($desk === 'favorite_list') {
            $query->Join('process_favorite_list', 'process_list.id', '=', 'process_favorite_list.process_id')
                ->where('process_favorite_list.user_id', $user_id);
        }

        // work for search parameter
        if ($request->has('process_search')) {
            $query->search($request); //calling of scopeSearch function
        } else {
            if ($process_type_id) {
                $query->where('process_list.process_type_id', $process_type_id);
            }
            $from = Carbon::now();
            $to = Carbon::now();
            // applicant 2 years and other desk users 3 months of data will be shown by default
            $previous_month = ($userType == '5x505' ? 24 : 3);
            $from->subMonths($previous_month);
            $query->whereBetween('process_list.updated_at', [$from, $to]);
        }

        if ($request->get('is_feedback') === 'feedback-list') {
            $query->where('process_list.is_feedback', 0); //pending feedback
            $query->where('process_list.status_id', '!=', -1);
        }

        if ($request->get('given_feedback') === 'given-feedback') {
            $query->where('process_list.is_feedback', 1);
        }

        if (!$request->has('order')) {
            $query->orderBy('process_list.created_at', 'DESC');
        }

        return $query->select([
            'process_list.id',
            'process_list.company_id',
            'process_list.ref_id',
            'process_list.tracking_no',
            'process_list.json_object',
            'process_list.desk_id',
            'process_list.process_type_id',
            'process_list.status_id',
            'process_list.updated_by',
            //'process_list.locked_by',
            //'process_list.locked_at',
            'process_list.created_by',
            //'process_list.read_status',
            'user_desk.desk_name',
            'process_type.name as process_name',
            'process_type.form_url',
            'process_type.form_id',
            'process_list.user_id',
            'users.user_first_name',
            'users.user_last_name',
            'company_info.company_name',
            DB::raw("CONCAT(process_status.status_name,'<br/>',process_list.updated_at) as status_name_updated_time"),
        ]);
    }

    public static function getStatusWiseApplication($process_type_id = 0, $status = '')
    {
        $userType = CommonFunction::getUserType();
        $working_company_id = CommonFunction::getUserWorkingCompany();
        $userDepartmentIds = CommonFunction::getUserDepartmentIds();
        $userSubDepartmentIds = CommonFunction::getUserSubDepartmentIds();
        $divisionIds = UtilFunction::getUserDivisionIds();

        $query = ProcessList::leftJoin('user_desk', 'process_list.desk_id', '=', 'user_desk.id')
            ->leftjoin('process_status', function ($on) {
                $on->on('process_list.status_id', '=', 'process_status.id')
                    ->on('process_list.process_type_id', '=', 'process_status.process_type_id', 'and');
            })
            ->leftJoin('process_type', 'process_list.process_type_id', '=', 'process_type.id')
            ->leftJoin('company_info', 'process_list.company_id', '=', 'company_info.id')
            ->leftJoin('users', 'process_list.user_id', '=', 'users.id')
            ->where('process_list.process_type_id', $process_type_id);

        // System admin can only view the application without Draft and Waiting for Payment Confirmation status
        if ($userType == '1x101' || $userType == '2x202') {
            $query->whereNotIn('process_list.status_id', [-1, 3]);
        } // General users can only view the applications related to their company
        elseif ($userType == '5x505' || $userType == '13x303') {
            $query->where('process_list.company_id', $working_company_id);
        }
        // Desk User can only view the applications related to their desk and department
        // and status id is not Draft or Shortfall
        else {
            $query->where(function ($query1) use ($userDepartmentIds, $userSubDepartmentIds, $divisionIds) {
                $query1->where(function ($query2) use ($userDepartmentIds) {
                    $query2->whereIn('process_list.department_id', $userDepartmentIds)
                        ->orWhere('process_list.department_id', 0);
                })
                    ->where(function ($query2) use ($userSubDepartmentIds) {
                        $query2->whereIn('process_list.sub_department_id', $userSubDepartmentIds)
                            ->orWhere('process_list.sub_department_id', 1);
                    })
                    ->where(function ($query2) use ($divisionIds) {
                        $query2->whereIn('process_list.approval_center_id', $divisionIds)
                            ->orWhere('process_list.approval_center_id', 0);
                    })
                    ->whereNotIn('process_list.status_id', [-1, 3]);
            });
        }

        if (!empty($status)) {
            $query->where('process_list.status_id', $status);
        }

        return $query->orderBy('process_list.created_at', 'desc')
            ->get([
                'process_list.id',
                'process_list.company_id',
                'process_list.ref_id',
                'process_list.tracking_no',
                'process_list.json_object',
                'process_list.desk_id',
                'process_list.process_type_id',
                'process_list.status_id',
                'process_list.updated_by',
                //'process_list.locked_by',
                //'process_list.locked_at',
                'process_list.created_by',
                //'process_list.read_status',
                'user_desk.desk_name',
                'process_type.name as process_name',
                'process_type.form_url',
                'process_type.form_id',
                'process_list.user_id',
                'users.user_first_name',
                'users.user_last_name',
                'company_info.company_name',
                DB::raw("CONCAT(process_status.status_name,'<br/>',process_list.updated_at) as status_name_updated_time"),
            ]);
    }

    public function scopeSearch($query, $request)
    {
        if ($request->has('search_date')) {
            $from = Carbon::parse($request->get('search_date'));
            $to = Carbon::parse($request->get('search_date'));
        } else {
            $from = Carbon::now();
            $to = Carbon::now();
        }

        switch ($request->get('search_time')) {
            case 180:
                $from->subMonths(6);
                $to->addMonths(6);
                break;
            case 90:
                $from->subMonths(3);
                $to->addMonths(3);
                break;
            case 30:
                $from->subMonth();
                $to->addMonth();
                break;
            case 15:
                $from->subWeeks(2);
                $to->addWeeks(2);
                break;
            case 7:
                $from->subWeek();
                $to->addWeek();
                break;
            case 1:
                $from->subDay();
                $to->addDay();
                break;
            default:
                //                $from->subDays($request->get('search_time'));
                //                $to->addDays($request->get('search_time'));
        }
        if ($request->has('search_date') && $request->get('search_time') != 'all') {
            $query->whereBetween('process_list.created_at', [$from, $to]); //date time wise search
        }

        if (strlen($request->get('search_text')) > 1) { //for search text data
            $query->where(function ($query1) use ($request) {
                $query1->where('company_info.company_name', 'like', '%' . $request->get('search_text') . '%')
                    ->orWhere('process_list.tracking_no', 'like', '%' . $request->get('search_text') . '%');
            });
        }

        if ($request->get('search_type') > 0) {
            $query->where('process_list.process_type_id', $request->get('search_type'));

            // The draft application cannot be searched by Desk user
            if (Auth::user()->user_type != '5x505') {
                $query->whereNotIn('process_list.status_id', [-1]);
            }
        } else {
            $query->whereNotIn('process_list.process_type_id', [100]);
        }

        if ($request->has('search_status') && $request->get('search_status') != 0) {
            $query->wherein('process_list.status_id', explode(",", $request->get('search_status')));
        }

        //for dashboard data card
        if ($request->has('card_status') && $request->get('card_status') != 0) {
            $query->wherein('process_list.status_id', explode(",", $request->get('card_status')));
        }

        return $query;
    }
}
