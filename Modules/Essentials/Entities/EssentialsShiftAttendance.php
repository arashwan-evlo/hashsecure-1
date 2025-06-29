<?php

namespace Modules\Essentials\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EssentialsShiftAttendance extends Model
{
    use HasFactory;


    protected $guarded = ['id'];
    
    protected static function newFactory()
    {
        return \Modules\Essentials\Database\factories\EssentialsShiftAttendanceFactory::new();
    }
}
