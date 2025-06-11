<?php

namespace App\Http\Controllers\Org;
use App\Http\Controllers\Controller;
use App\Http\Responses\BaseResponse;
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

        // Check subscription seat limit
        $user = Auth::user();
        if (!$user->workplace) {
            return BaseResponse::error(null, 'User tidak terkait dengan perusahaan manapun.', 403);
        }

        $subscription = $user->workplace->subscription;
        if (!$subscription) {
            return BaseResponse::error(null, 'Perusahaan tidak memiliki langganan aktif.', 403);
        }

        // Count active employees
        $activeEmployees = Employee::whereHas('user', function ($query) use ($user) {
            $query->where('id_workplace', $user->workplace->id);
        })->where('employment_status', 'active')->count();

        if ($activeEmployees >= $subscription->seats) {
            return BaseResponse::error([
                'current_seats' => $activeEmployees,
                'max_seats' => $subscription->seats,
                'subscription_id' => $subscription->id
            ], 'Jumlah karyawan telah mencapai batas maksimum. Silakan upgrade langganan Anda.', 403);
        }

        $employee = Employee::create($request->all());
        return BaseResponse::success($employee, 'Karyawan berhasil ditambahkan', 201);
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

    public function getEmployeeBasedCompany()
    {
        try {
            $user = Auth::user();

            if (!$user->workplace) {
                return BaseResponse::error(null, 'User tidak terkait dengan perusahaan manapun.', 403);
            }

            // Ambil semua employee yang terkait dengan perusahaan user saat ini
            $employees = Employee::whereHas('user', function ($query) use ($user) {
                $query->where('id_workplace', $user->workplace->id);
            })->with(['user', 'position'])->get();

            return BaseResponse::success($employees, 'Daftar karyawan berhasil diambil', 200);

        } catch (\Exception $e) {
            return BaseResponse::error(null, 'Gagal mengambil daftar karyawan', 500);
        }
    }

    public function getEmployee(){
        try {
            $user = Auth::user();

            if (!$user->workplace) {
                return BaseResponse::error(null, 'User tidak terkait dengan perusahaan manapun.', 403);
            }

            $employees = Employee::whereHas('user', function ($query) use ($user) {
                $query->where('id_workplace', $user->workplace->id);
            })->with(['user', 'position'])->get();

            $total = $employees->count();
            $active = $employees->where('employment_status','active')->count();
            $inactive = $employees->where('employment_status','inactive')->count();
            $newEmployees = $employees->filter(fn($e) => $e->created_at >= now()->subDays(30))->count();

            $data = [
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive,
                'new_employees' => $newEmployees,
                'last_updated' => now()->format('d F Y H:i'),
            ];

            return BaseResponse::success($data, 'Statistik karyawan berhasil diambil', 200);

        } catch (\Exception $e) {
            return BaseResponse::error(null, 'Gagal mengambil statistik karyawan',500);
        }
    }

    public function getEmployeeContractStats(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->workplace) {
                return BaseResponse::error(null, 'User tidak terkait dengan perusahaan manapun.', 403);
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
                ['label' => 'Tetap', 'total' => $employees->where('tipe_kontrak', 'Tetap')->count()],
                ['label' => 'Kontrak', 'total' => $employees->where('tipe_kontrak', 'Kontrak')->count()],
                ['label' => 'Lepas', 'total' => $employees->where('tipe_kontrak', 'Lepas')->count()],
            ];

            $data = [
                'data' => $stats,
                'selected_month' => "$year-$month",
                'last_updated' => now()->format('d F Y H:i'),
            ];

            return BaseResponse::success($data, 'Statistik kontrak karyawan berhasil diambil', 200);

        } catch (\Exception $e) {
            return BaseResponse::error(null, 'Gagal mengambil statistik kontrak karyawan', 500);
        }
    }

    public function getEmployeeStatusStats(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->workplace) {
                return BaseResponse::error(null, 'User tidak terkait dengan perusahaan manapun.', 403);
            }

            $month = $request->query('month', date('m'));
            $year = $request->query('year', date('Y'));

            $employees = Employee::whereHas('user', function ($query) use ($user) {
                $query->where('id_workplace', $user->workplace->id);
            })
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

            $statusStat = [
                ['label' => 'Aktif', 'total' => $employees->where('employment_status', 'active')->count()],
                ['label' => 'Baru', 'total' => $employees->filter(fn($e) => $e->created_at >= now()->subDays(30))->count()],
                ['label' => 'Tidak Aktif', 'total' => $employees->where('employment_status', 'inactive')->count()],
            ];

            $data = [
                'data' => $statusStat,
                'selected_month' => "$year-$month",
                'last_updated' => now()->format('d F Y H:i'),
            ];

            return BaseResponse::success($data, 'Statistik status karyawan berhasil diambil', 200);

        } catch (\Exception $e) {
            return BaseResponse::error(null, 'Gagal mengambil statistik status karyawan', 500);
        }
    }
