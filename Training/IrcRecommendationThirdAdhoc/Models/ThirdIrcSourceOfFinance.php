<?php 

namespace App\Modules\IrcRecommendationThirdAdhoc\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class ThirdIrcSourceOfFinance extends Model {
    protected $table = 'irc_3rd_source_of_finance';
    protected $fillable = [
        'id',
        'app_id',
        'country_id',
        'equity_amount',
        'loan_amount',
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
