<?php

namespace App\Http\Controllers;

use App\Services\PartnerService;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
  
  public function index(Request $request, PartnerService $service)
  {
    $partners = $service->listAll();
    return $this->response('list', $partners, $partners);
  }
}
