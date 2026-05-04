<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;
use App\Http\Requests\StoreResourceRequest;

class ResourceController extends Controller
{
    public function index()
    {
        return response()->json(Resource::all());
    }

    public function store(StoreResourceRequest $request)
    {
        $resource = Resource::create($request->validated());

        return response()->json([
            'message' => 'Resource created successfully',
            'data' => $resource
        ], 201);
    }

    public function show(Resource $resource)
    {
        return response()->json($resource);
    }

    public function update(StoreResourceRequest $request, Resource $resource)
    {
        $resource->update($request->validated());

        return response()->json([
            'message' => 'Resource updated successfully',
            'data' => $resource
        ]);
    }

    public function destroy(Resource $resource, Request $request)
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $resource->delete();
        return response()->json(['message' => 'Resource deleted successfully']);
    }
}
