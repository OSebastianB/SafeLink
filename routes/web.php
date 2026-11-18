<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BrandWatchlistController;
use App\Http\Controllers\Admin\CriterionController;
use App\Http\Controllers\Admin\EvaluationController;
use App\Http\Controllers\Admin\ScanHistoryController as AdminScanHistoryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ThreatIntelligenceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WeightController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ScanController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\ScanHistoryController as UserScanHistoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/scan', [ScanController::class, 'create'])->name('scan.create');
Route::post('/scan', [ScanController::class, 'store'])->name('scan.store');
Route::get('/scan/{scan}', [ScanController::class, 'show'])->name('scan.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/history', [UserScanHistoryController::class, 'index'])->name('history');
    Route::get('/history/export', [UserScanHistoryController::class, 'export'])->name('history.export');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/criteria', [CriterionController::class, 'index'])->name('criteria.index');
    Route::get('/criteria/create', [CriterionController::class, 'create'])->name('criteria.create');
    Route::post('/criteria', [CriterionController::class, 'store'])->name('criteria.store');
    Route::get('/criteria/{criterion}/edit', [CriterionController::class, 'edit'])->name('criteria.edit');
    Route::put('/criteria/{criterion}', [CriterionController::class, 'update'])->name('criteria.update');
    Route::delete('/criteria/{criterion}', [CriterionController::class, 'destroy'])->name('criteria.destroy');
    Route::post('/criteria/{criterion}/scales', [CriterionController::class, 'storeScale'])->name('criteria.scales.store');
    Route::put('/scales/{scale}', [CriterionController::class, 'updateScale'])->name('scales.update');
    Route::delete('/scales/{scale}', [CriterionController::class, 'destroyScale'])->name('scales.destroy');

    Route::get('/weights', [WeightController::class, 'edit'])->name('weights.edit');
    Route::put('/weights', [WeightController::class, 'update'])->name('weights.update');

    Route::get('/threats', [ThreatIntelligenceController::class, 'index'])->name('threats.index');
    Route::get('/threats/create', [ThreatIntelligenceController::class, 'create'])->name('threats.create');
    Route::post('/threats', [ThreatIntelligenceController::class, 'store'])->name('threats.store');
    Route::get('/threats/{threat}/edit', [ThreatIntelligenceController::class, 'edit'])->name('threats.edit');
    Route::put('/threats/{threat}', [ThreatIntelligenceController::class, 'update'])->name('threats.update');
    Route::delete('/threats/{threat}', [ThreatIntelligenceController::class, 'destroy'])->name('threats.destroy');
    Route::post('/threats/import', [ThreatIntelligenceController::class, 'import'])->name('threats.import');

    Route::get('/brands', [BrandWatchlistController::class, 'index'])->name('brands.index');
    Route::get('/brands/create', [BrandWatchlistController::class, 'create'])->name('brands.create');
    Route::post('/brands', [BrandWatchlistController::class, 'store'])->name('brands.store');
    Route::get('/brands/{brand}/edit', [BrandWatchlistController::class, 'edit'])->name('brands.edit');
    Route::put('/brands/{brand}', [BrandWatchlistController::class, 'update'])->name('brands.update');
    Route::delete('/brands/{brand}', [BrandWatchlistController::class, 'destroy'])->name('brands.destroy');

    Route::get('/scans', [AdminScanHistoryController::class, 'index'])->name('scans.index');
    Route::get('/scans/{scan}', [AdminScanHistoryController::class, 'show'])->name('scans.show');

    Route::get('/evaluation', [EvaluationController::class, 'index'])->name('evaluation.index');
    Route::get('/evaluation/create', [EvaluationController::class, 'create'])->name('evaluation.create');
    Route::post('/evaluation', [EvaluationController::class, 'store'])->name('evaluation.store');
    Route::get('/evaluation/{dataset}', [EvaluationController::class, 'show'])->name('evaluation.show');
    Route::post('/evaluation/{dataset}/run', [EvaluationController::class, 'run'])->name('evaluation.run');
    Route::delete('/evaluation/{dataset}', [EvaluationController::class, 'destroy'])->name('evaluation.destroy');

    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});
