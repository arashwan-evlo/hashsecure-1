<?php

namespace Modules\ChartOfAccounts\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class chartofaccount extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function childs() {
        return $this->hasMany('Modules\ChartOfAccounts\Entities\chartofaccount','parent_id','id') ;
    }

    protected static function newFactory()
    {
        return \Modules\ChartOfAccounts\Database\factories\ChartofaccountFactory::new();
    }
}
