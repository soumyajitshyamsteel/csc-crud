<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->as('auth.')->group(function(){
    Route::post('/login', 'login')->name('login');
});

Route::controller(CrudController::class)->middleware('auth:api')->as('location.')->group(function(){
    Route::get('list', 'index')->name('list');
    Route::get('get-location', 'findType')->name('find.location');
    Route::get('get-cities', 'getCitiesByCountry')->name('get.cities.by.country');
    Route::post('add-location','addType')->name('add.location');
    Route::post('edit-location','editType')->name('edit.location');
    Route::post('delete-location','deleteType')->name('delete.location');
});

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}); */
