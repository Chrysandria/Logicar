<?php

use App\Http\Controllers\AnomalieController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\MouvementController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\RamassageController;
use App\Http\Controllers\TrajetController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');
Route::view('/login', 'index')->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('session.store');

Route::middleware('auth')->group(function () {
    Route::get('/session', [AuthController::class, 'show'])->name('session.show');
    Route::delete('/session', [AuthController::class, 'destroy'])->name('session.destroy');
    Route::patch('/session/password', [AuthController::class, 'updatePassword'])
        ->name('session.password.update');
    Route::get('/plannings', [PlanningController::class, 'index'])
        ->name('plannings.index');

    Route::middleware('service-role:SNR')->group(function () {
        Route::view('/snr', 'snr')->name('snr');
        Route::view('/snr/{any}', 'snr')->where('any', '.*')->name('snr.fallback');

        Route::apiResources([
            'users' => UserController::class,
            'cars' => CarController::class,
        ], [
            'only' => ['index', 'store', 'destroy'],
            'parameters' => ['cars' => 'car'],
        ]);
        Route::get('/users/{user}', [UserController::class, 'show'])
            ->name('users.show');

        Route::get('/plannings/unassigned', [PlanningController::class, 'unassigned'])
            ->name('plannings.unassigned');
        Route::get('/plannings/ramassages', [RamassageController::class, 'index'])
            ->name('plannings.ramassages');
        Route::post('/plannings/pdf', [PdfController::class, 'generate'])
            ->name('plannings.pdf');
        Route::patch('/plannings/{planning}', [PlanningController::class, 'update'])
            ->whereNumber('planning')
            ->name('plannings.update');
        Route::post('/ramassages', [RamassageController::class, 'store'])->name('ramassages.store');

        Route::get('/services', [EmployeController::class, 'services'])->name('services.index');

        Route::post('/cars/{matricule}/anomalies', [AnomalieController::class, 'store'])
            ->name('cars.anomalies.store');
        Route::get('/anomalies', [AnomalieController::class, 'index'])->name('anomalies.index');
        Route::patch('/anomalies/{anomalie}', [AnomalieController::class, 'update'])
            ->whereNumber('anomalie')
            ->name('anomalies.update');

        Route::apiResource('trips', TrajetController::class);

        Route::get('/movements/monthly', [MouvementController::class, 'monthly'])
            ->name('movements.monthly');
        Route::get('/movements', [MouvementController::class, 'index'])->name('movements.index');
        Route::post('/movements', [MouvementController::class, 'store'])->name('movements.store');
    });

    Route::middleware('service-role:other')->group(function () {
        Route::view('/other_service', 'other_service')->name('other-service');
        Route::view('/other_service/{any}', 'other_service')
            ->where('any', '.*')
            ->name('other-service.fallback');

        Route::get('/employees', [EmployeController::class, 'index'])->name('employees.index');
        Route::post('/plannings', [PlanningController::class, 'store'])->name('plannings.store');
    });
});
