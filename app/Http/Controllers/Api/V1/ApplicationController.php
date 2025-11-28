<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ApplicationAccount;
use App\Models\CustomerApplication;
use App\Models\KycProfile;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function start(Request $req)
    {
        $app = CustomerApplication::firstOrCreate(
            ['user_id' => $req->user()->id, 'status' => 'draft'],
            ['submitted_at' => now(), 'assigned_admin_id' => null]
        );

        return response()->json(['id' => $app->id, 'status' => $app->status], 201);
    }

    public function show(Request $req, CustomerApplication $application)
    {
        abort_unless($application->user_id === $req->user()->id, 403);
        $application->load('requestedAccounts');

        return [
            'id' => $application->id,
            'status' => $application->status,
            'requested_accounts' => $application->requestedAccounts->map(fn ($r) => [
                'id' => $r->id,
                'requested_type' => $r->requested_type,
                'currency' => $r->currency,
                'initial_deposit' => (float) $r->initial_deposit,
            ]),
        ];
    }

    public function addRequestedAccount(Request $req, CustomerApplication $application)
{
    abort_unless($application->user_id === $req->user()->id, 403);
    abort_if($application->status !== 'draft', 422, 'Application not in draft');

    $data = $req->validate([
        'requested_type' => ['required','in:savings,current,time_deposit'],
        'currency'       => ['required','string','size:3'],
        'initial_deposit'=> ['nullable','numeric','min:0'],
    ]);

    $row = ApplicationAccount::create([
        'application_id' => $application->id,
        'requested_type' => $data['requested_type'],
        'currency'       => strtoupper($data['currency']),
        'initial_deposit'=> (float) ($data['initial_deposit'] ?? 0),
    ]);

    return response()->json(['id' => $row->id], 201);
}

    public function saveKyc(Request $req)
    {
        $data = $req->validate([
            'kyc_level' => ['required','in:basic,standard,enhanced'],
            'id_type'   => ['nullable','string','max:64'],
            'id_number' => ['nullable','string','max:128'],
            'id_expiry' => ['nullable','date'],
        ]);

        KycProfile::updateOrCreate(
            ['user_id' => $req->user()->id],
            $data
        );

        return ['ok' => true];
    }

    public function submit(Request $req, CustomerApplication $application)
    {
        abort_unless($application->user_id === $req->user()->id, 403);
        abort_if(! in_array($application->status, ['draft','submitted']), 422, 'Invalid status');
        abort_if(! $application->requestedAccounts()->exists(), 422, 'Add at least one requested account');

        $application->update(['status' => 'submitted', 'submitted_at' => now()]);

        return ['message' => 'Submitted'];
    }

    public function status(Request $req)
    {


        $user = $req->user();

        $openAccounts = $user->accounts()->where('status','open')->count();

        $kyc = $user->kycProfile()->first();
        $hasKyc = (bool) $kyc;

        $app = CustomerApplication::where('user_id', $user->id)
            ->latest()
            ->withCount('requestedAccounts')
            ->first();

        return response()->json([
            'open_accounts'      => $openAccounts,
            'has_kyc'            => $hasKyc,
            'application'        => $app ? [
                'id'               => $app->id,
                'status'           => $app->status,           // draft | submitted | in_review | approved | rejected
                'requested_count'  => $app->requested_accounts_count,
                'submitted_at'     => $app->submitted_at,
            ] : null,
        ]);
    }

}
