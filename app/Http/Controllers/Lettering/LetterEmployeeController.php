<?php

namespace App\Http\Controllers\Lettering;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use Illuminate\Http\JsonResponse;

class LetterEmployeeController extends Controller
{
    public function index(): JsonResponse
    {
        // Ambil surat + user langsung dari relasi User
        $letters = Letter::with('user')->get();

        $data = $letters->map(fn($item) => [
            'id' => $item->id,
            'id_user' => $item->id_user,
            'id_letter_format' => $item->id_letter_format,
            'subject' => $item->subject,
            'body' => $item->body,
            'user' => [
                'email' => $item->user?->email ?? "Tidak ditemukan",
            ],
        ]);

        return response()->json([
            'meta' => ['success' => true],
            'data' => $data,
        ]);
    }
}
