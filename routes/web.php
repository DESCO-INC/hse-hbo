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

Route::prefix('hbo')
    ->name('hbo.')
    ->controller(HboListController::class)
    ->group(function () {
        Route::get('/business_unit', 'business_unit')->name('business_unit');
        Route::get('/business_unit/{business_unit}/companies', 'company')->name('companies');
        Route::get('/list', 'list')->name('list');
        Route::post('/{hbo}/take-action', 'takeAction')->name('takeAction');
        Route::post('/{hbo}/verification', 'Verification')->name('verification');
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

Route::prefix('pob')
    ->name('pob.')
    ->controller(PobController::class)
    ->group(function () {
        Route::get('/data', 'getPobRecords')->name('data');
        Route::get('/list', 'list')->name('list');
        Route::get('/business_unit', 'business_unit')->name('business_unit');
        Route::get('/template', 'downloadTemplate')->name('downloadTemplate');
        Route::post('/upload', 'upload')->name('upload');
        Route::get('/getYearWeek', 'availableYearsAndWeeks')->name('getYearWeek');

        Route::get('/getAveDataCount', 'PobHboAveData')->name('getAveDataCount');
        Route::get('/getWeeklyData', 'PobHboWeeklyData')->name('getWeeklyData');
    });


// MAINTENANCE ROUTE

Route::prefix('maintenance')
    ->name('maintenance.')
    ->middleware('super_admin')
    ->controller(MaintenanceController::class)
    ->group(function () {
        Route::get('/', 'user')->name('user');
        Route::post('/', 'store_user')->name('store_user');
        Route::put('/user/{user}', 'update_user')->name('update_user');
        Route::delete('/user/{id}', 'destroy_user')->name('destroy_user');

        Route::post('/org', 'store_org')->name('store_org');
        Route::put('/org/{org}', 'update_org')->name('update_org');
        Route::delete('/org/{id}', 'destroy_org')->name('destroy_org');

        Route::get('/audit_trail', 'audit_trail')->name('audit_trail');
        Route::get('/organization', 'organization')->name('organization');
    });

// Profile Routes (accessible to logged users)
Route::prefix('maintenance')
    ->controller(MaintenanceController::class)
    ->group(function () {
        Route::get('/profile', 'profile')->name('maintenance.profile');
        Route::put('/profile', 'profile_update')->name('maintenance.profile_update');
    });
