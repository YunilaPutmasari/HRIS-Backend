<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Responses\BaseResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Org\Position;
use App\Models\Org\Department;
use App\Models\Org\Company;

class DeptPositionsController extends Controller
{
    /**
     * Get all positions based on department and company of the logged-in user.
     */
    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user->workplace) {
                return BaseResponse::error(null, 'User tidak terkait dengan perusahaan manapun.', 403);
            }

            $company = $user->workplace;
            if (!$company) {
                return BaseResponse::error(null, 'User tidak memiliki workplace', 403);
            }

            // Ambil semua department milik perusahaan ini
            $departmentIds = Department::where('id_company', $company->id)
                ->pluck('id');

            if ($departmentIds->isEmpty()) {
                return BaseResponse::success([], 'Perusahaan belum memiliki department');
            }
            
            // Ambil semua department yang terkait dengan perusahaan user
            $positions = Position::whereIn('id_department', $departmentIds)
            ->with(['department:id,name']) // Load hanya field penting dari department
            ->get();

            // Format response untuk FE
            $formatted = $positions->map(function ($position) {
                return [
                    'id' => $position->id,
                    'name' => $position->name,
                    'level' => $position->level,
                    'gaji' => $position->gaji,
                    'department' => optional($position->department)->only(['id', 'name']),
                ];
            });

            return BaseResponse::success($formatted, 'Daftar posisi berhasil diambil.', 200);

        } catch (\Exception $e) {
            return BaseResponse::error(null, 'Gagal mengambil daftar posisi.', 500);
        }
    }

    /**
     * Get all positions under a specific department (and ensure it belongs to the user's company).
     */
    public function getByDepartment($idDepartment)
    {
        try {
            $user = Auth::user();

            if (!$user->workplace) {
                return BaseResponse::error(null, 'User tidak terkait dengan perusahaan manapun.', 403);
            }

            // Validasi bahwa department milik perusahaan user
            $department = Department::where('id', $idDepartment)
                ->whereHas('company', function ($q) use ($user) {
                    $q->where('id', $user->workplace->id);
                })
                ->first();

            if (!$department) {
                return BaseResponse::error(null, 'Departemen tidak ditemukan atau bukan bagian dari perusahaan Anda.', 404);
            }

            // Ambil semua position yang terkait dengan department ini
            $positions = Position::where('id_department', $idDepartment)->get();

            return BaseResponse::success($positions, 'Daftar posisi berhasil diambil.', 200);

        } catch (\Exception $e) {
            return BaseResponse::error($e->getMessage(), 'Gagal mengambil daftar posisi.', 500);
        }
    }

    /**
     * Get detail position by ID
     */
    public function show($id)
    {
        try {
            $user = Auth::user();

            if (!$user->workplace) {
                return BaseResponse::error(null, 'User tidak terkait dengan perusahaan manapun.', 403);
            }

            // Ambil position beserta department dan company
            $position = Position::whereHas('department', function ($query) use ($user) {
                $query->where('id_company', $user->workplace->id);
            })
                ->with(['department.company'])
                ->find($id);

            if (!$position) {
                return BaseResponse::error(null, 'Posisi tidak ditemukan atau bukan bagian dari perusahaan Anda.', 404);
            }

            return BaseResponse::success($position, 'Detail posisi berhasil diambil.', 200);

        } catch (\Exception $e) {
            return BaseResponse::error(null, 'Gagal mengambil detail posisi.', 500);
        }
    }

    /**
     * Create new position under a department.
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
                'level' => 'required|integer',
                'gaji' => 'required|numeric',
                'id_department' => 'required|uuid|exists:tb_department,id',
            ]);

            // Validasi bahwa department ini milik company user
            $department = Department::where('id', $request->id_department)
                ->whereHas('company', function ($q) use ($user) {
                    $q->where('id', $user->workplace->id);
                })
                ->first();

            if (!$department) {
                return BaseResponse::error(null, 'Departemen tidak ditemukan atau bukan bagian dari perusahaan Anda.', 404);
            }

            $position = Position::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'name' => $request->name,
                'level' => $request->level,
                'gaji' => $request->gaji,
                'id_department' => $request->id_department,
            ]);

            return BaseResponse::success($position, 'Posisi berhasil dibuat.', 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return BaseResponse::error($e->errors(), 'Validasi gagal.', 422);
        } catch (\Exception $e) {
            return BaseResponse::error(null, 'Gagal membuat posisi.', 500);
        }
    }

    /**
     * Create new position under a specific department by ID in the URL.
     */
    public function storeByDepartment(Request $request, $idDepartment)
    {
        try {
            $user = Auth::user();

            if (!$user->workplace) {
                return BaseResponse::error(null, 'User tidak terkait dengan perusahaan manapun.', 403);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'level' => 'required|integer',
                'gaji' => 'required|numeric',
            ]);

            // Validasi bahwa department ini milik company user
            $department = Department::where('id', $idDepartment)
                ->whereHas('company', function ($q) use ($user) {
                    $q->where('id', $user->workplace->id);
                })
                ->first();

            if (!$department) {
                return BaseResponse::error(null, 'Departemen tidak ditemukan atau bukan bagian dari perusahaan Anda.', 404);
            }

            $position = Position::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'name' => $request->name,
                'level' => $request->level,
                'gaji' => $request->gaji,
                'id_department' => $idDepartment,
            ]);

            return BaseResponse::success($position, 'Posisi berhasil dibuat.', 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return BaseResponse::error($e->errors(), 'Validasi gagal.', 422);
        } catch (\Exception $e) {
            return BaseResponse::error(null, 'Gagal membuat posisi.', 500);
        }
    }

    /**
     * Update existing position.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if (!$user->workplace) {
                return BaseResponse::error(null, 'User tidak terkait dengan perusahaan manapun.', 403);
            }

            $position = Position::where('id', $id)
                ->whereHas('department', function ($q) use ($user) {
                    $q->whereHas('company', function ($qq) use ($user) {
                        $qq->where('id', $user->workplace->id);
                    });
                })
                ->first();

            if (!$position) {
                return BaseResponse::error(null, 'Posisi tidak ditemukan atau bukan bagian dari perusahaan Anda.', 404);
            }

            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'level' => 'sometimes|required|integer',
                'gaji' => 'sometimes|required|numeric',
                'id_department' => 'sometimes|required|uuid|exists:tb_department,id',
            ]);

            $position->update($request->only(['name', 'level', 'gaji', 'id_department']));

            return BaseResponse::success($position, 'Posisi berhasil diperbarui.', 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return BaseResponse::error($e->errors(), 'Validasi gagal.', 422);
        } catch (\Exception $e) {
            return BaseResponse::error(null, 'Gagal memperbarui posisi.', 500);
        }
    }

    /**
     * Delete position.
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();

            if (!$user->workplace) {
                return BaseResponse::error(null, 'User tidak terkait dengan perusahaan manapun.', 403);
            }

            $position = Position::where('id', $id)
                ->whereHas('department', function ($q) use ($user) {
                    $q->whereHas('company', function ($qq) use ($user) {
                        $qq->where('id', $user->workplace->id);
                    });
                })
                ->first();

            if (!$position) {
                return BaseResponse::error(null, 'Posisi tidak ditemukan atau bukan bagian dari perusahaan Anda.', 404);
            }

            $position->delete();

            return BaseResponse::success(null, 'Posisi berhasil dihapus.', 200);

        } catch (\Exception $e) {
            return BaseResponse::error(null, 'Gagal menghapus posisi.', 500);
        }
    }
}