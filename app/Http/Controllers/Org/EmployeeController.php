<?php

namespace App\Http\Controllers\Org;
use App\Http\Controllers\Controller;
use App\Models\Org\Employee;
use App\Models\Org\Company;
use App\Models\Org\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


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

    public function getEmployee(){
        $user = Auth::user();

        if (!$user->workplace) {
            return response()->json([
                'message' => 'User tidak terkait dengan perusahaan manapun.'
            ], 403);
        }

        $employees = Employee::whereHas('user', function ($query) use ($user) {
            $query->where('id_workplace', $user->workplace->id);
        })->with(['user', 'position'])->get();

        $total = $employees->count();
        $active = $employees->where('employment_status','active')->count();
        $inactive = $employees->where('employment_status','inactive')->count();
        $newEmployees = $employees->filter(function ($employee) {
            return $employee->created_at >= Carbon::now()->subDays(30);
        })->count();

        return response()->json([
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'new_employees' => $newEmployees,
            'last_updated' => now()->format('d F Y H:i'),
        ]);
    }

    public function getEmployeeContractStats(Request $request)
    {
        $user = Auth::user();

        if (!$user->workplace) {
            return response()->json([
                'message' => 'User tidak terkait dengan perusahaan manapun.'
            ], 403);
        }

        $month = $request->query('month', date('m'));
        $year = $request->query('year', date('Y'));

        $employees = Employee::whereHas('user', function ($query) use ($user) {
            $query->where('id_workplace', $user->workplace->id);
        })
        ->whereMonth('created_at', $month)
        ->whereYear('created_at', $year)
        ->get();

        $stats = [
            [
                'label' => 'Tetap',
                'total' => $employees->where('tipeKontrak', 'Tetap')->count(),
            ],
            [
                'label' => 'Kontrak',
                'total' => $employees->where('tipeKontrak', 'Kontrak')->count(), // asumsi kolom tipeKontrak ada
            ],
            [
                'label' => 'Lepas',
                'total' => $employees->where('tipeKontrak', 'Lepas')->count(), // nanti menyesuaikan 
            ],
        ];

        return response()->json([
            'data' => $stats,
            'selected_month' => "$year-$month",
            'last_updated' => now()->format('d F Y H:i'),
        ]);
    }

    public function getEmployeeStatusStats(Request $request)
    {
        $user = Auth::user();

        if (!$user->workplace) {
            return response()->json([
                'message' => 'User tidak terkait dengan perusahaan manapun.'
            ], 403);
        }

        // Ambil bulan dan tahun dari query param (default: bulan ini)
        $month = $request->query('month', date('m'));
        $year = $request->query('year', date('Y'));

        // Ambil semua employee berdasarkan company user saat ini
        $employees = Employee::whereHas('user', function ($query) use ($user) {
            $query->where('id_workplace', $user->workplace->id);
        })
        ->whereMonth('created_at', $month)
        ->whereYear('created_at', $year)
        ->get();

        $statusStat = [
            [
                'label' => 'Aktif', 
                'total' => $employees->where('employment_status', 'active')->count(),
            ],
            [
                'label' => 'Baru', 
                'total' => $employees->filter(function ($employee) {
                    return $employee->created_at >= Carbon::now()->subDays(30);
                })->count(),
            ],
            [
                'label' => 'Tidak Aktif', 
                'total' => $employees->where('employment_status', 'inactive')->count(),
            ],
        ];

        return response()->json([
            'data' => $statusStat,
            'selected_month' => "$year-$month",
            'last_updated' => now()->format('d F Y H:i'),
        ]);
    }
}
