<?php

namespace App\Providers;

use App\Models\Address;
use App\Models\Appointment;
use App\Models\Billing;
use App\Models\Card;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Coworking;
use App\Models\CoworkingOpeningHours;
use App\Models\HealthAdvice;
use App\Models\HealthAdviceHasUser;
use App\Models\Photo;
use App\Models\Plan;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\User;
use App\Models\UserVerifyCode;
use App\Observers\AddressObserver;
use App\Observers\AppointmentObserver;
use App\Observers\BillingObserver;
use App\Observers\CardObserver;
use App\Observers\CategoryObserver;
use App\Observers\ContactObserver;
use App\Observers\CoworkingObserver;
use App\Observers\CoworkingOpeningHoursObserver;
use App\Observers\HealthAdviceHasUserObserver;
use App\Observers\HealthAdviceObserver;
use App\Observers\PhotoObserver;
use App\Observers\PlanObserver;
use App\Observers\RoomObserver;
use App\Observers\ScheduleObserver;
use App\Observers\UserObserver;
use App\Observers\UserVerifyCodeObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
      Address::observe(AddressObserver::class);
      Appointment::observe(AppointmentObserver::class);
      Billing::observe(BillingObserver::class);
      Card::observe(CardObserver::class);
      Category::observe(CategoryObserver::class);
      Contact::observe(ContactObserver::class);
      Coworking::observe(CoworkingObserver::class);
      CoworkingOpeningHours::observe(CoworkingOpeningHoursObserver::class);
      HealthAdvice::observe(HealthAdviceObserver::class);
      HealthAdviceHasUser::observe(HealthAdviceHasUserObserver::class);
      Photo::observe(PhotoObserver::class);
      Plan::observe(PlanObserver::class);
      Room::observe(RoomObserver::class);
      Schedule::observe(ScheduleObserver::class);
      User::observe(UserObserver::class);
      UserVerifyCode::observe(UserVerifyCodeObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
