<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Photo;

class Coworking extends Model
{
    use HasFactory, Uuids;

    protected $appends = [
      'address',
      'contact',
      'photo',
    ];

    protected $fillable = [
      'user_id',
      'name',
      'email',
      'description'
    ];

    protected $hidden = [
      'id',
      'user_id',
      // 'created_at',
      'updated_at'
    ];

    public function partner()
    {
      return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function addresses()
    {
      return $this->morphOne(Address::class, 'model');
    }

    public function photos()
    {
      return $this->morphMany(Photo::class, 'model');
    }

    public function getPhotoAttribute($value){
      return Photo::where('model_type', 'App\Models\CoworkingPhoto')->where('model_id', $this->id)->orderBy('id', 'DESC')->first() ?? $value;
    }

    public function rooms()
    {
      return $this->belongsTo(Room::class, 'coworking_id', 'id');
    }

    public function contacts()
    {
      return $this->morphMany(Contact::class, 'model');
    }

    public function schedule()
    {
      return Schedule::where('user_id', $this->user_id)->first();
    }

    public function openingHours()
    {
      return $this->hasMany(CoworkingOpeningHours::class);
    }

    public function getAddressAttribute()
    {
      return $this->addresses()->first();
    }

    public function getRoomsAttribute()
    {
      return $this->rooms()->paginate(config('settings.paginate_rooms')) ?? [];
    }

    public function getContactAttribute($value)
    {
      return $this->contacts()->first() ?? $value;
    }

}
