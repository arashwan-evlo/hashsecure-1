<?php

namespace Modules\Sitemanager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SlideMedia extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    
    protected static function newFactory()
    {
        return \Modules\Sitemanager\Database\factories\SlideMediaFactory::new();
    }
}
