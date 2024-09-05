<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'facility_id'
    ];

    protected $hidden = [
        'id'
    ];

    protected $with = [
        'facility'
    ];
    
    public function facility()
    {
        return $this->hasOne(Facility::class, 'id', 'facility_id');
    }

}
