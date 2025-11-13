<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CustomerApplication;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerApplicationController extends Controller
{
    /**
     * Get application status for the authenticated user
     */
    public function status(Request $request)
    {
        $user = $request->user();
        
        // Get user's latest application
        $application = $user->applications()->latest()->first();
        
        // Count open accounts
        $openAccounts = $user->accounts()->where('status', 'active')->count();
        
        // Check if user has KYC profile
        $hasKyc = $user->kycProfile()->exists();
        
        return response()->json([
            'application' => $application,
            'open_accounts' => $openAccounts,
            'has_kyc' => $hasKyc,
        ]);
    }

    /**
     * Get user's applications
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $applications = $user->applications()
            ->with(['requestedAccounts'])
            ->latest()
            ->get();

        return response()->json($applications);
    }

    /**
     * Create a new application
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Check if user already has a pending application
        $existingApplication = $user->applications()
            ->whereIn('status', ['draft', 'submitted', 'in_review'])
            ->first();

        if ($existingApplication) {
            return response()->json([
                'message' => 'You already have a pending application',
                'application' => $existingApplication,
            ], 422);
        }

        $application = CustomerApplication::create([
            'user_id' => $user->id,
            'status' => 'draft',
            'application_date' => now(),
        ]);

        return response()->json([
            'message' => 'Application created successfully',
            'application' => $application,
        ]);
    }

    /**
     * Get specific application
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $application = $user->applications()
            ->with(['requestedAccounts'])
            ->findOrFail($id);

        return response()->json($application);
    }

    /**
     * Submit application for review
     */
    public function submit(Request $request, $id)
    {
        $user = $request->user();
        
        $application = $user->applications()->findOrFail($id);

        if ($application->status !== 'draft') {
            return response()->json([
                'message' => 'Application cannot be submitted in current status',
            ], 422);
        }

        // Check if user has KYC profile
        if (!$user->kycProfile()->exists()) {
            return response()->json([
                'message' => 'KYC profile is required before submission',
            ], 422);
        }

        // Check if application has requested accounts
        if ($application->requestedAccounts()->count() === 0) {
            return response()->json([
                'message' => 'At least one account type must be requested',
            ], 422);
        }

        $application->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return response()->json([
            'message' => 'Application submitted successfully',
            'application' => $application->fresh(),
        ]);
    }

    /**
     * Add requested account types to application
     */
    public function addRequestedAccounts(Request $request, $id)
    {
        $user = $request->user();
        
        $request->validate([
            'account_types' => 'required|array|min:1',
            'account_types.*' => 'required|in:savings,checking,time_deposit',
        ]);

        $application = $user->applications()->findOrFail($id);

        if ($application->status !== 'draft') {
            return response()->json([
                'message' => 'Cannot modify application in current status',
            ], 422);
        }

        // Clear existing requested accounts
        $application->requestedAccounts()->delete();

        // Add new requested accounts
        foreach ($request->account_types as $accountType) {
            $application->requestedAccounts()->create([
                'account_type' => $accountType,
                'currency' => 'PHP', // Default currency
                'initial_deposit' => 0,
            ]);
        }

        return response()->json([
            'message' => 'Requested accounts updated successfully',
            'application' => $application->fresh(['requestedAccounts']),
        ]);
    }

    /**
     * Cancel application
     */
    public function cancel(Request $request, $id)
    {
        $user = $request->user();
        
        $application = $user->applications()->findOrFail($id);

        if (!in_array($application->status, ['draft', 'submitted', 'in_review'])) {
            return response()->json([
                'message' => 'Application cannot be cancelled in current status',
            ], 422);
        }

        $application->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return response()->json([
            'message' => 'Application cancelled successfully',
            'application' => $application->fresh(),
        ]);
    }

    /**
     * Get application requirements/checklist
     */
    public function requirements()
    {
        $requirements = [
            [
                'id' => 'kyc_profile',
                'title' => 'KYC Profile',
                'description' => 'Complete your Know Your Customer profile with valid identification',
                'required' => true,
            ],
            [
                'id' => 'account_selection',
                'title' => 'Account Selection',
                'description' => 'Choose the types of accounts you want to open',
                'required' => true,
            ],
            [
                'id' => 'initial_deposit',
                'title' => 'Initial Deposit',
                'description' => 'Minimum initial deposit may be required for certain account types',
                'required' => false,
            ],
        ];

        return response()->json($requirements);
    }
}
