<?php

namespace App\Modules\BidaRegistrationAmendment\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class SourceOfFinanceAmendment extends Model {
    protected $table = 'source_of_finance_amendment';
    protected $fillable = [
        'id',
        'app_id',
        'process_type_id',
        'country_id',
        'equity_amount',
        'loan_amount',
        'n_country_id',
        'n_equity_amount',
        'n_loan_amount',
        'status',
        'is_archive',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public static function boot() {
        parent::boot();
        static::creating(function($post) {
            $post->created_by = CommonFunction::getUserId();
            $post->updated_by = CommonFunction::getUserId();
        });

        static::updating(function($post) {
            $post->updated_by = CommonFunction::getUserId();
        });
    }

}
