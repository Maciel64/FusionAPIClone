<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
      'name',
      'description',
    ];

    protected $hidden = [
      'id',
      'created_at',
      'updated_at',
      'pivot'
    ];

    public function rooms()
    {
      return $this->belongsToMany(Room::class, 'categories_has_rooms');
    }
}
