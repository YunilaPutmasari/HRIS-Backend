<?php

namespace App\Http\Controllers\Lettering;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use Illuminate\Http\JsonResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class LetterEmployeeController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'meta' => ['success' => false, 'message' => 'User tidak terautentikasi.'],
            ], 401);
        }

        $letters = Letter::with(['user', 'format'])
            ->where('id_user', $user->id)
            ->get();

        $data = $letters->map(fn($item) => [
            'id' => $item->id,
            'id_user' => $item->id_user,
            'id_letter_format' => $item->id_letter_format,
            'subject' => $item->subject,
            'body' => $item->body,
            'user' => [
                'email' => $item->user?->email ?? "Tidak ditemukan",
            ],
            'format' => [
                'name' => $item->format?->name ?? "Tidak ditemukan",
            ],
            'created_at' => $item->created_at->format('Y-m-d H:i:s'), // waktu kirim surat
        ]);

        return response()->json([
            'meta' => ['success' => true],
            'data' => $data,
        ]);
    }

    public function downloadPdf($id)
    {
        $letter = Letter::with(['user', 'format'])->findOrFail($id);



        $pdf = Pdf::loadView('pdf.letter', [
            'letter' => $letter
        ]);

        return $pdf->download("Surat-{$letter->subject}.pdf");
    }

}
