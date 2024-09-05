<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Photo;

class Room extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
      'name',
      'number',
      'price_per_minute',
      'description',
      'coworking_id',
      'operating_hours',
      'fixed',
      'appointment_restriction_hour',
      'appointment_type'
    ];

    protected $appends = [
      'opening_hours',
      'schedule_id',
      'schedule_uuid',
      'address',
      'categories',
      'photos',
      'photo',
      'contacts',
      'coworking_name',
      'partner_name',
      'partner_uuid',
      'coworking_uuid',
      'facilities',
    ];

    protected $hidden = [
      'id',
      'schedule_id',
      'coworking_id',
      'coworking',
      'created_at',
      'updated_at',
      'pivot',
    ];

    protected $with = [
      
    ];

    public function categories()
    {
      return $this->belongsToMany(Category::class, 'categories_has_rooms');
    }

    public function facilities()
    {
        return $this->hasMany(FacilityRoom::class, 'room_id', 'id');
    }
    
    
    public function coworking()
    {
      return $this->hasOne(Coworking::class, 'id', 'coworking_id');
    }

    public function partner()
    {
      $coworking = $this->coworking();
      return $coworking->partner();
    }

    public function photos()
    {
      return $this->morphMany(Photo::class, 'model')->get();
    }

    public function getContactsAttribute()
    {
      return $this->coworking->contacts;
    }
    
    public function getFacilitiesAttribute()
    {
        $facilities = $this->facilities()->get();

        // Transforme a coleção em um array com o formato desejado
        return $facilities->map(function ($facility) {
            return [
                'id' => $facility->facility->id,
                'uuid' => $facility->facility->uuid,
                'name' => $facility->facility->name,
                'created_at' => $facility->facility->created_at,
                'updated_at' => $facility->facility->updated_at,
            ];
        });
    }

    public function schedule()
    {
      $coworking = $this->coworking()->first();
      $schedule  = $coworking->schedule();
      return ($schedule instanceof Schedule) ? $schedule : null;
    }

    public function getOpeningHoursAttribute()
    {
      return $this->coworking->openingHours;
    }

    public function getOpeningHoursByDayAttribute()
    {
      return $this->coworking->opening_hours;
    }    

    public function getAddressAttribute()
    {
      return $this->coworking->address;
    }

    public function getScheduleIdAttribute($value)
    {
      $userId = $this->coworking->user_id;
      return (User::find($userId))->schedule->id ?? $value;
    }

    public function getScheduleUuidAttribute($value)
    {
      return $this->schedule()->uuid ?? $value;
    }

    public function getCategoriesAttribute($value)
    {
      return $this->categories()->get() ?? $value;
    }

    public function getPhotosAttribute($value)
    {
      return $this->photos() ?? $value;
    }

    public function getPhotoAttribute($value){
      return Photo::where('model_type', 'App\Models\RoomPhoto')->where('model_id', $this->id)->orderBy('id', 'DESC')->first() ?? $value;
    }

    public function getCoworkingNameAttribute()
    {
      return $this->coworking->name;
    }

    public function getCoworkingUuidAttribute()
    {
      return $this->coworking->uuid;
    }

    public function getPartnerNameAttribute()
    {
      return $this->coworking->partner->name;
    }

    public function getPartnerUuidAttribute()
    {
      return $this->coworking->partner->uuid;
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
