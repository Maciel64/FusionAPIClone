<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\Uuids;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Uuids, HasRoles, HasPermissions;

    protected $fillable = [
        'name',
        'status', // adimplente, inadimplente
        'description',
        'document_type',
        'document',
        'gender',
        'birth_date',
        'email',
        'password',
        'account_active',
        'account_activated_at',
        'account_deactivated_at',
    ];

    protected $appends = [
        'role_name',
        'photo',
        'address',
        'schedule',
        'advice',
        'subscription',
        'contacts',
    ];

    protected $hidden = [
        'subscription',
        'password',
        'remember_token',
        'id',
        'roles',
        'updated_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'account_activated_at' => 'date'
    ];

    public function sendPasswordResetNotification($token)
    {

        $this->notify(new ResetPasswordNotification($token));
     
    }

    
    public function isDefaulter()
    {
      return $this->status == 'inadimplente';
    }

    public function card(){
      return $this->hasOne(Card::class)->where('is_default', true) ?? null;
    }

    public function contacts(){
      return $this->morphMany(Contact::class, 'model');
    }

    public function subscription(){
      return $this->hasOne(Subscription::class);
    }

    public function healthAdvice(){
      return $this->hasOne(HealthAdviceHasUser::class);
    }

    public function addresses(){
      return $this->morphOne(Address::class, 'model');
    }

    public function photos(){
      return $this->morphOne(Photo::class, 'model');
    }

    public function types(){
      return $this->hasMany(Type::class);
    }

    public function schedules(){
      return $this->hasOne(Schedule::class);
    }

    public function getPhotoAttribute(){
      return $this->photos()->first();
    }

    public function getAddressAttribute(){
      return $this->addresses()->first();
    }

    public function getRoleNameAttribute($value){
      return $this->roles()->first()->name ?? $value;
    }

    public function getScheduleAttribute($value){
      return $this->schedules()->first() ?? $value;
    }

    public function getAdviceAttribute($value){
      return $this->healthAdvice()->first() ?? $value;
    }

    public function getSubscriptionAttribute($value){
      return $this->subscription()->first() ?? $value;
    }

    public function getContactsAttribute($value){
      return $this->contacts()->get() ?? $value;
    }
}
