<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ParentRegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PediatricianRegistrationController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/', [ChildController::class, 'showVaccines'])->name('welcome');


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');



Route::get('/register', function () {
    return view('auth.register');
})->name('register');




Route::get('/register-parent', [ParentRegistrationController::class, 'showRegistrationForm'])->name('register.parent');
Route::post('/register-parent', [ParentRegistrationController::class, 'register']);
Route::get('/add-child', function() {
    // This route would go to the add child page
})->name('add_child');



Route::post('/register/medecin', [PediatricianRegistrationController::class, 'register']);
Route::get('/register/medecin', [PediatricianRegistrationController::class, 'showForm'])->name('register.medecin');





// Protect the dashboard routes with authentication middleware
// Example routes for dashboards
Route::get('/meddash', [DashboardController::class, 'pediatricianDashboard'])->name('meddash');
Route::get('/pardash', [DashboardController::class, 'parentDashboard'])->name('pardash');



Route::post('/children', [ChildController::class, 'store'])->name('child.store');
Route::get('/calendar/child/{child_id}', [ChildController::class, 'showCalendar'])->name('calendar.child');
Route::post('/vaccines/update', [DashboardController::class, 'updateVaccines'])->name('vaccines.update');


Route::get('statistics', [AdminController::class, 'statistics'])->name('statistics');

Route::get('/admin/parent/{id}/children', [AdminController::class, 'getChildren']);



Route::put('/children/{id}', [ChildController::class, 'update'])->name('children.update');

Route::delete('/children/{id}', [ChildController::class, 'destroy'])->name('children.destroy');


use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;

Route::get('/send-test-email', function () {
    Mail::to('Cheboublydia2@gmail.com')->send(new WelcomeMail());
    return 'E-mail envoyé avec succès !';
});
Route::post('/pediatrician/accept/{id}', [AdminController::class, 'acceptPediatrician'])->name('pediatrician.accept');
