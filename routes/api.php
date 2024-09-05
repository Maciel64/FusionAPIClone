<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CoworkingController;
use App\Http\Controllers\CoworkingOpeningHoursController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HealthAdviceController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\FacilityRoomController;
use App\Http\Controllers\BlockedScheduleController;
use Illuminate\Http\Request;
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

Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'fusion'], function() { 
  Route::post('setActiveStatus', [UserController::class, 'deactiveOrActiveUser']) ;
  Route::post('/room/setFixed', [RoomController::class, 'setFixed']);
  Route::get('/customer/sorted', [CustomerController::class, 'getAllSorted']);
  Route::get('/dashboard/specialists', [UserController::class, 'specialistData']);
  Route::get('/dashboard/salesturnover', [AppointmentController::class, 'salesTurnover']);
  Route::get('/dashboard/frequentschedules', [AppointmentController::class, 'frequentSchedules']);
  Route::get('/dashboard/coworkingcount', [CoworkingController::class, 'countAllCoworkings']);
  Route::get('dashboard/availableRooms', [RoomController::class, 'availableRooms']);
  Route::match(['get', 'post'],'exportAppointments', [AppointmentController::class, 'export']);
  Route::match(['get', 'post'],'exportFinance', [AppointmentController::class, 'financeExport']);
  Route::post('/appointments', [AppointmentController::class, 'indexOrderedAppointments']);
  Route::resource('facility', FacilityController::class)->parameters(['facility' => 'uuid',]);
  Route::get('facilitywithoutpaginate', [FacilityController::class, 'indexNoPaginate']);
  Route::post('/appointment/{user_uuid}', [AppointmentController::class, 'indexAppointmentByAdmin']);
  Route::put('/basicdata/{user_uuid}', [UserController::class, 'updateBasicDataByAdmin']);
  Route::get('rooms', [RoomController::class, 'indexAll'])->name('room.index.all');
  Route::resource('user', UserController::class)->only(['store', 'show', 'update', 'destroy'])->parameters(['user' => 'uuid']);
  Route::get('users/{role}', [UserController::class, 'index'])->name('user.index');
  Route::get('allusers/{role}', [UserController::class, 'indexAll']);
  Route::resource('category', CategoryController::class)->only(['index', 'store', 'show', 'update', 'destroy'])->parameters(['category' => 'uuid']);
  Route::resource('plan', PlanController::class)->except(['index', 'create', 'edit'])->parameters(['plan' => 'uuid']);
  Route::post('customer/check', [CustomerController::class, 'check'])->name('customer.check');
  Route::post('customer/store', [CustomerController::class, 'store'])->name('fusion.customer.store');
  Route::post('customer/search', [CustomerController::class, 'search'])->name('customer.search');
  Route::resource('transfer', TransferController::class)->only(['update', 'show'])->parameters(['transfer' => 'uuid']);
  Route::post('transfer/search', [TransferController::class, 'search'])->name('fusion.transfer.search');
  Route::post('transfer/{uuid}/upload/receipt', [TransferController::class, 'uploadReceipt'])->name('transfer.upload.receipt');
});

Route::group(['prefix' => 'partner'], function(){
  Route::get('blockedschedule/room/{room_uuid}', [BlockedScheduleController::class, 'index']);
  Route::delete('deleteblockedschedule/{blocked_uuid}', [BlockedScheduleController::class, 'destroy']);
  Route::post('bulkblocking', [BlockedScheduleController::class, 'storeBulk']);
  Route::post('operatinghour/{room_uuid}', [RoomController::class, 'registerOperatingHours']);
  Route::get('roomsbycoworking/{coworking_uuid}', [RoomController::class, 'listRoomByCoworking']);
  Route::delete('/room/{room_uuid}/facility/{facility_uuid}', [FacilityRoomController::class, 'destroyFacility']);
  Route::post('/room/{room_uuid}/facility/{facility_uuid}', [FacilityRoomController::class, 'storeFacility']);
  Route::put('/basicdata/{user_uuid}', [UserController::class, 'updateBasicDataPartner']);
  Route::resource('/{user_uuid}/coworking', CoworkingController::class)->only(['index', 'store', 'show', 'update', 'destroy'])->parameters(['coworking' => 'uuid']);
  Route::get('/coworkings', [CoworkingController::class, 'indexAll']);
  Route::resource('/{user_uuid}/coworking.room', RoomController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['room' => 'uuid', 'coworking' => 'coworking_uuid']);
  Route::get('room/{uuid}', [RoomController::class, 'show'])->name('room.show');
  Route::resource('/{user_uuid}/coworking.opening-hour', CoworkingOpeningHoursController::class)->only(['index', 'store', 'destroy'])->parameters(['opening-hour' => 'uuid', 'coworking' => 'coworking_uuid']);
  Route::post('coworking/{coworking_uuid}/opening-hours/destroy-bulk', [CoworkingOpeningHoursController::class, 'destroyBulk'])->name('coworking.opening-hour.destroy.bulk');
  Route::post('room/{uuid}/attach/category', [RoomController::class, 'attachCategory'])->name('room.attach.category');
  Route::post('room/{uuid}/detach/category', [RoomController::class, 'detachCategory'])->name('room.detach.category');
  Route::post('appointment/{uuid}/status/update', [AppointmentController::class, 'updateStatus'])->name('appointment.update.status');
  Route::post('appointment/search', [AppointmentController::class, 'search'])->name('appointment.search');
  Route::get('appointment/{room_uuid}/room/{date}', [AppointmentController::class, 'listByRoom'])->name('appointment.list.by.room');
  Route::get('appointment/{schedule_uuid}/schedule/{date}', [AppointmentController::class, 'listByRoom']);
  Route::get('appointments/{schedule_uuid}/{dateInit}/{dateEnd}', [AppointmentController::class, 'listBySchedule'])->name('appointments.by.schedule');
  Route::post('transfer/search', [TransferController::class, 'index'])->name('partner.transfer.search');
  Route::get('transfer/{uuid}/download/receipt', [TransferController::class, 'downloadReceipt'])->name('transfer.download.receipt');
});

