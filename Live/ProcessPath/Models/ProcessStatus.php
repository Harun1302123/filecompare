<?php namespace App\Modules\ProcessPath\Models;

use Illuminate\Database\Eloquent\Model;
class ProcessStatus extends Model
{
    protected $table = 'process_status';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}