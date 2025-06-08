<?php

namespace App\Http\Controllers\org;
use App\Http\Controllers\Controller;
use App\Models\Org\Employee;
use App\Models\Org\Document;
use App\Models\Org\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Requests\ImportEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class EmployeeController extends Controller
{
    // ✅ READ - Ambil semua employee
    public function index(Request $request)
    {
        try {
            $query = Employee::with('position', 'user');

            if ($request->has('id')) {
                $id = $request->query('id');

                // Validasi sederhana format UUID v4 (36 karakter termasuk strip)
                if (!is_string($id) || !preg_match('/^[0-9a-fA-F\-]{36}$/', $id)) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Invalid UUID format for id parameter',
                    ], 400);
                }

                $query->where('id', $id);
            }

            $employees = $query->get();

            return EmployeeResource::collection($employees);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function upload(Request $request, $id)
    {
        try {
            Log::info('Upload request diterima, id: ' . $id);

            // Validasi file
            $request->validate([
                'dokumen.*' => 'required|file|mimes:pdf,docx|max:5000',

            ]);

            // Cek apakah employee ada
            $employee = Employee::with('documents')->findOrFail($id);

            // Ambil file dari request
            $files = $request->file('dokumen');
            $files = is_array($files) ? $files : [$files]; // pastikan array
            $uploadedFiles = [];

            foreach ($files as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $filepath = $file->storeAs('public/uploads', $filename);

                $docId = DB::table('tb_documents')->insertGetId([
                    'id_user' => $employee->id_user,
                    'name' => $filename,
                    'file_path' => $filepath,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $uploadedFiles[] = [
                    'id' => $docId,
                    'name' => $filename,
                    'file' => Storage::url($filepath),  // pastikan kamu pakai Storage facade dan sudah `use Illuminate\Support\Facades\Storage;`
                ];
            }

            return response()->json([
                'message' => 'Upload berhasil',
                'dokumen' => $uploadedFiles,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Employee tidak ditemukan: ' . $id);
            return response()->json(['message' => 'Data karyawan tidak ditemukan'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Upload dokumen gagal: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error saat upload dokumen',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // // ✅ CREATE - Tambah employee baru
    // public function store(StoreEmployeeRequest $request)
    // {
    //     $employee = Employee::create($request->validated());
    //     return response()->json($employee, 201);
    // }
    // Fungsi store untuk menyimpan employee baru
    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();

        // Hapus dokumen dari $data supaya gak disimpan ke tabel employee
        unset($data['dokumen']);

        // Log data yang akan dimasukkan
        \Log::info('Employee Data to Insert:', $data);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Simpan employee ke database
        $employee = Employee::create($data);

        // Cek jika ada file dokumen
        if ($request->hasFile('dokumen')) {
            $files = $request->file('dokumen');

            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                $path = $file->store('documents', 'public');

                Document::create([
                    'id_user' => $employee->id_user, // hubungan ke tb_user
                    'type' => 'other', // default, bisa disesuaikan
                    'name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                ]);
            }
        }

        return new EmployeeResource($employee->load('position', 'documents'));
    }


    // ✅ SHOW - Ambil 1 employee berdasarkan id
    public function show($id)
    {
        $employee = Employee::with(['user', 'position', 'documents'])->find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }
        return new EmployeeResource($employee->load('position', 'documents'));

    }


    // ✅ UPDATE - Ubah data employee
    public function update(UpdateEmployeeRequest $request, $id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        // 🔍 Debug isi validasi
        Log::info('Validated data:', $request->validated());

        // Ambil semua field valid kecuali file
        $data = $request->validated();
        unset($data['avatar']); // avatar ditangani terpisah

        // Simpan field biasa
        $employee->fill($data);

        // Simpan avatar jika ada file baru
        if ($request->hasFile('avatar')) {
            $employee->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        // Simpan ke database
        $employee->save();

        return response()->json([
            'message' => 'Data berhasil diperbarui.',
            'data' => $employee->load('position', 'documents')
        ]);
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


    //IMPORT EMPLOYEE
    public function import(ImportEmployeeRequest $request)
    {
        $employees = $request->input('id');

        if (empty($employees)) {
            return response()->json(['message' => 'No data to import'], 400);
        }

        \Log::info('Import employees:', $employees);

        try {
            foreach ($employees as $emp) {
                if (empty($emp['first_name']) && empty($emp['last_name']) && !empty($emp['nama'])) {
                    $nameParts = explode(' ', $emp['nama'], 2);
                    $emp['first_name'] = $nameParts[0];
                    $emp['last_name'] = $nameParts[1] ?? '';
                }

                if (empty($emp['address'])) {
                    $emp['address'] = 'Tidak Diketahui';
                }

                if (empty($emp['first_name']) || empty($emp['last_name']) || empty($emp['address'])) {
                    \Log::warning('Skipping employee, data tidak lengkap', $emp);
                    continue;
                }

                Employee::updateOrCreate(
                    ['id' => $emp['id'] ?? null],
                    [
                        'id_user' => $emp['id_user'] ?? null,
                        'first_name' => $emp['first_name'],
                        'last_name' => $emp['last_name'],
                        'address' => $emp['address'],
                        'jenisKelamin' => $emp['jenisKelamin'] ?? null,
                        'notelp' => $emp['notelp'] ?? null,
                        'cabang' => $emp['cabang'] ?? null,
                        'jabatan' => $emp['jabatan'] ?? null,
                        'employment_status' => $emp['employment_status'] ?? 'active',
                        'email' => $emp['email'] ?? null,
                    ]
                );
            }

            // Tambahan: Kembalikan seluruh data setelah import
            $allEmployees = Employee::with('user')->get();

            return response()->json([
                'message' => 'Import berhasil',
                'data' => $allEmployees
            ], 200);

        } catch (\Exception $e) {
            \Log::error("Import Employee gagal: " . $e->getMessage());
            return response()->json(['message' => 'Gagal import data', 'error' => $e->getMessage()], 500);
        }
    }




    public function deleteEmployeeDocument($employeeId, $documentId)
    {
        \Log::info("Delete document called with employeeId: $employeeId, documentId: $documentId");

        try {
            $employee = Employee::where('id', $employeeId)->firstOrFail();

            \Log::info("Employee found: " . $employee->id);

            $userId = $employee->id_user;
            $document = Document::where('id', $documentId)
                ->where('id_user', $userId)
                ->first();

            if (!$document) {
                \Log::warning("Document not found or not belongs to userId: $userId");
                return response()->json(['message' => 'Dokumen tidak ditemukan atau tidak milik employee ini.'], 404);
            }

            $document->delete();
            \Log::info("Document deleted successfully.");

            return response()->json(['message' => 'Dokumen berhasil dihapus dari employee.']);
        } catch (\Exception $e) {
            \Log::error('Error hapus dokumen: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus dokumen.'], 500);
        }
    }




}
