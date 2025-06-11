<?php

namespace App\Http\Controllers\Org;
use App\Models\Org\Document;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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


}
