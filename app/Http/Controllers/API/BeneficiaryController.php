<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    /**
     * Get user's beneficiaries
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'search' => 'sometimes|string|max:255',
            'bank' => 'sometimes|string|max:255',
            'currency' => 'sometimes|string|size:3',
        ]);

        $query = $user->beneficiaries();

        // Search by name, bank, or account number
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('bank', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%");
            });
        }

        // Filter by bank
        if ($request->has('bank')) {
            $query->where('bank', 'like', "%{$request->bank}%");
        }

        // Filter by currency
        if ($request->has('currency')) {
            $query->where('currency', $request->currency);
        }

        $beneficiaries = $query->orderBy('name')->get();

        return response()->json($beneficiaries);
    }

    /**
     * Add a new beneficiary
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'bank' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'currency' => 'required|string|size:3',
            'nickname' => 'sometimes|nullable|string|max:100',
            'notes' => 'sometimes|nullable|string|max:500',
        ]);

        // Check if beneficiary already exists for this user
        $existingBeneficiary = $user->beneficiaries()
            ->where('account_number', $request->account_number)
            ->where('bank', $request->bank)
            ->first();

        if ($existingBeneficiary) {
            return response()->json([
                'message' => 'Beneficiary with this account number and bank already exists',
                'beneficiary' => $existingBeneficiary,
            ], 422);
        }

        $beneficiary = $user->beneficiaries()->create([
            'name' => $request->name,
            'bank' => $request->bank,
            'account_number' => $request->account_number,
            'currency' => strtoupper($request->currency),
            'nickname' => $request->nickname,
            'notes' => $request->notes,
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Beneficiary added successfully',
            'beneficiary' => $beneficiary,
        ]);
    }

    /**
     * Get specific beneficiary
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $beneficiary = $user->beneficiaries()->findOrFail($id);

        return response()->json($beneficiary);
    }

    /**
     * Update beneficiary
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        
        $beneficiary = $user->beneficiaries()->findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'bank' => 'sometimes|required|string|max:255',
            'account_number' => 'sometimes|required|string|max:50',
            'currency' => 'sometimes|required|string|size:3',
            'nickname' => 'sometimes|nullable|string|max:100',
            'notes' => 'sometimes|nullable|string|max:500',
            'status' => 'sometimes|in:active,inactive',
        ]);

        // Check for duplicate if account number or bank is being updated
        if ($request->has('account_number') || $request->has('bank')) {
            $accountNumber = $request->get('account_number', $beneficiary->account_number);
            $bank = $request->get('bank', $beneficiary->bank);
            
            $existingBeneficiary = $user->beneficiaries()
                ->where('account_number', $accountNumber)
                ->where('bank', $bank)
                ->where('id', '!=', $beneficiary->id)
                ->first();

            if ($existingBeneficiary) {
                return response()->json([
                    'message' => 'Beneficiary with this account number and bank already exists',
                ], 422);
            }
        }

        $updateData = $request->only([
            'name', 'bank', 'account_number', 'currency', 
            'nickname', 'notes', 'status'
        ]);

        if (isset($updateData['currency'])) {
            $updateData['currency'] = strtoupper($updateData['currency']);
        }

        $beneficiary->update($updateData);

        return response()->json([
            'message' => 'Beneficiary updated successfully',
            'beneficiary' => $beneficiary->fresh(),
        ]);
    }

    /**
     * Delete beneficiary
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        $beneficiary = $user->beneficiaries()->findOrFail($id);
        
        $beneficiary->delete();

        return response()->json([
            'message' => 'Beneficiary deleted successfully',
        ]);
    }

    /**
     * Get beneficiary banks/institutions
     */
    public function banks()
    {
        // This could be from a database table or configuration
        $banks = [
            ['code' => 'BDO', 'name' => 'Banco de Oro'],
            ['code' => 'BPI', 'name' => 'Bank of the Philippine Islands'],
            ['code' => 'MBTC', 'name' => 'Metrobank'],
            ['code' => 'PNB', 'name' => 'Philippine National Bank'],
            ['code' => 'LBP', 'name' => 'Land Bank of the Philippines'],
            ['code' => 'DBP', 'name' => 'Development Bank of the Philippines'],
            ['code' => 'UCPB', 'name' => 'United Coconut Planters Bank'],
            ['code' => 'RCBC', 'name' => 'Rizal Commercial Banking Corporation'],
            ['code' => 'SB', 'name' => 'Security Bank'],
            ['code' => 'EWB', 'name' => 'East West Bank'],
            ['code' => 'CLOVER', 'name' => 'Clover Bank'],
        ];

        return response()->json($banks);
    }

    /**
     * Get supported currencies
     */
    public function currencies()
    {
        $currencies = [
            ['code' => 'PHP', 'name' => 'Philippine Peso', 'symbol' => '₱'],
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£'],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥'],
            ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => 'S$'],
            ['code' => 'HKD', 'name' => 'Hong Kong Dollar', 'symbol' => 'HK$'],
        ];

        return response()->json($currencies);
    }

    /**
     * Activate/Deactivate beneficiary
     */
    public function toggleStatus(Request $request, $id)
    {
        $user = $request->user();
        
        $beneficiary = $user->beneficiaries()->findOrFail($id);
        
        $newStatus = $beneficiary->status === 'active' ? 'inactive' : 'active';
        $beneficiary->update(['status' => $newStatus]);

        return response()->json([
            'message' => "Beneficiary {$newStatus}",
            'beneficiary' => $beneficiary->fresh(),
        ]);
    }
}
