<?php

namespace App\Http\Controllers\Overtime;

use App\Http\Controllers\Controller;
use App\Http\Requests\OvertimeStoreRequest;
use App\Http\Requests\OvertimeUpdateRequest;
use App\Http\Responses\BaseResponse;
use App\Models\Overtime\Overtime;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OvertimeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Overtime::with(['user', 'employee', 'setting', 'approver']);

            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('overtime_date', [$request->start_date, $request->end_date]);
            }

            if ($request->has('employee_name')) {
                $query->whereHas('employee', function ($q) use ($request) {
                    $q->where('first_name', 'like', '%' . $request->employee_name . '%')
                        ->orWhere('last_name', 'like', '%' . $request->employee_name . '%');
                });
            }

            $overtimes = $query->latest()->get();

            return BaseResponse::success($overtimes, 'Overtime records retrieved successfully');
        } catch (\Throwable $e) {
            $errorMessage = 'Failed to retrieve overtime records: ' . $e->getMessage();
            return BaseResponse::error(null, $errorMessage, 500);
        }
    }

    public function store(OvertimeStoreRequest $request)
    {
        $validated = $request->validated();

        // --- VALIDASI PENTING: Cek tumpang tindih dengan jam kerja ---
        // TODO: Implementasikan logika untuk mendapatkan jadwal kerja karyawan pada tanggal tersebut.
        // Anda perlu menyesuaikan ini dengan cara Anda menyimpan data shift kerja.
        $workSchedule = $this->getWorkScheduleForUser($validated['id_user'], $validated['overtime_date']);

        if ($this->isOverlapping($validated['start_time'], $validated['end_time'], $workSchedule['start'], $workSchedule['end'])) {
            return BaseResponse::error(['time_conflict' => 'Overtime schedule conflicts with regular work hours.'], 'Validation Error', 422);
        }
        // --- AKHIR VALIDASI PENTING ---

        try {
            $overtime = Overtime::create($validated);
            return BaseResponse::success($overtime, 'Overtime record created successfully', 201);
        } catch (\Throwable $e) {
            return BaseResponse::error(null, 'Failed to create overtime record: ' . $e->getMessage(), 500);
        }
    }

    public function show(Overtime $overtime)
    {
        return BaseResponse::success($overtime->load(['user', 'employee', 'setting', 'approver']), 'Overtime record retrieved successfully');
    }

    public function update(OvertimeUpdateRequest $request, Overtime $overtime)
    {
        $validated = $request->validated();
        try {
            $overtime->update($validated);
            return BaseResponse::success($overtime, 'Overtime record updated successfully');
        } catch (\Throwable $e) {
            return BaseResponse::error(null, 'Failed to update overtime record: ' . $e->getMessage(), 500);
        }
    }

    public function approve(Request $request, Overtime $overtime)
    {
        try {
            $overtime->update([
                'status' => 'approved',
                'approved_by' => $request->user()->id,
            ]);
            return BaseResponse::success($overtime, 'Overtime has been approved');
        } catch (\Throwable $e) {
            return BaseResponse::error(null, 'Failed to approve overtime: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Reject the specified overtime request.
     */
    public function reject(Request $request, Overtime $overtime)
    {
        try {
            $overtime->update([
                'status' => 'rejected',
                'approved_by' => $request->user()->id,
            ]);
            return BaseResponse::success($overtime, 'Overtime has been rejected');
        } catch (\Throwable $e) {
            return BaseResponse::error(null, 'Failed to reject overtime: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Overtime $overtime)
    {
        try {
            $overtime->delete();
            return BaseResponse::success(null, 'Overtime record deleted successfully');
        } catch (\Throwable $e) {
            return BaseResponse::error(null, 'Failed to delete overtime record: ' . $e->getMessage(), 500);
        }
    }

    private function getWorkScheduleForUser(string $userId, string $date): array
    {
        // TODO: Ganti dengan logika sebenarnya untuk mengambil jadwal kerja dari database atau sistem lain.
        // Contoh: $schedule = WorkShift::where('user_id', $userId)->where('date', $date)->first();
        // Untuk sekarang, kita asumsikan jam kerja standar 09:00 - 17:00.
        return ['start' => '09:00', 'end' => '17:00'];
    }

    private function isOverlapping(string $overtimeStart, string $overtimeEnd, string $workStart, string $workEnd): bool
    {
        $overtimeStart = Carbon::parse($overtimeStart);
        $overtimeEnd = Carbon::parse($overtimeEnd);
        $workStart = Carbon::parse($workStart);
        $workEnd = Carbon::parse($workEnd);

        // Overlap terjadi jika (StartA < EndB) dan (EndA > StartB)
        return $overtimeStart->lt($workEnd) && $overtimeEnd->gt($workStart);
    }

}
