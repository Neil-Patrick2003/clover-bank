<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function recent(Request $req)
    {
        $limit = (int) min(max((int) $req->query('limit', 12), 1), 50);

        $accountIds = $req->user()->accounts()->pluck('id');
        if ($accountIds->isEmpty()) {
            return [];
        }

        $rows = Transaction::whereIn('account_id', $accountIds)
            ->latest()
            ->limit($limit)
            ->get(['id','account_id','type','amount','currency','status','reference_no','remarks','created_at']);

        // attach last4 account_number for convenience
        $accounts = $req->user()->accounts()->get(['id','account_number'])->keyBy('id');

        return $rows->map(function ($r) use ($accounts) {
            $acc = $accounts->get($r->account_id);
            return [
                'id'           => $r->id,
                'type'         => $r->type,
                'amount'       => (float) $r->amount,
                'currency'     => $r->currency,
                'status'       => $r->status,
                'reference_no' => $r->reference_no,
                'remarks'      => $r->remarks,
                'created_at'   => $r->created_at,
                'account_number' => $acc ? ('****' . substr($acc->account_number, -4)) : null,
            ];
        });
    }
}
