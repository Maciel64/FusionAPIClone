<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PagarmeConstroller;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HealthAdviceController;
use App\Jobs\TestMailJob;
use App\Mail\SendgridTestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// route to health-advice list
Route::get('health-advice', [HealthAdviceController::class, 'index']);

// routes to login
Route::post('loginTwoFactor', [LoginController::class, 'authenticateTwoFactor']);
Route::post('authenticateToken', [LoginController::class, 'authenticateLoginToken']);

Route::post('login', [LoginController::class, 'authenticate'])->name('login');
Route::get('logout', [LoginController::class, 'logout'])->name('logout');
Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.reset');
Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');

// Route::post('register/customer', [UserController::class, 'storeCustomer'])->name('register.customer');
Route::post('customer/completeData', [CustomerController::class, 'completeUserData']);
Route::post('customer/store', [CustomerController::class, 'store'])->name('customer.store');
Route::post('customer/register', [CustomerController::class, 'register'])->name('customer.register');
Route::get('/fusion/plan', [PlanController::class, 'index'])->name('plan.index');
Route::get('address/{zip_code}/search', [AddressController::class, 'findByZipCode'])->name('address.find.by.zip_code');

/**
 * verification.notice
 * 
 * When a user tries to access a route without being verified
 * 
 * @group User
 * 
 * @response 302 
 * 
 * 
 */
Route::get('/auth/email/verification-notice', function(){
  return redirect(config('settings.url_verification_notice'));
})->name('verification.notice');

Route::middleware('verify.internal.token')->post('/user/verify-email', [UserController::class, 'verifyEmail'])->name('user.verify');
Route::middleware('verify.internal.token')->post('/user/resend/verification-code', [UserController::class, 'resendVerificationCode'])->name('user.resend.verification.code');

Route::post('/hook/pagarme', [PagarmeConstroller::class, 'hook'])->name('pagarme.hook');

Route::get('/force/verification/{email}', [UserController::class, 'forceVerification'])->name('force.verification');

Route::get('/senha', function (Request $request) {
  return Hash::make($request->senha);
});