<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\DestroyRequest;
use App\Http\Requests\Api\User\IndexRequest;
use App\Http\Requests\Api\User\StoreRequest;
use App\Http\Requests\Api\User\ShowRequest;
use App\Http\Requests\Api\User\UpdateRequest;

class CustomerController extends Controller
{
    public function index(IndexRequest $request)
    {
        return $request->run();
    }

    public function show(ShowRequest $request, $id)
    {
        return $request->run($id);
    }

    public function store(StoreRequest $request)
    {
        return $request->run();
    }

    public function update(UpdateRequest $request)
    {
        return $request->run();
    }

    public function destroy(DestroyRequest $request, $id)
    {
        return $request->run($id);
    }

}
