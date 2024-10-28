<?php

namespace App\Modules\BidaRegistrationAmendment\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model {
    protected $table = 'product_unit';
    protected $fillable = [
        'id',
        'name'
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
