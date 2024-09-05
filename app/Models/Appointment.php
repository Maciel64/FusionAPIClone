<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
      'patient_name',
      'patient_phone',
      'schedule_id',
      'customer_id',
      'room_id',
      'time_init',
      'time_end',
      'time_total',
      'status',
      'value_per_minute',
      'value_total',
      'checkin_at',
      'checkout_at',
      'finished_at',
      'canceled_at',
    ];

    protected $appends = [
      'schedule_uuid',
      'specialist',
      'room',
      // 'coworking_name',
    ];

    protected $hidden = [
      'id',
      'schedule_id',
      'customer_id',
      'room_id',
      // 'created_at',
      // 'updated_at',
    ];

    protected $casts = [
      'time_init' => 'date:Y-m-d H:i',
      'time_end'  => 'date:Y-m-d H:i',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format("Y-m-d H:i:s");
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function getScheduleUuidAttribute($value)
    {
        return $this->schedule()->first()->uuid ?? $value;
    }

    public function getSpecialistAttribute($value)
    {
        $customer = $this->customer()->first();
        if($customer)
          $customer->makeHidden([
            'address', 'schedule', 'birth_date',  'last_access']);
        return $customer ?? $value;
    }

    public function getRoomAttribute($value)
    {
        $room = $this->room()->first();
        if($room)
          $room->makeHidden(['opening_hours','address','categories', 'description']);
        return $room ?? $value;
    }

    // public function getCoworkingNameAttribute($value)
    // {
    //     $room = $this->room()->first();
    //     return $room->coworking_name ?? $value;
    // }
}
