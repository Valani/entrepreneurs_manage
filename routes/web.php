<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntrepreneurController;
use App\Http\Controllers\KvedController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\FinancialDataController;
use App\Http\Controllers\TaxPaymentController;

Route::resource('entrepreneurs', EntrepreneurController::class);
Route::resource('kveds', KvedController::class);
Route::get('/search/entrepreneurs', [EntrepreneurController::class, 'search'])->name('entrepreneurs.search');
Route::get('/keys-overview', [EntrepreneurController::class, 'keysOverview'])->name('entrepreneurs.keys-overview');
Route::resource('settings', SettingController::class)->only(['index', 'update']);
Route::post('/entrepreneurs/update-report', [EntrepreneurController::class, 'updateReport'])->name('entrepreneurs.update-report');
Route::get('/entrepreneurs/{entrepreneur}/financial-data', [FinancialDataController::class, 'index'])
    ->name('entrepreneurs.financial-data');
Route::post('/entrepreneurs/{entrepreneur}/financial-data', [FinancialDataController::class, 'update'])
    ->name('entrepreneurs.financial-data.update');

Route::get('/entrepreneurs/{entrepreneur}/c', [TaxPaymentController::class, 'index'])
    ->name('entrepreneurs.tax-payments');
Route::post('/entrepreneurs/{entrepreneur}/tax-payments', [TaxPaymentController::class, 'store'])
    ->name('entrepreneurs.tax-payments.store');
Route::put('/tax-payments/{payment}', [TaxPaymentController::class, 'update'])
    ->name('entrepreneurs.tax-payments.update');
Route::delete('/tax-payments/{payment}', [TaxPaymentController::class, 'destroy'])
    ->name('entrepreneurs.tax-payments.destroy');

Route::get('/search/kveds', [EntrepreneurController::class, 'searchKveds'])->name('entrepreneurs.search-kveds');
Route::post('/entrepreneurs/{entrepreneur}/financial-data/import', [FinancialDataController::class, 'import'])
    ->name('entrepreneurs.financial-data.import');
Route::get('/entrepreneurs/{entrepreneur}/financial-data/export', [FinancialDataController::class, 'export'])
    ->name('entrepreneurs.financial-data.export');
Route::get('/', function () {
    return view('welcome');
});
