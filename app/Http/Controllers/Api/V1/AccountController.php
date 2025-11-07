<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $req)
    {
        $accounts = Account::where('user_id', $req->user()->id)
            ->orderBy('created_at','desc')
            ->get(['id','account_number','currency','balance','status','created_at']);

        return $accounts;
    }

    public function show(Request $req, Account $account)
    {
        abort_unless($account->user_id === $req->user()->id, 403);
        return $account->only(['id','account_number','currency','balance','status','created_at']);
    }

    public function transactions(Request $req, Account $account)
    {
        abort_unless($account->user_id === $req->user()->id, 403);

        $limit = (int) min(max((int) $req->query('limit', 50), 1), 200);

        $rows = Transaction::where('account_id', $account->id)
            ->latest()
            ->limit($limit)
            ->get(['id','type','amount','currency','status','reference_no','remarks','created_at']);

        return $rows;
    }

    public function resolve(Request $req)
    {
        $data = $req->validate([
            'account_number' => ['nullable','string','max:64'],
            'email'          => ['nullable','email','max:160'],
            'username'       => ['nullable','string','max:80'],
        ]);

        // Priority: explicit account_number > email > username
        if (!empty($data['account_number'])) {
            $acc = \App\Models\Account::where('account_number', $data['account_number'])
                ->where('status', 'open')->first();
            if (! $acc) return response()->json(['message' => 'Account not found or not open'], 404);

            $u = $acc->user()->first(['id','username','email']);
            return [
                'account_number' => $acc->account_number,
                'currency'       => $acc->currency,
                'recipient'      => [
                    'id' => $u?->id, 'username' => $u?->username, 'email' => $u?->email,
                ],
            ];
        }

        if (!empty($data['email']) || !empty($data['username'])) {
            $userQuery = \App\Models\User::query();
            if (!empty($data['email']))    $userQuery->where('email', $data['email']);
            if (!empty($data['username'])) $userQuery->orWhere('username', $data['username']);
            $user = $userQuery->first(['id','username','email']);
            if (! $user) return response()->json(['message' => 'User not found'], 404);

            // choose a default/open account to receive
            $acc = $user->accounts()->where('status','open')->orderBy('created_at')->first();
            if (! $acc) return response()->json(['message' => 'Recipient has no open account'], 404);

            return [
                'account_number' => $acc->account_number,
                'currency'       => $acc->currency,
                'recipient'      => [
                    'id' => $user->id, 'username' => $user->username, 'email' => $user->email,
                ],
            ];
        }

        return response()->json(['message' => 'Provide account_number or email/username'], 422);
    }

}
