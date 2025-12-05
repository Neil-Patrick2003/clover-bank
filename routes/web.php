<?php

use App\Http\Controllers\TransactionReportPdfController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('home');
});



Route::get('/admin/transactions/report/pdf', TransactionReportPdfController::class)
    ->middleware(['auth']) // add your Filament/admin middleware here if needed
    ->name('transactions.report.pdf');
