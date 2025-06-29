<?php

namespace App;

use App\Models\UserAccountAccess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utils\Util;
use DB;
use App\BusinessLocation;

class Account extends Model
{
    use SoftDeletes;
    
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'account_details' => 'array',
    ];
    
    public static function forDropdown($business_id, $prepend_none, $closed = false, $show_balance = false)
    {
        $user_id=auth()->user()->id;
      if(auth()->user()->selected_accounts==0) {
            $query = Account::where('business_id', $business_id)
                ->whereIN('account_type_id', [6]);
        }else{
            $selected_accounts=UserAccountAccess::where('user_id',$user_id)
                ->where('status',1)
                ->pluck('account_id')->toArray();
            $query = Account::where('business_id', $business_id)
                ->whereIN('account_type_id', [6])
                ->whereIN('id',$selected_accounts);
        }
        $can_access_account = auth()->user()->can('account.access');
        if ($can_access_account && $show_balance) {
            // $query->leftjoin('account_transactions as AT', function ($join) {
            //     $join->on('AT.account_id', '=', 'accounts.id');
            //     $join->whereNull('AT.deleted_at');
            // })
            $query->select('accounts.name', 
                    'accounts.id', 
                    DB::raw("(SELECT SUM( IF(account_transactions.type='credit', amount, -1*amount) ) as balance from account_transactions where account_transactions.account_id = accounts.id AND deleted_at is NULL) as balance")
                );
        }

        if (!$closed) {
            $query->where('is_closed', 0);
        }

        $accounts = $query->get();

        $dropdown = [];
        if ($prepend_none) {
            $dropdown[''] = __('lang_v1.none');
        }

        $commonUtil = new Util;
        foreach ($accounts as $account) {
            $name = $account->name;

            if ($can_access_account && $show_balance) {
                $name .= ' (' . __('lang_v1.balance') . ': ' . $commonUtil->num_f($account->balance) . ')';
            }

            $dropdown[$account->id] = $name;
        }

        return $dropdown;
    }

    /**
     * Scope a query to only include not closed accounts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */


    public static function forDropdownmainaccount($business_id, $prepend_none=false, $closed = false)
    {
        $accounts=Account::where('account_type',0)->where('business_id',$business_id)
            ->whereIN('account_type_id',[1,2])
            ->select('id',
                \Illuminate\Support\Facades\DB::raw('CONCAT(COALESCE(account_code, ""), "- ", COALESCE(name, "")) as full_name'))
            ->get()
            ->pluck('full_name','id');

        if ($prepend_none) {
            $accounts->prepend(__('lang_v1.none'), '');
        }
        return $accounts;
    }

    public static function forDropdownsubaccount($business_id, $prepend_none, $closed = false)
    {
        $accounts=Account::where('account_type',0)->where('business_id',$business_id)
            ->whereIN('account_type_id',[3,4,5])
            ->select('id',
                \Illuminate\Support\Facades\DB::raw('CONCAT(COALESCE(account_code, ""), "- ", COALESCE(name, "")) as full_name'))
            ->get()
            ->pluck('full_name','id');

        if ($prepend_none) {
            $accounts->prepend(__('lang_v1.none'), '');
        }
        return $accounts;
    }

    public static function forDropdownAllsubaccount($business_id, $prepend_none, $closed = false)
    {
        $accounts=Account::where('business_id',$business_id)
            ->whereIN('account_type_id',[3,4,5])
            ->select('id',
                \Illuminate\Support\Facades\DB::raw('CONCAT(COALESCE(account_code, ""), "- ", COALESCE(name, "")) as full_name'))
            ->get()
            ->pluck('full_name','id');

        if ($prepend_none) {
            $accounts->prepend(__('lang_v1.none'), '');
        }
        return $accounts;
    }

    public function scopeNotClosed($query)
    {
        return $query->where('is_closed', 0);
    }

    /**
     * Scope a query to only include non capital accounts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return array
     */
    // public function scopeNotCapital($query)
    // {
    //     return $query->where(function ($q) {
    //         $q->where('account_type', '!=', 'capital');
    //         $q->orWhereNull('account_type');
    //     });
    // }

    public static function accountTypes()
    {
        return [
            '' => __('account.not_applicable'),
            'saving_current' => __('account.saving_current'),
            'capital' => __('account.capital')
        ];
    }

    public function account_type()
    {
        return $this->belongsTo(\App\AccountType::class, 'account_type_id');
    }
}
