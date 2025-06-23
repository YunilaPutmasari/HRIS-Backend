<?php

namespace App\Http\Controllers\Lettering;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use App\Models\LetterFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LetterController extends Controller
{
    public function getFormats()
    {
        try {
            $formats = LetterFormat::all();
            return response()->json([
                'meta' => ['success' => true],
                'data' => $formats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'meta' => ['success' => false, 'message' => 'Gagal mengambil format surat'],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id_user' => 'required|uuid',
            'id_letter_format' => 'required|uuid|exists:tb_letter_format,id',
            'subject' => 'required|string|max:255',
            'body' => 'nullable|string', // Bisa diisi manual atau auto dari template
        ]);

        if ($validator->fails()) {
            return response()->json([
                'meta' => ['success' => false, 'message' => 'Validasi gagal'],
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Ambil format
            $format = LetterFormat::find($request->id_letter_format);
            $body = $request->body ?? $format->template;
            \Log::info('Cek format:', [$format]);
            \Log::info('Cek request:', $request->all());
            $letter = Letter::create([
                'id_user' => $request->id_user,             // Penerima surat
                'id_sender' => $request->user()->id,        // Pengirim surat (user yang login)
                'id_letter_format' => $format->id,
                'subject' => $request->subject,
                'body' => $body,
            ]);



            return response()->json([
                'meta' => ['success' => true, 'message' => 'Surat berhasil dibuat dan dikirim ke karyawan'],
                'data' => $letter,
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan surat:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'meta' => ['success' => false, 'message' => 'Gagal menyimpan surat'],
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
