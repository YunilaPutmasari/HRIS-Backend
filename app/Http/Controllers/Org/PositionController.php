<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use App\Models\Org\Position;
use Illuminate\Http\Request;
use App\Http\Requests\PositionRequest;

class PositionController extends Controller
{
    public function index()
    {
        $position = Position::with('department')->paginate(10);
        return response()->json($position);
    }

    public function store(PositionRequest $request)
    {
        $position = Position::create($request->validated());
        return response()->json($position, 201);
    }

    public function show(Position $position)
    {
        return response()->json($position);
    }

    public function update(PositionRequest $request, Position $position)
    {
        $position->update($request->validated());
        return response()->json($position);
    }

    public function destroy(Position $position)
    {
        $position->delete();
        return response()->json(null, 204);
    }
}