// ===================================================================================================
// Break Points Untuk Controller Employee ============================================================
// ===================================================================================================
    public function getEmployeeDashboard()
    {
        try {
            $user = Auth::user();
            $employee = Employee::where('id_user', $user->id)
                ->with(['user', 'position'])
                ->first();

            if (!$employee) {
                return BaseResponse::error(null, 'Employee data not found', 404);
            }

            $data = [
                'employee' => $employee,
                'attendance_today' => $this->getTodayAttendance($employee->id),
                'payroll_summary' => $this->getPayrollSummary($employee->id),
                'last_updated' => now()->format('d F Y H:i'),
            ];

            return BaseResponse::success($data, 'Employee dashboard data retrieved successfully', 200);
        } catch (\Exception $e) {
            return BaseResponse::error(null, 'Failed to retrieve employee dashboard data', 500);
        }
    }

    public function getEmployeeProfile()
    {
        try {
            $user = Auth::user();
            $employee = Employee::where('id_user', $user->id)
                ->with(['user', 'position'])
                ->first();

            if (!$employee) {
                return BaseResponse::error(null, 'Employee data not found', 404);
            }

            return BaseResponse::success($employee, 'Employee profile retrieved successfully', 200);
        } catch (\Exception $e) {
            return BaseResponse::error(null, 'Failed to retrieve employee profile', 500);
        }
    }

    public function getEmployeeAttendance()
    {
        try {
            $user = Auth::user();
            $employee = Employee::where('id_user', $user->id)->first();

            if (!$employee) {
                return BaseResponse::error(null, 'Employee data not found', 404);
            }

            // TODO: Implement actual attendance data retrieval
            $attendance = [
                'today' => [
                    'status' => 'present',
                    'check_in' => '08:00',
                    'check_out' => '17:00',
                ],
                'monthly_summary' => [
                    'present' => 20,
                    'absent' => 2,
                    'late' => 1,
                ],
            ];

            return BaseResponse::success($attendance, 'Employee attendance data retrieved successfully', 200);
        } catch (\Exception $e) {
            return BaseResponse::error(null, 'Failed to retrieve employee attendance data', 500);
        }
    }

    public function getEmployeePayroll()
    {
        try {
            $user = Auth::user();
            $employee = Employee::where('id_user', $user->id)->first();

            if (!$employee) {
                return BaseResponse::error(null, 'Employee data not found', 404);
            }

            // TODO: Implement actual payroll data retrieval
            $payroll = [
                'current_month' => [
                    'basic_salary' => 5000000,
                    'allowances' => 1000000,
                    'deductions' => 500000,
                    'net_salary' => 5500000,
                ],
                'payment_history' => [
                    [
                        'month' => 'January 2024',
                        'amount' => 5500000,
                        'status' => 'paid',
                    ],
                ],
            ];

            return BaseResponse::success($payroll, 'Employee payroll data retrieved successfully', 200);
        } catch (\Exception $e) {
            return BaseResponse::error(null, 'Failed to retrieve employee payroll data', 500);
        }
    }

    private function getTodayAttendance($employeeId)
    {
        // TODO: Implement actual attendance check
        return [
            'status' => 'present',
            'check_in' => '08:00',
            'check_out' => '17:00',
        ];
    }

    private function getPayrollSummary($employeeId)
    {
        // TODO: Implement actual payroll summary
        return [
            'current_salary' => 5500000,
            'last_payment' => '2024-01-31',
            'payment_status' => 'paid',
        ];
    }
}
