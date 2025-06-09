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
use App\Helpers\BaseResponse;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Employee::with(['position', 'user']); // eager load

            if ($request->has('id')) {
                $id = $request->query('id');
                if (!is_string($id) || !preg_match('/^[0-9a-fA-F\-]{36}$/', $id)) {
                    return BaseResponse::error('Invalid UUID format for id parameter', 400);
                }
                $query->where('id', $id);
            }

            if ($request->has('id_workplace')) {
                $companyId = $request->query('id_workplace');
                if (!is_string($companyId) || !preg_match('/^[0-9a-fA-F\-]{36}$/', $companyId)) {
                    return BaseResponse::error('Invalid UUID format for id_workplace parameter', 400);
                }

                // Filter Employee berdasarkan id_workplace lewat relasi user → id_workplace
                $query->whereHas('user', function ($q) use ($companyId) {
                    $q->where('id_workplace', $companyId);
                });
            }

            $employees = $query->get();

            return BaseResponse::success(EmployeeResource::collection($employees));
        } catch (\Exception $e) {
            return BaseResponse::error($e->getMessage(), 500);
        }
    }


    public function upload(Request $request, $id)
    {
        try {
            Log::info('Upload request diterima, id: ' . $id);

            $request->validate([
                'dokumen.*' => 'required|file|mimes:pdf,docx|max:5000',
            ]);

            $employee = Employee::with('documents')->findOrFail($id);

            $files = $request->file('dokumen');
            $files = is_array($files) ? $files : [$files];
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
                    'file' => Storage::url($filepath),
                ];
            }

            return BaseResponse::success([
                'message' => 'Upload berhasil',
                'dokumen' => $uploadedFiles,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return BaseResponse::error('Data karyawan tidak ditemukan', 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return BaseResponse::error('Validasi gagal', 422, $e->errors());
        } catch (\Exception $e) {
            Log::error('Upload dokumen gagal: ' . $e->getMessage());
            return BaseResponse::error('Error saat upload dokumen', 500, ['exception' => $e->getMessage()]);
        }
    }

    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();
        unset($data['dokumen']);

        \Log::info('Employee Data to Insert:', $data);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $employee = Employee::create($data);

        if ($request->hasFile('dokumen')) {
            $files = $request->file('dokumen');
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                $path = $file->store('documents', 'public');

                Document::create([
                    'id_user' => $employee->id_user,
                    'type' => 'other',
                    'name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                ]);
            }
        }

        return BaseResponse::success(new EmployeeResource($employee->load('position', 'documents')), 201);
    }

    public function show($id)
    {
        $employee = Employee::with(['user', 'position', 'documents'])->find($id);
        if (!$employee) {
            return BaseResponse::error('Employee not found', 404);
        }
        return BaseResponse::success(new EmployeeResource($employee->load('position', 'documents')));
    }

    public function update(UpdateEmployeeRequest $request, $id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return BaseResponse::error('Employee not found', 404);
        }

        Log::info('Validated data:', $request->validated());

        $data = $request->validated();
        unset($data['avatar']);

        $employee->fill($data);

        if ($request->hasFile('avatar')) {
            $employee->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $employee->save();

        return BaseResponse::success([
            'message' => 'Data berhasil diperbarui.',
            'data' => $employee->load('position', 'documents')
        ]);
    }

    public function destroy($id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return BaseResponse::error('Employee not found', 404);
        }

        $employee->delete();
        return BaseResponse::success(['message' => 'Employee deleted successfully']);
    }

    public function import(ImportEmployeeRequest $request)
    {
        $employees = $request->input('id');

        if (empty($employees)) {
            return BaseResponse::error('No data to import', 400);
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
                        'jenis_kelamin' => $emp['jenis_kelamin'] ?? null,
                        'no_telp' => $emp['no_telp'] ?? null,
                        'cabang' => $emp['cabang'] ?? null,
                        'jabatan' => $emp['jabatan'] ?? null,
                        'employment_status' => $emp['employment_status'] ?? 'active',
                        'email' => $emp['email'] ?? null,
                    ]
                );
            }

            $allEmployees = Employee::with('user')->get();

            return BaseResponse::success([
                'message' => 'Import berhasil',
                'data' => $allEmployees
            ]);
        } catch (\Exception $e) {
            \Log::error("Import Employee gagal: " . $e->getMessage());
            return BaseResponse::error('Gagal import data', 500, ['exception' => $e->getMessage()]);
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
                return BaseResponse::error('Dokumen tidak ditemukan atau tidak milik employee ini.', 404);
            }

            $document->delete();
            \Log::info("Document deleted successfully.");

            return BaseResponse::success(['message' => 'Dokumen berhasil dihapus dari employee.']);
        } catch (\Exception $e) {
            \Log::error('Error hapus dokumen: ' . $e->getMessage());
            return BaseResponse::error('Terjadi kesalahan saat menghapus dokumen.', 500);
        }
    }
}
