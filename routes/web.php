<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
Route::resource('hbo', HboListController::class)
    ->except(['show'])
    ->middleware('auth');

Route::prefix('hbo')->name('hbo.')->controller(HboListController::class)->group(function () {
    Route::get('/business_unit', 'business_unit')->name('business_unit');
    Route::get('/business_unit/{business_unit}/companies', 'company')->name('companies');
    Route::get('/statuses', 'statuses')->name('statuses');
    Route::get('/list', 'list')->name('list');
    Route::post('/{hbo}/take-action', 'takeAction')->name('takeAction');
    Route::post('/{hbo}/verification', 'Verification')->name('verification');
    Route::get('/filter', 'getFilteredData')->name('filter');
    Route::get('/count', 'getDataCounts')->name('count');
    Route::get('/chart-data', 'getChartData')->name('chartData');
    Route::post('/upload', 'upload')->name('upload');
    Route::get('/export', 'export')->name('export');
    
    Route::get('/getcount', 'hboStatusCount')->name('getcount');
    Route::get('/getcountbyDate', 'hboDataByDate')->name('getcountbyDate');
    Route::get('/getcountbyCategory', 'hboDataByCategory')->name('getcountbyCategory');
    Route::get('/getcountbyGroup', 'hboDataByGroup')->name('getcountbyGroup');
    Route::get('/getcountbyType', 'hboDataByType')->name('getcountbyType');
    Route::get('/getcountbySubCategory', 'hboDataBySubCategory')->name('getcountbySubCategory');
    Route::get('/getcountbyWeek', 'hboDataByWeek')->name('getcountbyWeek');
    Route::get('/getcountbyReporter', 'hboDataByReporter')->name('getcountbyReporter');
    Route::get('/getWeeklyData', 'hboWeeklyData')->name('getWeeklyData');
});

// POB routes
Route::resource('pob', PobController::class)
    ->except(['show'])
    ->middleware('auth');
Route::controller(PobController::class)->group(function () {
    Route::get('/pob/data', 'getPobRecords')->name('pob.data');
    Route::get('/pob/list', 'list')->name('pob.list');
    Route::get('/pob/chartdata', 'getChartData')->name('pob.chart-data');
    Route::get('/pob/chartdata2', 'getChartData2')->name('pob.chart-data2');
    Route::get('/pob/business_unit', 'business_unit')->name('pob.business_unit');
    Route::get('/pob/template', 'downloadTemplate')->name('pob.downloadTemplate');
    Route::post('/pob/upload', 'upload')->name('pob.upload');
    Route::get('/pob/getYearWeek', 'availableYearsAndWeeks')->name('pob.getYearWeek');
});

// Organization Routes
Route::controller(OrganizationController::class)->group(function () {
    Route::get('/org/business_unit', 'business_unit')->name('org.business_unit');
    Route::get('/org/business_unit/{business_unit}/companies', 'company')->name('org.business_unit.companies');
});

// Maintenance Routes
// Profile Routes (accessible to logged users)
Route::prefix('maintenance')
    ->controller(MaintenanceController::class)
    ->group(function () {
        Route::get('/profile', 'profile')->name('maintenance.profile');
        Route::put('/profile', 'profile_update')->name('maintenance.profile_update');
    });

// SUPER ADMIN ONLY
Route::prefix('maintenance')
    ->middleware('super_admin')
    ->controller(MaintenanceController::class)
    ->group(function () {
        Route::get('/', 'user')->name('maintenance.user');
        Route::post('/', 'store_user')->name('maintenance.store_user');
        Route::put('/user/{user}', 'update_user')->name('maintenance.update_user');
        Route::delete('/user/{id}', 'destroy_user')->name('maintenance.destroy_user');

        Route::post('/org', 'store_org')->name('maintenance.store_org');
        Route::put('/org/{org}', 'update_org')->name('maintenance.update_org');
        Route::delete('/org/{id}', 'destroy_org')->name('maintenance.destroy_org');

        Route::get('/audit_trail', 'audit_trail')->name('maintenance.audit_trail');
        Route::get('/organization', 'organization')->name('maintenance.organization');
    });

// Route::post('/maintenance/store/bu', [MaintenanceController::class, 'storeBU'])->name('maintenance.bu.store');
// Route::post('/maintenance/store/company', [MaintenanceController::class, 'storeCompany'])->name('maintenance.company.store');

// Route::delete('/maintenance/bu/{id}', [MaintenanceController::class, 'bu_destroy'])->name('maintenance.bu_destroy');
// Route::delete('/maintenance/company/{id}', [MaintenanceController::class, 'company_destroy'])->name('maintenance.company_destroy');
