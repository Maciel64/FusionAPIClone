<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserHasWorkspaceRequest;
use App\Http\Requests\UpdateUserHasWorkspaceRequest;
use App\Models\UserHasWorkspace;

class UserHasWorkspaceController extends Controller
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
     * @param  \App\Http\Requests\StoreUserHasWorkspaceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserHasWorkspaceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserHasWorkspace  $userHasWorkspace
     * @return \Illuminate\Http\Response
     */
    public function show(UserHasWorkspace $userHasWorkspace)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserHasWorkspace  $userHasWorkspace
     * @return \Illuminate\Http\Response
     */
    public function edit(UserHasWorkspace $userHasWorkspace)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserHasWorkspaceRequest  $request
     * @param  \App\Models\UserHasWorkspace  $userHasWorkspace
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserHasWorkspaceRequest $request, UserHasWorkspace $userHasWorkspace)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserHasWorkspace  $userHasWorkspace
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserHasWorkspace $userHasWorkspace)
    {
        //
    }
}
