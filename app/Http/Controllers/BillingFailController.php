<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillingFailRequest;
use App\Http\Requests\UpdateBillingFailRequest;
use App\Models\BillingFail;

class BillingFailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBillingFailRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBillingFailRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BillingFail  $billingFail
     * @return \Illuminate\Http\Response
     */
    public function show(BillingFail $billingFail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BillingFail  $billingFail
     * @return \Illuminate\Http\Response
     */
    public function edit(BillingFail $billingFail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBillingFailRequest  $request
     * @param  \App\Models\BillingFail  $billingFail
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBillingFailRequest $request, BillingFail $billingFail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BillingFail  $billingFail
     * @return \Illuminate\Http\Response
     */
    public function destroy(BillingFail $billingFail)
    {
        //
    }
}
