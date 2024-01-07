<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;


class ApplicationController extends Controller
{
    public function index()
    {
        //
    }

    public function store(StoreApplicationRequest $request)
    {
        //
    }

    public function show(Application $application)
    {
        //
    }

    public function update(UpdateApplicationRequest $request, Application $application)
    {
        //
    }

    public function destroy(Application $application)
    {
        //
    }
}
