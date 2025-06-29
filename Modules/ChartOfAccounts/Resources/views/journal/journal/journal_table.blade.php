@foreach($transactions as $transaction)
    <tr >
        <td style="width: 150px">{{$transaction->transaction_date}}</td>
        <td>@lang("account.".$transaction->type )  {{$transaction->additional_notes}}</td>
        <th style="width: 100px">مدين </th>
        <th style="width: 100px">دائن</th>
        <td style="width: 150px">{{$transaction->surname}} {{$transaction->first_name}} {{$transaction->last_name}} </td>
        <td></td>
    </tr>

    {{--<?php
      $account_transactions=\App\AccountTransaction::where('transaction_id',$transaction->id)
                          ->join('accounts','accounts.id','account_transactions.account_id')
                          ->leftjoin('contacts','contacts.id','account_transactions.contact_id')
                          ->select('account_transactions.amount as amount','account_transactions.type as type'
                                  ,'contacts.name as contact_name','accounts.name as account_name')
                            ->get()

       ?>
    @foreach($account_transactions as $data)
        <tr>
            <td style="text-align: left">
                <i class="fa fa-angle-left"></i>
            </td>
            <td >{{$data->account_name}}
            @if(!empty($data->contact_name))
               ({{$data->contact_name}})
            @endif
            </td>
            <td>
                @if($data->type==='debit')
                    {{number_format($data->amount,2,'.',',')}}
                @endif
            </td>
            <td>
                @if($data->type==='credit')
                    {{number_format($data->amount,2,'.',',')}}
                @endif
            </td>
            <td colspan="2"></td>

        </tr>

    @endforeach--}}







@endforeach