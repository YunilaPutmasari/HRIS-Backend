<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Responses\BaseResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Org\Employee;
use App\Models\Attendance\CheckClock;
use App\Models\Attendance\CheckClockSetting;
use App\Models\Attendance\CheckClockSettingTime;
use App\Models\Org\Document;
use App\Models\Org\Company;
use App\Models\Org\User;
use App\Models\Approval;
use Carbon\Carbon;


class DashboardController extends Controller
{
    // ==========================================
    // MANAGER DASHBOARD
    public function getEmployee()
    {
        try {
            $user = Auth::user();

            if (!$user->workplace) {
                return BaseResponse::error(null, 'User tidak terkait dengan perusahaan manapun.', 403);
            }

            $employees = Employee::whereHas('user', function ($query) use ($user) {
                $query->where('id_workplace', $user->workplace->id);
            })->with(['user', 'position'])->get();

            $total = $employees->count();
            $active = $employees->where('employment_status', 'active')->count();
            $inactive = $employees->where('employment_status', 'inactive')->count();
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
            return BaseResponse::error(null, 'Gagal mengambil statistik karyawan', 500);
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
        return BaseResponse::success($data, 'Statistik status karyawan berhasil diambil', 200);

    }

    public function getRecentApprovals(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return BaseResponse::error(null, 'User not Authenticated', 401);
            }

            $companies = $user->companies()->get();
            $companyIds = $companies->pluck('id')->toArray();
            
            if (empty($companyIds)) {
                return BaseResponse::success([], 'No Company Found', 200);
            }
            // Get users in the same companies
            $userIds = User::whereIn('id_workplace', $companyIds)
                ->pluck('id')
                ->toArray();

            if (empty($userIds)) {
                return BaseResponse::success([], 'No User Found in this Company', 200);
            }

            $approvals = Approval::whereIn('id_user', $userIds)
                ->with([
                    'employee',
                    'employee.position'
                ])
                ->latest()
                ->get()
                ->map(function ($approval) {
                    return [
                        'id' => $approval->id,
                        'employee_name' => $approval->employee ? $approval->employee->first_name . ' ' . $approval->employee->last_name : 'Tidak diketahui',
                        'type' => $approval->request_type,
                        'status' => $approval->status,
                        'created_at' => $approval->created_at
                    ];
                });

            return BaseResponse::success([
                'data' => $approvals
            ], 'Recent approvals berhasil diambil', 200);
        } catch (\Throwable $e) {
            \Log::error('Error fetching recent approvals: ' . $e->getMessage());

            return BaseResponse::error(null, 'Terjadi kesalahan server', 500);
        }
    }
    
    public function getAttendanceSummary(Request $request)
    {
        try {
            $date = $request->input('date') ?? Carbon::today()->toDateString(); // default: hari ini
            $startOfDay = Carbon::parse($date)->startOfDay();
            $endOfDay = Carbon::parse($date)->endOfDay();

            // Ambil semua user karyawan
            $users = User::has('employee')->get();
            $userIds = $users->pluck('id');

            // CheckClock hari ini
            $checkClocks = CheckClock::whereBetween('clock_in', [$startOfDay, $endOfDay])
                ->whereIn('id_user', $userIds)
                ->get()
                ->groupBy('id_user');

            // Approval hari ini
            $approvals = Approval::whereIn('id_user', $userIds)
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)
                ->get()
                ->groupBy('id_user');

            $summary = [
                'on-time' => 0,
                'late' => 0,
                'sick' => 0,
                'permit' => 0,
                'absent' => 0,
                'total_employee' => $users->count(),
                'date' => $date,
            ];

            foreach ($users as $user) {
                $clock = $checkClocks->get($user->id)?->first();
                $approval = $approvals->get($user->id)?->first();

                if ($approval) {
                    if ($approval->request_type === 'sick') {
                        $summary['sick']++;
                    } elseif ($approval->request_type === 'permit') {
                        $summary['permit']++;
                    }
                } elseif ($clock) {
                    match ($clock->status) {
                        'on-time' => $summary['on-time']++,
                        'late' => $summary['late']++,
                        'sick' => $summary['sick']++,
                        'permit' => $summary['permit']++,
                        default => $summary['absent']++, // jika status tidak dikenali
                    };
                }
                
            }

            return BaseResponse::success($summary, "Ringkasan absensi untuk tanggal $date");

        } catch (\Throwable $e) {
            // Log detail error
            \Log::error('Error in getAttendanceSummary', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_date' => $request->input('date'),
                'user' => $request->user()?->id ?? 'unauthenticated'
            ]);
            return BaseResponse::error(null, 'Terjadi kesalahan saat mengambil ringkasan absensi');
        }
    }

    // ==========================================
    // EMPLOYEE DASHBOARD
    public function getEmployeeData()
    {
        try {
            $user = Auth::user();
            
            $companyId = $user->workplace->id;

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Company ID not found for this employee'
                ], 404);
            }
            // Ambil semua check clock bulan ini
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();

            $checkClocks = CheckClock::where('id_user', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->with('checkClockSettingTime')
                ->get();

            // Hitung total work hours
            $totalWorkHours = 0;
            $totalWorkSeconds = 0;
            foreach ($checkClocks as $cc) {
                if ($cc->clock_in && $cc->clock_out) {
                    $date = Carbon::parse($cc->created_at)->toDateString();
                    // $workStart = Carbon::parse($cc->clock_in);
                    // $workEnd = Carbon::parse($cc->clock_out);
                    $in = Carbon::createFromFormat('Y-m-d H:i:s', "$date {$cc->clock_in}");
                    $out = Carbon::createFromFormat('Y-m-d H:i:s', "$date {$cc->clock_out}");

                    // Hitung break time
                    $breakDuration = 0;
                    if ($cc->break_start && $cc->break_end) {
                        $breakStart = Carbon::createFromFormat('Y-m-d H:i:s', "$date {$cc->break_start}");
                        $breakEnd = Carbon::createFromFormat('Y-m-d H:i:s', "$date {$cc->break_end}");
                        $breakDuration = $breakStart->diffInSeconds($breakEnd);
                    }

                    $totalWorkSeconds += $in->diffInSeconds($out) - $breakDuration;
                }
            }

            $totalWorkHours = round($totalWorkSeconds / 3600, 2);

            // Hitung statistik presensi
            $stats = [
                'on_time' => $checkClocks->whereIn('status', 'on-time')->count(),
                'late' => $checkClocks->where('status', 'late')->count(),
                'sick' => $checkClocks->where('status', 'sick')->count(),
                'permit' => $checkClocks->where('status', 'permit')->count(),
                'leave' => $checkClocks->where('status', 'leave')->count(),
            ];

            // Jadwal kerja minggu ini
            // $scheduleThisWeek = CheckClock::whereHas('checkClockSetting.checkClockSettingTime', function ($query) use ($user) {
            //     $query->where('id_user', $user->id);
            // })->get();
            $checkClockSetting = CheckClockSetting::where('id_company', $companyId)->first();

            if (!$checkClockSetting) {
                return response()->json([
                    'success' => false,
                    'message' => 'No check clock setting found for this company'
                ], 404);
            }

            $scheduleThisWeek = CheckClockSettingTime::where('id_ck_setting', $checkClockSetting->id)
            ->whereIn('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'])
            ->orderByRaw("CASE
                WHEN day = 'Monday' THEN 1
                WHEN day = 'Tuesday' THEN 2
                WHEN day = 'Wednesday' THEN 3
                WHEN day = 'Thursday' THEN 4
                WHEN day = 'Friday' THEN 5
                ELSE 6 END")
            ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'total_work_hours' => round($totalWorkHours, 2),
                    'attendance_stats' => $stats,
                    'schedule_this_week' => $scheduleThisWeek,
                    'daily_attendance' => $this->generateDailyAttendanceChart($checkClocks),
                    'last_updated' => now()->format('Y-m-d H:i:s'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard data: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function generateDailyAttendanceChart($checkClocks)
    {
        $chartData = [];

        foreach ($checkClocks as $cc) {
            $date = Carbon::parse($cc->created_at)->toDateString();
            $hours = 0;

            if ($cc->clock_in && $cc->clock_out) {
                $workStart = Carbon::parse($cc->clock_in);
                $workEnd = Carbon::parse($cc->clock_out);
                $hours = $workStart->diffInHours($workEnd);
            }

            $chartData[] = [
                'date' => $date,
                'hours' => $hours,
            ];
        }

        return $chartData;
    }

}