Route::group(['prefix' => 'customer'], function(){
  Route::middleware('customer.is.active')->group(function(){
    Route::put('/basicdata/{user_uuid}', [UserController::class, 'updateBasicDataCustomer']);
    Route::resource('appointment', AppointmentController::class)->only(['store', 'show', 'destroy'])->parameters(['customer' => 'customer_uuid','appointment' => 'uuid']);
    Route::post('appointments', [AppointmentController::class, 'indexAppointment']);
    Route::post('appointment/bulk', [AppointmentController::class, 'storeBulk'])->name('appointment.store.bulk');
    Route::post('room/search', [RoomController::class, 'search'])->name('room.search');
    Route::post('room/searchByNeighborhood', [RoomController::class, 'searchByNeighborhood'])->name('room.search.by.neighborhood');
    Route::post('room/genericsearch', [RoomController::class, 'genericSearch']);
    Route::get('room/{uuid}/availability/{date}', [RoomController::class, 'availability'])->name('room.availability');
    Route::post('room/{uuid}/availability', [RoomController::class, 'availabilityBulk'])->name('room.availability.bulk');
    Route::post('{uuid}/card', [CardController::class, 'store'])->name('card.store');
    Route::delete('{card_uuid}/card', [CardController::class, 'destroy'])->name('card.destroy');
    Route::put('{uuid}/card/{card_uuid}', [CardController::class, 'update'])->name('card.update');
    Route::get('{uuid}/card/{card_uuid}', [CardController::class, 'show'])->name('card.show');
    Route::get('card/{user_uuid}', [CardController::class, 'index'])->name('card.index');
    Route::post('{user_uuid}/card/{card_uuid}', [CardController::class, 'setDefault']);
    Route::resource('billing', BillingController::class)->only(['index', 'show'])->parameters(['billing' => "uuid"]);
    Route::post('{uuid}/cancel', [CustomerController::class, 'cancelAccount'])->name('customer.cancel');
    Route::get('{uuid}/subscription', [SubscriptionController::class, 'show'])->name('subscription.show');
    Route::post('{uuid}/subscription', [SubscriptionController::class, 'store'])->name('subscription.store');
    Route::put('{uuid}/subscription/{subscription_uuid}', [SubscriptionController::class, 'update'])->name('subscription.update');
  });

  Route::post('{uuid}/active', [CustomerController::class, 'activeAccount'])->name('customer.active');
});

Route::get('/rooms/{partner_uuid}', [RoomController::class, 'listByPartner'])->name('room.list.by.partner');
Route::resource('address', AddressController::class)->only(['update', 'store', 'destroy'])->parameters(['address' => 'uuid']);
Route::resource('contact', ContactController::class)->only(['store', 'show', 'update', 'destroy'])->parameters(['contact' => 'uuid']);
Route::resource('health-advice', HealthAdviceController::class)->only(['index','store', 'show', 'update', 'destroy'])->parameters(['health-advice' => 'uuid']);

Route::resource('photo', PhotoController::class)->only(['show', 'store'])->parameters(['photo' => 'uuid']);
Route::delete('photo/{uuid}/{type}', [PhotoController::class, 'destroy'])->name('photo.destroy');
Route::post('photos/{uuid}/{type}', [PhotoController::class, 'index'])->name('photo.index');
Route::post('photo/bulk', [PhotoController::class, 'bulkStore'])->name('photo.bulk.store');
 
