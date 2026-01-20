<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\RegisterUserController;
use App\Http\Controllers\HboListController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PobController;
use App\Http\Controllers\OrganizationController;


// Auth
Route::get('/', [SessionController::class, 'index'])->name('login');
Route::post('/login', [SessionController::class, 'store']);
Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');

// Register
Route::get('/register', [RegisterUserController::class, 'index'])->middleware('auth');
Route::post('/register', [RegisterUserController::class, 'store'])->middleware('auth');

// HBO Routes
Route::resource('hbo', HboListController::class)->except(['show'])->middleware('auth'); 
Route::controller(HboListController::class)->group(function () {
    Route::get('/hbo/business_unit', 'business_unit')->name('hbo.business_unit');
    Route::get('/hbo/business_unit/{business_unit}/companies', 'company')->name('hbo.companies');
    Route::get('/hbo/statuses', 'statuses')->name('hbo.statuses');
    Route::get('/hbo/list', 'list')->name('hbo.list');
    Route::post('/hbo/{hbo}/take-action', 'takeAction')->name('hbo.takeAction');
    Route::post('/hbo/{hbo}/verification', 'Verification')->name('hbo.verification');
    Route::get('/hbo/filter', 'getFilteredData')->name('hbo.filter');
    Route::get('/hbo/count', 'getDataCounts')->name('hbo.count');
    Route::get('/hbo/chart-data', 'getChartData')->name('hbo.chartData');
    Route::post('/hbo/upload', 'upload')->name('hbo.upload');
    Route::get('/hbo/export', 'export')->name('hbo.export');
});

// POB routes
Route::resource('pob', PobController::class)->except(['show'])->middleware('auth');
Route::controller(PobController::class)->group(function () {
    Route::get('/pob/data', 'getPobRecords')->name('pob.data');
    Route::get('/pob/list', 'list')->name('pob.list');
    Route::get('/pob/chartdata', 'getChartData')->name('pob.chart-data');
    Route::get('/pob/chartdata2', 'getChartData2')->name('pob.chart-data2');
    Route::get('/pob/business_unit', 'business_unit')->name('pob.business_unit');
    Route::get('/pob/template/{business_unit}', 'downloadTemplate')->name('pob.downloadTemplate');
    Route::post('/pob/upload', 'upload')->name('pob.upload');
    Route::get('/pob/getYearWeek', 'availableYearsAndWeeks')->name('pob.getYearWeek');
});

// Organization Routes
Route::controller(OrganizationController::class)->group(function () {
    Route::get('/org/business_unit', 'business_unit')->name('org.business_unit');
    Route::get('/org/business_unit/{business_unit}/companies', 'company')->name('org.business_unit.companies');
});


// Maintenance Routes
Route::prefix('maintenance')->controller(MaintenanceController::class)->group(function ()  {
    Route::get('/', 'index')->name('maintenance.index');
    Route::post('/', 'store_user')->name('maintenance.store_user');
    Route::delete('/user/{id}', 'destroy_user')->name('maintenance.destroy_user');
    Route::post('/org', 'store_org')->name('maintenance.store_org');
    Route::delete('/org/{id}', 'destroy_org')->name('maintenance.destroy_org');
    Route::get('/profile', 'profile')->name('maintenance.profile');
    Route::put('/profile', 'profile_update')->name('maintenance.profile_update');
    
});

// Route::post('/maintenance/store/bu', [MaintenanceController::class, 'storeBU'])->name('maintenance.bu.store');
// Route::post('/maintenance/store/company', [MaintenanceController::class, 'storeCompany'])->name('maintenance.company.store');

// Route::delete('/maintenance/bu/{id}', [MaintenanceController::class, 'bu_destroy'])->name('maintenance.bu_destroy');
// Route::delete('/maintenance/company/{id}', [MaintenanceController::class, 'company_destroy'])->name('maintenance.company_destroy');

