<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelRoiLog extends Model
{
    protected $table = 'level_roi_logs';

    protected $fillable = [
        'member_id', 'from_id', 'daily_roi_log_id', 'level', 'percent', 'base_amount', 'amount', 'roi_date',
    ];
}
