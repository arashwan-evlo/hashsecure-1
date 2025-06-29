<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;



class AccountTransaction extends Model
{
    use SoftDeletes;
    
    protected $guarded = ['id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'operation_date',
        'created_at',
        'updated_at'
    ];

    public function media()
    {
        return $this->morphMany(\App\Media::class, 'model');
    }

    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class, 'transaction_id');
    }

    /**
     * Gives account transaction type from payment transaction type
     * @param  string $payment_transaction_type
     * @return string
     */
    public static function getAccountTransactionType($tansaction_type)
    {
        $account_transaction_types = [
            'sell' => 'credit',
            'purchase' => 'debit',
            'expense' => 'debit',
            'stock_adjustment' => 'debit',
            'purchase_return' => 'credit',
            'sell_return' => 'debit',
            'payroll' => 'debit',
            'expense_refund' => 'credit'
        ];

        return $account_transaction_types[$tansaction_type];
    }

    /**
     * Creates new account transaction
     * @return obj
     */
    public static function createAccountTransaction($data)
    {

          $transaction_data = [
            'amount' => $data['amount'],
            'account_id' => $data['account_id'],
            'business_id' => !empty($data['business_id'])?$data['business_id']:null,
            'contact_id' =>!empty($data['contact_id'])?$data['contact_id']:null,
            'type' => $data['type'],
            'sub_type' => !empty($data['sub_type']) ? $data['sub_type'] : null,
            'operation_date' => !empty($data['operation_date']) ? $data['operation_date'] : \Carbon::now(),
            'created_by' => $data['created_by'],
            'transaction_id' => !empty($data['transaction_id']) ? $data['transaction_id'] : null,
            'transaction_payment_id' => !empty($data['transaction_payment_id']) ? $data['transaction_payment_id'] : null,
            'note' => !empty($data['note']) ? $data['note'] : null,
            'transfer_transaction_id' => !empty($data['transfer_transaction_id']) ? $data['transfer_transaction_id'] : null,
            'cost_center'=>!empty($data['cost_center']) ? $data['cost_center'] : 0,
            'percent'=>!empty($data['percent']) ? $data['percent'] : 0,
            'cost_center_amount'=>!empty($data['cost_center_amount']) ? $data['cost_center_amount'] :0,
        ];

        $account_transaction = AccountTransaction::create($transaction_data);
        /*todo:  Update balance 18-2-2025*/
        $account=Account::where('id',$data['account_id'])->first();
        $parent_id=$account->parent_id;
        $amount=$data['amount'];
        if($data['type']==='debit'){
            $amount=-$data['amount'];
        }
        $account->balance=$account->balance+$amount;
        $account->save();
        while ($parent_id>0){
            $account=Account::where('id',$parent_id)->first();
            $parent_id=$account->parent_id;
            $account->balance=$account->balance+$amount;
            $account->save();
        }

        return $account_transaction;
    }

    /**
     * Added by Ali to update all parents balance
     *  $data is an account transaction
     */
  public static function updateparentpalance($data){
    $account=Account::where('id',$data['account_id'])->first();
    $parent_id=$account->parent_id;
    $amount=$data['amount'];
    if($data['type']==='debit'){
        $amount=-$data['amount'];
    }
    $account->balance=$account->balance+$amount;
    $account->save();
    while ($parent_id>0){
        $account=Account::where('id',$parent_id)->first();
        $parent_id=$account->parent_id;
        $account->balance=$account->balance+$amount;
        $account->save();
    }
}

    /**
     * Updates transaction payment from transaction payment
     * @param  obj $transaction_payment
     * @param  array $inputs
     * @param  string $transaction_type
     * @return string
     */
    public static function updateAccountTransaction($transaction_payment, $transaction_type)
    {
        if (!empty($transaction_payment->account_id)) {
            $account_transaction = AccountTransaction::where(
                'transaction_payment_id',
                $transaction_payment->id
            )
                    ->first();
            if (!empty($account_transaction)) {
                $account_transaction->amount = $transaction_payment->amount;
                $account_transaction->account_id = $transaction_payment->account_id;
                $account_transaction->save();
                return $account_transaction;
            } else {
                $accnt_trans_data = [
                    'amount' => $transaction_payment->amount,
                    'account_id' => $transaction_payment->account_id,
                    'type' => self::getAccountTransactionType($transaction_type),
                    'operation_date' => $transaction_payment->paid_on,
                    'created_by' => $transaction_payment->created_by,
                    'transaction_id' => $transaction_payment->transaction_id,
                    'transaction_payment_id' => $transaction_payment->id
                ];

                //If change return then set type as debit
                if ($transaction_payment->transaction->type == 'sell' && $transaction_payment->is_return == 1) {
                    $accnt_trans_data['type'] = 'debit';
                }

                self::createAccountTransaction($accnt_trans_data);
            }
        }
    }

    public static function deleteAccountTransaction($transaction_id)
    {
        $account_transaction=AccountTransaction::where('transaction_id',$transaction_id)->get();
        foreach ($account_transaction as $row){

            /*todo:  Update balance 18-2-2025*/
            $account=Account::where('id',$row->account_id)->first();
            $parent_id=$account->parent_id;
            $amount=$row->amount;
            if($row->type==='credit'){
                $amount=-$row->amount;
            }
            $account->balance=$account->balance+$amount;
            $account->save();
            while ($parent_id>0){
                $account=Account::where('id',$parent_id)->first();
                $parent_id=$account->parent_id;
                $account->balance=$account->balance+$amount;
                $account->save();
            }
            $row->delete();



        }

    }



    public function transfer_transaction()
    {
        return $this->belongsTo(\App\AccountTransaction::class, 'transfer_transaction_id');
    }

    public function account()
    {
        return $this->belongsTo(\App\Account::class, 'account_id');
    }
}
