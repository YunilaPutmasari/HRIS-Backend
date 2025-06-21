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
        // Ambil surat + user langsung dari relasi User
        $letters = Letter::with(['user', 'format'])->get();


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
