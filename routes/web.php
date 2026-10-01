<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/moov', [App\Http\Controllers\MouvementController::class, 'index']);

Route::get('/login', function () {
    return view('index');
})->name('login');

Route::post('/authentify', [App\Http\Controllers\AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
    Route::get('/user', [App\Http\Controllers\AuthController::class, 'getUser']);
    Route::get('/get-plannings', [App\Http\Controllers\PlanningController::class, 'index']);

    Route::get('/emps', [App\Http\Controllers\EmployeController::class, 'index']);

    Route::get('/snr', function () {
        return view('snr');
    });

    Route::get('/snr/{any}', function () {
        return view('snr');
    })->where('any', '.*');

    Route::get('/other_service', function () {
        return view('other_service');
    });

    Route::get('/other_service/{any}', function () {
        return view('other_service');
    })->where('any', '.*');

    Route::get('/nweek', [App\Http\Controllers\PlanningController::class, 'Next_Week']);

    Route::post('/save_planning', [App\Http\Controllers\PlanningController::class, 'store']);

    Route::get('/gap', [App\Http\Controllers\PlanningController::class, 'allPlanning']);

    Route::post('/pdf', [App\Http\Controllers\PdfController::class, 'generate']);

    Route::put('/set_non_recups', [App\Http\Controllers\PlanningController::class, 'update']);

    Route::post('/create_ramassage', [App\Http\Controllers\RamassageController::class, 'createRamassage']);

    Route::get('/available_cars', [App\Http\Controllers\CarController::class, 'index']);

    Route::get('/pwr', [App\Http\Controllers\PlanningController::class, 'pwr']);

    Route::get('/pr', [App\Http\Controllers\PlanningController::class, 'pr']);

    Route::get('/allTrajet', [App\Http\Controllers\TrajetController::class, 'index']);

    Route::get('/allCars', [App\Http\Controllers\CarController::class, 'index']);

    Route::put('/updateCar', [App\Http\Controllers\CarController::class, 'update']);

    Route::post('/newCar', [App\Http\Controllers\CarController::class, 'create']);

    Route::delete('/deleteCar', [App\Http\Controllers\CarController::class, 'delete']);

    Route::get('/daily_moov', [App\Http\Controllers\MouvementController::class, 'getDailyMoov']);

    Route::post('/store_moov', [App\Http\Controllers\MouvementController::class, 'store']);

    Route::get('/current_anomalies', [App\Http\Controllers\AnomalieController::class, 'index']);

    Route::put('/end_anomalie', [App\Http\Controllers\AnomalieController::class, 'update']);

    Route::get('/findAnom', [App\Http\Controllers\AnomalieController::class, 'show']);

    Route::get('/getServices', [App\Http\Controllers\EmployeController::class, 'getServices']);

    Route::get('/allUsers', [App\Http\Controllers\AuthController::class, 'AllUsers']);

    Route::put('resetPassword', [App\Http\Controllers\AuthController::class, 'reset']);

    Route::delete('/deleteUser',[App\Http\Controllers\AuthController::class, 'delete']);

});
