<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AccountController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\TransferController;
use App\Http\Controllers\API\BillController;
use App\Http\Controllers\API\CustomerApplicationController;
use App\Http\Controllers\API\KycProfileController;
use App\Http\Controllers\API\BeneficiaryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix("v1")->group(function() {
    // Public routes
    Route::post("/auth/register", [AuthController::class, "register"]);
    Route::post("/auth/login", [AuthController::class, "login"]);

    // Protected routes
    Route::middleware("auth:sanctum")->group(function() {
        // Auth routes
        Route::post("/auth/logout", [AuthController::class, "logout"]);
        Route::get("/auth/me", [AuthController::class, "me"]);
        Route::get("/user", function (Request $request) {
            return $request->user()->load('accounts');
        });
        Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
        Route::put('/auth/password', [AuthController::class, 'updatePassword']);

        // Account routes
        Route::get('/accounts', [AccountController::class, 'index']);
        Route::get('/accounts/{id}', [AccountController::class, 'show']);
        Route::get('/accounts/resolve', [AccountController::class, 'resolve']);
        Route::get('/accounts/{id}/balance', [AccountController::class, 'balance']);
        Route::get('/accounts/{id}/statement', [AccountController::class, 'statement']);
        
        // Transaction routes
        Route::get('/transactions', [TransactionController::class, 'index']);
        Route::get('/transactions/{id}', [TransactionController::class, 'show']);
        Route::get('/transactions/summary', [TransactionController::class, 'summary']);
        
        // Transfer routes
        Route::get('/transfers', [TransferController::class, 'index']);
        Route::post('/transfers', [TransferController::class, 'store']);
        Route::get('/transfers/{id}', [TransferController::class, 'show']);
        Route::get('/transfers/limits', [TransferController::class, 'limits']);
        
        // Bill Payment routes
        Route::get('/billers', [BillController::class, 'billers']);
        Route::get('/billers/categories', [BillController::class, 'categories']);
        Route::get('/bill-payments', [BillController::class, 'index']);
        Route::post('/bill-payments', [BillController::class, 'store']);
        Route::get('/bill-payments/{id}', [BillController::class, 'show']);
        
        // Customer Application routes
        Route::get('/applications/status', [CustomerApplicationController::class, 'status']);
        Route::get('/applications', [CustomerApplicationController::class, 'index']);
        Route::post('/applications', [CustomerApplicationController::class, 'store']);
        Route::get('/applications/{id}', [CustomerApplicationController::class, 'show']);
        Route::post('/applications/{id}/submit', [CustomerApplicationController::class, 'submit']);
        Route::post('/applications/{id}/cancel', [CustomerApplicationController::class, 'cancel']);
        Route::post('/applications/{id}/requested-accounts', [CustomerApplicationController::class, 'addRequestedAccounts']);
        Route::get('/applications/requirements', [CustomerApplicationController::class, 'requirements']);
        
        // KYC Profile routes
        Route::get('/applications/kyc', [KycProfileController::class, 'show']);
        Route::post('/applications/kyc', [KycProfileController::class, 'store']);
        Route::put('/applications/kyc', [KycProfileController::class, 'update']);
        Route::get('/applications/kyc/status', [KycProfileController::class, 'status']);
        Route::get('/applications/kyc/requirements', [KycProfileController::class, 'requirements']);
        
        // Beneficiary routes
        Route::get('/beneficiaries', [BeneficiaryController::class, 'index']);
        Route::post('/beneficiaries', [BeneficiaryController::class, 'store']);
        Route::get('/beneficiaries/{id}', [BeneficiaryController::class, 'show']);
        Route::put('/beneficiaries/{id}', [BeneficiaryController::class, 'update']);
        Route::delete('/beneficiaries/{id}', [BeneficiaryController::class, 'destroy']);
        Route::post('/beneficiaries/{id}/toggle-status', [BeneficiaryController::class, 'toggleStatus']);
        Route::get('/beneficiaries/banks', [BeneficiaryController::class, 'banks']);
        Route::get('/beneficiaries/currencies', [BeneficiaryController::class, 'currencies']);
    });
});

// Fallback route for undefined API endpoints
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found.'
    ], 404);
});