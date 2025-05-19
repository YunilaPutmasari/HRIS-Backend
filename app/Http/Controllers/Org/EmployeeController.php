<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Org\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class EmployeeController extends Controller
{
    // ✅ READ - Ambil semua employee
    public function index()
    {

        $employees = Employee::with(['user', 'position'])->get();
        return response()->json($employees);
    }

    // ✅ CREATE - Tambah employee baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required|uuid',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'address' => 'required|string',
            'id_position' => 'nullable|uuid',
            'employment_status' => 'in:active,inactive,resign',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employee = Employee::create($request->all());
        return response()->json($employee, 201);
    }

    // ✅ SHOW - Ambil 1 employee berdasarkan id
    public function show($id)
    {
        $employee = Employee::with(['user', 'position'])->find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }
        return response()->json($employee);
    }

    // ✅ UPDATE - Ubah data employee
    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->update($request->all());
        return response()->json($employee);
    }

    // ✅ DELETE - Hapus (soft delete)
    public function destroy($id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->delete();
        return response()->json(['message' => 'Employee deleted successfully']);
    }
}
