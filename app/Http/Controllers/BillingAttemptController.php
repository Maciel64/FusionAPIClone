<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillingAttemptRequest;
use App\Http\Requests\UpdateBillingAttemptRequest;
use App\Models\BillingAttempt;

class BillingAttemptController extends Controller
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
     * @param  \App\Http\Requests\StoreBillingAttemptRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBillingAttemptRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BillingAttempt  $billingAttempt
     * @return \Illuminate\Http\Response
     */
    public function show(BillingAttempt $billingAttempt)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BillingAttempt  $billingAttempt
     * @return \Illuminate\Http\Response
     */
    public function edit(BillingAttempt $billingAttempt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBillingAttemptRequest  $request
     * @param  \App\Models\BillingAttempt  $billingAttempt
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBillingAttemptRequest $request, BillingAttempt $billingAttempt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BillingAttempt  $billingAttempt
     * @return \Illuminate\Http\Response
     */
    public function destroy(BillingAttempt $billingAttempt)
    {
        //
    }
}
