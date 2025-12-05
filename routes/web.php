<?php

use App\Http\Controllers\TransactionReportPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/admin/transactions/report/pdf', TransactionReportPdfController::class)
    ->middleware(['auth']) // add your Filament/admin middleware here if needed
    ->name('transactions.report.pdf');
