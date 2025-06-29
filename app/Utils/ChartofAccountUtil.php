<?php

namespace App\Utils;



use App\Account;

class ChartofAccountUtil extends Util
{
    /* for first level $parent_id=0  Code 1
    /* for other level $paret_id>0 Code in 2
    */
    public function GetAccountCode($parent_id){

        $parent_account=Account::where('id',$parent_id)->first();
        $parent_account_code=$parent_account->account_code;
        $last_account=Account::where('parent_id',$parent_id)->orderBy('id', 'desc')->first();
        $last_code=$last_account->account_code;
        $account_code=str_replace($parent_account_code,'',$last_code);
        $count = str_pad($account_code, 2, '0', STR_PAD_LEFT);
        return $count;


    }

}