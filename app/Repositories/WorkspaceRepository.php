<?php

namespace App\Repositories;

use App\Models\Workspace;

class WorkspaceRepository extends BaseRepository
{
  public function __construct()
  {
    parent::__construct(Workspace::class);
  }
}