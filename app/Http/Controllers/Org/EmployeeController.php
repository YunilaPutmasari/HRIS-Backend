<?php

namespace App\Http\Controllers\org;
use App\Http\Controllers\Controller;
use App\Models\Org\Employee;
use App\Models\Org\Document;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use Illuminate\Support\Facades\Validator;


class EmployeeController extends Controller
{
    // ✅ READ - Ambil semua employee
    public function index(Request $request)
    {
        try {
            $query = Employee::with('position', 'user');

            if ($request->has('id')) {
                $query->where('id', $request->query('id'));
            }

            $employees = $query->get();

            return EmployeeResource::collection($employees);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    // // ✅ CREATE - Tambah employee baru
    // public function store(StoreEmployeeRequest $request)
    // {
    //     $employee = Employee::create($request->validated());
    //     return response()->json($employee, 201);
    // }
    // Fungsi store untuk menyimpan employee baru
    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();

        // Simpan avatar jika ada
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Simpan employee ke database
        $employee = Employee::create($data);

        // Cek jika ada file dokumen
        if ($request->hasFile('dokumen')) {
            $files = $request->file('dokumen');

            // Jika hanya satu file dikirim
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                $path = $file->store('documents', 'public');

                Document::create([
                    'id_user' => $employee->id_user, // hubungan ke tb_user
                    'type' => 'other', // default, bisa disesuaikan
                    'name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                ]);
            }
        }

        return response()->json($employee->load('documents'), 201);
    }


    // ✅ SHOW - Ambil 1 employee berdasarkan id
    public function show($id)
    {
        $employee = Employee::with(['user', 'position', 'documents'])->find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }
        return response()->json($employee);
    }

    // ✅ UPDATE - Ubah data employee
    public function update(UpdateEmployeeRequest $request, $id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->update($request->validated());
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
