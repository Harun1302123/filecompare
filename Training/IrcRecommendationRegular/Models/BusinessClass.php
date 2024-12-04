<?php 

namespace App\Modules\IrcRecommendationRegular\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessClass extends Model {
    protected $table = 'sector_info_bbs';
    protected $fillable = [
        'id',
        'code',
        'name',
        'type',
        'pare_code',
        'pare_id',
    ];
}
