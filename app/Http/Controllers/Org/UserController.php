<?php

namespace App\Http\Controllers\Org;
use App\Http\Responses\BaseResponse;
use App\Models\Org\Document;
use App\Models\Attendance\CheckClock;
use App\Models\Org\User;
use App\Models\Approval;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class UserController extends Controller
{
    public function deleteUserDocument($userId, $documentId)
    {
        $dokumen = Document::where('id', $documentId)
            ->where('id_user', $userId)
            ->first();

        if (!$dokumen) {
            \Log::error("Dokumen ID $documentId milik user $userId tidak ditemukan.");
            return response()->json(['message' => 'Dokumen tidak ditemukan'], 404);
        }

        try {
            $dokumen->delete();
            return response()->json(['message' => 'Dokumen berhasil dihapus']);
        } catch (\Exception $e) {
            \Log::error('Gagal hapus dokumen: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal menghapus dokumen'], 500);
        }
    }

    // CONTROLLER DASHBOARD UNTUK APPROVAL
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


}
