<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;
use App\Models\Company;

class DepartmentsController extends Controller
{
    /**
     * Get all departments for current company.
     */
    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user->workplace) {
                return BaseResponse::error(null, 'User tidak terkait dengan perusahaan manapun.', 403);
            }

            $departments = Department::where('id_company', $user->workplace->id)->get();

            return BaseResponse::success($departments, 'Daftar departemen berhasil diambil.', 200);

        } catch (\Exception $e) {
            return BaseResponse::error(null, 'Gagal mengambil daftar departemen.', 500);
        }
    }

    /**
     * Create new department.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->workplace) {
                return BaseResponse::error(null, 'User tidak terkait dengan perusahaan manapun.', 403);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'nullable|string',
            ]);

            $department = Department::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'name' => $request->name,
                'location' => $request->location,
                'id_company' => $user->workplace->id,
            ]);

            return BaseResponse::success($department, 'Departemen berhasil dibuat.', 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return BaseResponse::error($e->errors(), 'Validasi gagal.', 422);
        } catch (\Exception $e) {
            return BaseResponse::error(null, 'Gagal membuat departemen.', 500);
        }
    }

    /**
     * Update department.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if (!$user->workplace) {
                return BaseResponse::error(null, 'User tidak terkait dengan perusahaan manapun.', 403);
            }

            $department = Department::where('id', $id)
                ->where('id_company', $user->workplace->id)
                ->first();

            if (!$department) {
                return BaseResponse::error(null, 'Departemen tidak ditemukan atau bukan bagian dari perusahaan Anda.', 404);
            }

            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'location' => 'sometimes|nullable|string',
            ]);

            $department->update($request->only(['name', 'location']));

            return BaseResponse::success($department, 'Departemen berhasil diperbarui.', 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return BaseResponse::error($e->errors(), 'Validasi gagal.', 422);
        } catch (\Exception $e) {
            return BaseResponse::error(null, 'Gagal memperbarui departemen.', 500);
        }
    }

    /**
     * Delete department.
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();

            if (!$user->workplace) {
                return BaseResponse::error(null, 'User tidak terkait dengan perusahaan manapun.', 403);
            }

            $department = Department::where('id', $id)
                ->where('id_company', $user->workplace->id)
                ->first();

            if (!$department) {
                return BaseResponse::error(null, 'Departemen tidak ditemukan atau bukan bagian dari perusahaan Anda.', 404);
            }

            $department->delete();

            return BaseResponse::success(null, 'Departemen berhasil dihapus.', 200);

        } catch (\Exception $e) {
            return BaseResponse::error(null, 'Gagal menghapus departemen.', 500);
        }
    }
}