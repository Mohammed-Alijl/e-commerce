<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Size\DestroyRequest;
use App\Http\Requests\Api\Size\IndexRequest;
use App\Http\Requests\Api\Size\ShowRequest;
use App\Http\Requests\Api\Size\StoreRequest;
use App\Http\Requests\Api\Size\UpdateRequest;

class SizeController extends Controller
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

    public function update(UpdateRequest $request, $id)
    {
        return $request->run($id);
    }

    public function destroy(DestroyRequest $request, $id)
    {
        return $request->run($id);
    }

}
