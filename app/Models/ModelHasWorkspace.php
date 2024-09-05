<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ModelHasWorkspace extends MorphPivot
{
    use HasFactory;

    protected $table = 'models_has_workspace';

    protected $fillable = [
      'model_id',
      'model_type',
      'workspace_id',
    ];
}
