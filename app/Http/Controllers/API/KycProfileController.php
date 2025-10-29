<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\KycProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KycProfileController extends Controller
{
    /**
     * Get user's KYC profile
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $kycProfile = $user->kycProfile;

        if (!$kycProfile) {
            return response()->json(['message' => 'KYC profile not found'], 404);
        }

        return response()->json($kycProfile);
    }

    /**
     * Create or update KYC profile
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'kyc_level' => 'required|in:basic,standard,enhanced',
            'id_type' => 'required|in:passport,national_id,drivers_license,voters_id,sss_id,tin_id',
            'id_number' => 'required|string|max:50',
            'id_expiry' => 'nullable|date|after:today',
            'first_name' => 'sometimes|required|string|max:100',
            'middle_name' => 'sometimes|nullable|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'date_of_birth' => 'sometimes|required|date|before:today',
            'place_of_birth' => 'sometimes|required|string|max:255',
            'nationality' => 'sometimes|required|string|max:100',
            'gender' => 'sometimes|required|in:male,female,other',
            'civil_status' => 'sometimes|required|in:single,married,divorced,widowed,separated',
            'occupation' => 'sometimes|required|string|max:255',
            'employer' => 'sometimes|nullable|string|max:255',
            'monthly_income' => 'sometimes|nullable|numeric|min:0',
            'source_of_funds' => 'sometimes|required|string|max:255',
            'address_line_1' => 'sometimes|required|string|max:255',
            'address_line_2' => 'sometimes|nullable|string|max:255',
            'city' => 'sometimes|required|string|max:100',
            'province' => 'sometimes|required|string|max:100',
            'postal_code' => 'sometimes|required|string|max:20',
            'country' => 'sometimes|required|string|max:100',
            'phone_number' => 'sometimes|required|string|max:20',
            'emergency_contact_name' => 'sometimes|nullable|string|max:255',
            'emergency_contact_relationship' => 'sometimes|nullable|string|max:100',
            'emergency_contact_phone' => 'sometimes|nullable|string|max:20',
        ]);

        // Check if ID number is unique (excluding current user's profile)
        $existingKyc = KycProfile::where('id_number', $request->id_number)
            ->where('user_id', '!=', $user->id)
            ->first();

        if ($existingKyc) {
            return response()->json([
                'message' => 'ID number already exists in our records',
                'errors' => ['id_number' => ['This ID number is already registered']],
            ], 422);
        }

        $kycData = $request->only([
            'kyc_level', 'id_type', 'id_number', 'id_expiry',
            'first_name', 'middle_name', 'last_name', 'date_of_birth',
            'place_of_birth', 'nationality', 'gender', 'civil_status',
            'occupation', 'employer', 'monthly_income', 'source_of_funds',
            'address_line_1', 'address_line_2', 'city', 'province',
            'postal_code', 'country', 'phone_number',
            'emergency_contact_name', 'emergency_contact_relationship',
            'emergency_contact_phone'
        ]);

        $kycData['status'] = 'pending_verification';
        $kycData['submitted_at'] = now();

        $kycProfile = $user->kycProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $kycData
        );

        return response()->json([
            'message' => 'KYC profile saved successfully',
            'kyc_profile' => $kycProfile,
        ]);
    }

    /**
     * Update specific KYC fields
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $kycProfile = $user->kycProfile;

        if (!$kycProfile) {
            return response()->json(['message' => 'KYC profile not found'], 404);
        }

        if ($kycProfile->status === 'verified') {
            return response()->json([
                'message' => 'Cannot update verified KYC profile. Please contact support for changes.',
            ], 422);
        }

        $request->validate([
            'kyc_level' => 'sometimes|in:basic,standard,enhanced',
            'id_type' => 'sometimes|in:passport,national_id,drivers_license,voters_id,sss_id,tin_id',
            'id_number' => 'sometimes|string|max:50',
            'id_expiry' => 'sometimes|nullable|date|after:today',
            'first_name' => 'sometimes|string|max:100',
            'middle_name' => 'sometimes|nullable|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'date_of_birth' => 'sometimes|date|before:today',
            'place_of_birth' => 'sometimes|string|max:255',
            'nationality' => 'sometimes|string|max:100',
            'gender' => 'sometimes|in:male,female,other',
            'civil_status' => 'sometimes|in:single,married,divorced,widowed,separated',
            'occupation' => 'sometimes|string|max:255',
            'employer' => 'sometimes|nullable|string|max:255',
            'monthly_income' => 'sometimes|nullable|numeric|min:0',
            'source_of_funds' => 'sometimes|string|max:255',
            'address_line_1' => 'sometimes|string|max:255',
            'address_line_2' => 'sometimes|nullable|string|max:255',
            'city' => 'sometimes|string|max:100',
            'province' => 'sometimes|string|max:100',
            'postal_code' => 'sometimes|string|max:20',
            'country' => 'sometimes|string|max:100',
            'phone_number' => 'sometimes|string|max:20',
            'emergency_contact_name' => 'sometimes|nullable|string|max:255',
            'emergency_contact_relationship' => 'sometimes|nullable|string|max:100',
            'emergency_contact_phone' => 'sometimes|nullable|string|max:20',
        ]);

        // Check ID number uniqueness if being updated
        if ($request->has('id_number') && $request->id_number !== $kycProfile->id_number) {
            $existingKyc = KycProfile::where('id_number', $request->id_number)
                ->where('user_id', '!=', $user->id)
                ->first();

            if ($existingKyc) {
                return response()->json([
                    'message' => 'ID number already exists in our records',
                    'errors' => ['id_number' => ['This ID number is already registered']],
                ], 422);
            }
        }

        $updateData = $request->only([
            'kyc_level', 'id_type', 'id_number', 'id_expiry',
            'first_name', 'middle_name', 'last_name', 'date_of_birth',
            'place_of_birth', 'nationality', 'gender', 'civil_status',
            'occupation', 'employer', 'monthly_income', 'source_of_funds',
            'address_line_1', 'address_line_2', 'city', 'province',
            'postal_code', 'country', 'phone_number',
            'emergency_contact_name', 'emergency_contact_relationship',
            'emergency_contact_phone'
        ]);

        // Reset status to pending if significant changes are made
        $significantFields = ['id_type', 'id_number', 'first_name', 'last_name', 'date_of_birth'];
        $hasSignificantChanges = collect($significantFields)->some(function ($field) use ($request, $kycProfile) {
            return $request->has($field) && $request->$field !== $kycProfile->$field;
        });

        if ($hasSignificantChanges) {
            $updateData['status'] = 'pending_verification';
            $updateData['submitted_at'] = now();
        }

        $kycProfile->update($updateData);

        return response()->json([
            'message' => 'KYC profile updated successfully',
            'kyc_profile' => $kycProfile->fresh(),
        ]);
    }

    /**
     * Get KYC requirements and validation rules
     */
    public function requirements()
    {
        $requirements = [
            'basic' => [
                'required_fields' => ['kyc_level', 'id_type', 'id_number', 'first_name', 'last_name', 'date_of_birth'],
                'description' => 'Basic KYC for standard banking services',
                'limits' => [
                    'daily_transaction_limit' => 50000,
                    'monthly_transaction_limit' => 200000,
                ],
            ],
            'standard' => [
                'required_fields' => [
                    'kyc_level', 'id_type', 'id_number', 'id_expiry', 'first_name', 'last_name',
                    'date_of_birth', 'place_of_birth', 'nationality', 'gender', 'civil_status',
                    'occupation', 'address_line_1', 'city', 'province', 'postal_code', 'country', 'phone_number'
                ],
                'description' => 'Standard KYC for enhanced banking services',
                'limits' => [
                    'daily_transaction_limit' => 200000,
                    'monthly_transaction_limit' => 1000000,
                ],
            ],
            'enhanced' => [
                'required_fields' => [
                    'kyc_level', 'id_type', 'id_number', 'id_expiry', 'first_name', 'last_name',
                    'date_of_birth', 'place_of_birth', 'nationality', 'gender', 'civil_status',
                    'occupation', 'employer', 'monthly_income', 'source_of_funds',
                    'address_line_1', 'city', 'province', 'postal_code', 'country', 'phone_number',
                    'emergency_contact_name', 'emergency_contact_relationship', 'emergency_contact_phone'
                ],
                'description' => 'Enhanced KYC for premium banking services and high-value transactions',
                'limits' => [
                    'daily_transaction_limit' => 1000000,
                    'monthly_transaction_limit' => 5000000,
                ],
            ],
        ];

        return response()->json($requirements);
    }

    /**
     * Get KYC status
     */
    public function status(Request $request)
    {
        $user = $request->user();
        $kycProfile = $user->kycProfile;

        if (!$kycProfile) {
            return response()->json([
                'has_kyc' => false,
                'status' => null,
                'message' => 'No KYC profile found',
            ]);
        }

        return response()->json([
            'has_kyc' => true,
            'status' => $kycProfile->status,
            'kyc_level' => $kycProfile->kyc_level,
            'submitted_at' => $kycProfile->submitted_at,
            'verified_at' => $kycProfile->verified_at,
        ]);
    }
}
