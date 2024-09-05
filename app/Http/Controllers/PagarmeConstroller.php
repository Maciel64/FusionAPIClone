<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PagarmeConstroller extends Controller
{
  public function hook(Request $request)
  {
    Log::info(json_encode($request->all()));
  }
}
