<?php

namespace App\Http\Controllers\Lettering;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovalStoreRequest;
use App\Http\Requests\ApprovalUpdateRequest;
use App\Http\Responses\BaseResponse;
use App\Models\Approval;
use App\Models\Org\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // user should own and be the admin of issued company id
        $user = $request->user();
        $companies = $user->companies()->get();
        $companyIds = $companies->pluck('id')->toArray();

        // Get users in the same companies
        $userIds = User::whereIn('id_workplace', $companyIds)
            ->pluck('id')
            ->toArray();


        // Retrieve approvals for those users
        try {
            if ($user->isAdmin()){
                $approval = Approval::whereIn('id_user', $userIds)
                    ->with([
                        'employee',
                        'employee.position'
                    ])->get()
                    ->map(function ($approval) {
                        $approval->document_url = $approval->document ? Storage::url($approval->document) : null;
                        return $approval;
                    });
            } else {
                $approval = Approval::where('id_user', $user->id)
                    ->with([
                        'employee',
                        'employee.position'
                    ])->get()
                    ->map(function ($approval) {
                        $approval->document_url = $approval->document ? Storage::url($approval->document) : null;
                        return $approval;
                    });
            }
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ], 500);
        }


        return BaseResponse::success(
            data : $approval,
            message : 'Approval retrieved successfully',
        );
    }

    public function create(Request $request)
    {
        $search = $request->input('search', '');
        // user should own and be the admin of issued company id
        $user = $request->user();
        $companies = $user->companies()->get();
        $companyIds = $companies->pluck('id')->toArray();

        try {
            $query = User::whereIn('id_workplace', $companyIds)
                ->with([
                    'employee:id,id_user,first_name,last_name',
                ]);

            if (!empty($search)) {
                $query->whereHas('employee', function ($subQuery) use ($search){
                    $subQuery->whereRaw('LOWER(first_name) LIKE ?', ["%".strtolower($search)."%"])
                        ->orWhereRaw('LOWER(last_name) LIKE ?', ["%".strtolower($search)."%"]);
                });
            }

            $users = $query->paginate(10, ['id']);

            return BaseResponse::success(
                data: $users,
                message: 'Users retrieved successfully',
                code: 200
            );
        } catch (\Throwable $e) {
            return BaseResponse::error(
                message: 'Error retrieving users: ' . $e->getMessage(),
                code: 500
            );
        }
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();

        $approval = Approval::with([
                'employee',
                'employee.position'
            ])
            ->find($id);
        if (!$approval) {
            return BaseResponse::error(
                message: 'Approval not found',
                code: 404
            );
        }

        $isOwner = ($approval->id_user == $user->id);

        if ($user->isAdmin()) {
            $companies = $user->companies()->pluck('id')->toArray();

            $approvalOwner = User::find($approval->id_user);

            if (!$isOwner && !in_array($approvalOwner->id_workplace, $companies)) {
                return BaseResponse::error(
                    message: 'You do not have permission to view this approval',
                    code: 403
                );
            }
        } else {
            if (!$isOwner) {
                return BaseResponse::error(
                    message: 'You do not have permission to view this approval',
                    code: 403
                );
            }
        }

        $approval->document_url = $approval->document ? Storage::url($approval->document) : null;

        return BaseResponse::success(
            data: $approval,
            message: 'Approval retrieved successfully',
            code: 200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ApprovalStoreRequest $request)
    {   // user should own and be the admin of issued company id
        $user = $request->user();
        $companies = $user->companies()->get();
        $companyIds = $companies->pluck('id')->toArray();

        $data = $request->validated();

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filePatth = $file->store('documents', 'public');
            $data['document'] = $filePatth;
        }

        if ($user->isAdmin()) {
            $data['id_user'] = $request->input('id_user');
            $data['status'] = 'approved';
            $data['approved_by'] = $user->id;


            return BaseResponse::success(
                data: [
                    'approval' => Approval::create($data),
                ],
                message: 'Approval created and CheckClock created successfully',
                code: 201
            );
        } else {
            $data['id_user'] = $user->id;
            $data['status'] = 'pending';
        }

        $approval = Approval::create($data);

        return BaseResponse::success(
            data: $approval,
            message: 'Approval created successfully',
            code: 201
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ApprovalUpdateRequest $request, $id)
    {
        $approval = Approval::find($id);

        if (!$approval) {
            return BaseResponse::error(
                message: 'Approval not found',
                code: 404
            );
        }

        $user = $request->user();
        $isOwner = ($approval->id_user == $user->id);

        if (!$user->isAdmin()) {
            if (!$isOwner) {
                return BaseResponse::error(
                    message: 'You do not have permission to update this approval',
                    code: 403
                );
            }
            if ($approval->status !== 'pending') {
                return BaseResponse::error(
                    message: 'This approval cannot be edited because it has already been processed.',
                    code: 403
                );
            }
        }

        if ($user->isAdmin()) {
            $adminCompanyIds = $user->companies()->pluck('id')->toArray();
            $approvalOwner = User::find($approval->id_user);

            if (!$isOwner && (!is_null($approvalOwner) && !in_array($approvalOwner->id_workplace, $adminCompanyIds))) {
                return BaseResponse::error(
                    message: 'You do not have permission to update this approval',
                    code: 403
                );
            }
        }

        $data = $request->validated();
        $approval->update($data);

        return BaseResponse::success(
            data: $approval->fresh(),
            message: 'Approval updated successfully',
        );
    }

    public function approve(Request $request, $id){
        $user = $request->user();
        $companies = $user->companies()->get();
        $companyIds = $companies->pluck('id')->toArray();

        $userIds = User::whereIn('id_workplace', $companyIds)
            ->pluck('id')
            ->toArray();

        $approval = Approval::whereIn('id_user', $userIds)
            ->where('id', $id)
            ->first();

        if (!$approval) {
            return BaseResponse::error(
                message: 'Approval not found',
                code: 404
            );
        }

        $approval->status = 'approved';
        $approval->approved_by = $user->id;
        $approval->save();

        return BaseResponse::success(
            data: [
                'approval' => $approval,
            ],
            message: 'Approval approved and CheckClock created successfully',
            code: 200
        );
    }

    public function reject(Request $request, $id){
        $user = $request->user();
        $companies = $user->companies()->get();
        $companyIds = $companies->pluck('id')->toArray();

        $userIds = User::whereIn('id_workplace', $companyIds)
            ->pluck('id')
            ->toArray();

        $approval = Approval::whereIn('id_user', $userIds)
            ->where('id', $id)
            ->first();

        if (!$approval) {
            return BaseResponse::error(
                message: 'Approval not found',
                code: 404
            );
        }

        $approval->status = 'rejected';
        $approval->approved_by = $user->id;
        $approval->save();

        return BaseResponse::success(
            data: $approval,
            message: 'Approval rejected successfully',
            code: 200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Approval $approval)
    {
        //
    }

    public function isAdmin(Request $request) {
        $user = $request->user();

        if (!$user) {
            return BaseResponse::error(
                message: 'User not authenticated',
                code: 401
            );
        }
        return BaseResponse::success(
            data: ['isAdmin' => $user->isAdmin()],
            message: 'Admin status retrieved successfully',
        );
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
}
