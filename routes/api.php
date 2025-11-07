<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AccountController;
use App\Http\Controllers\Api\V1\DepositController;
use App\Http\Controllers\Api\V1\TransferController;
use App\Http\Controllers\Api\V1\BillController;
use App\Http\Controllers\Api\V1\BeneficiaryController;
use App\Http\Controllers\Api\V1\ApplicationController;

// Health
Route::get('v1/test', fn() => ['message' => 'API is working']);

// Auth
Route::prefix('v1/auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
});

// Protected
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('auth/me',    [AuthController::class, 'me']);
    Route::post('auth/logout',[AuthController::class, 'logout']);

    // Application (onboarding)
    Route::post('applications', [ApplicationController::class, 'start']);
    Route::get('applications/status', [ApplicationController::class, 'status']); // 👈 move this up
    Route::get('applications/{application}', [ApplicationController::class, 'show']);
    Route::post('applications/{application}/accounts', [ApplicationController::class, 'addRequestedAccount']);
    Route::post('applications/kyc', [ApplicationController::class, 'saveKyc']);
    Route::post('applications/{application}/submit', [ApplicationController::class, 'submit']);



    // Accounts & transactions
    Route::get('accounts', [AccountController::class, 'index']);
    Route::get('accounts/resolve', [\App\Http\Controllers\Api\V1\AccountController::class, 'resolve']);
    Route::get('accounts/{account}', [AccountController::class, 'show']);
    Route::get('accounts/{account}/transactions', [AccountController::class, 'transactions']);

    // Money ops
    Route::post('deposits',  [DepositController::class, 'store']);
    Route::post('transfers', [TransferController::class, 'store']);

    // Bills
    Route::get('billers', [BillController::class, 'billers']);
    Route::post('bill-payments', [BillController::class, 'pay']);

    // Beneficiaries
    Route::get('beneficiaries',    [BeneficiaryController::class, 'index']);
    Route::post('beneficiaries',   [BeneficiaryController::class, 'store']);
    Route::delete('beneficiaries/{beneficiary}', [BeneficiaryController::class, 'destroy']);

    Route::get('transactions/recent', [\App\Http\Controllers\Api\V1\TransactionController::class, 'recent']);

});
