<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\KycProfile;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user();
        $kycProfile = KycProfile::where('user_id', $user->id)->first();

        if (!$kycProfile) {
            return response()->json([
                'message' => 'KYC profile not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'KYC profile retrieved successfully',
            'data' => $kycProfile
        ], 200);
    }
}