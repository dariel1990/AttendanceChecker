<?php

use App\Employee;
use Carbon\Carbon;
use App\IDImageRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Home Controller
Route::get('/', [HomeController::class, 'index']);
Route::get('/attendanceList', [HomeController::class, 'attendanceList']);
Route::get('/generateIdPicture/{id}', [HomeController::class, 'generateIdPicture']);
Route::get('/production/{id}/{IDNoFontSize}/{nameFontSize}/{positionFontSize}/{addressFontSize}/{officeFontSize}/{pictureSize}/{position}/{office}/', [HomeController::class, 'production']);
