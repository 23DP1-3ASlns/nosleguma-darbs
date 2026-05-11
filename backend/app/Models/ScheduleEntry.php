<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_name',
        'subject',
        'teacher',
        'day_of_week',
        'start_time',
        'end_time',
    ];
}
