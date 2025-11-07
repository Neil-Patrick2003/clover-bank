<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    public function index(Request $req)
    {
        return Beneficiary::where('user_id', $req->user()->id)
            ->latest()
            ->get(['id','name','bank','account_number','currency','created_at']);
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'name'           => ['required','string','max:160'],
            'bank'           => ['required','string','max:160'],
            'account_number' => ['required','string','max:64'],
            'currency'       => ['required','string','size:3'],
        ]);

        $row = Beneficiary::create([
            'user_id'        => $req->user()->id,
            'name'           => $data['name'],
            'bank'           => $data['bank'],
            'account_number' => $data['account_number'],
            'currency'       => strtoupper($data['currency']),
        ]);

        return response()->json(['id' => $row->id], 201);
    }

    public function destroy(Request $req, Beneficiary $beneficiary)
    {
        abort_unless($beneficiary->user_id === $req->user()->id, 403);
        $beneficiary->delete();
        return ['ok' => true];
    }
}
