<?php

namespace App\Http\Controllers\Lettering;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovalStoreRequest;
use App\Http\Requests\ApprovalUpdateRequest;
use App\Http\Responses\BaseResponse;
use App\Models\Approval;
use App\Models\Org\User;
use Illuminate\Http\Request;

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
                    ])->get();
            } else {
                $approval = Approval::where('id_user', $user->id)
                    ->with([
                        'employee',
                        'employee.position'
                    ])->get();
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
            code : 200,
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(ApprovalStoreRequest $request)
    {   // user should own and be the admin of issued company id
        $user = $request->user();
        $companies = $user->companies()->get();
        $companyIds = $companies->pluck('id')->toArray();

        $data = $request->validated();
        if ($user->isAdmin()) {
            $data['id_user'] = $request->input('id_user');
            $data['status'] = 'approved';
            $data['approved_by'] = $user->id;
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
        $data = $request->validated();

        // user should own and be the admin of issued company id
        $user = $request->user();
        $companies = $user->companies()->get();
        $companiesIds = $companies->pluck('id')->toArray();

        $approval = Approval::whereIn('id_company', $companiesIds)
            ->where('id', $id)
            ->first();

        if (!$approval) {
            return BaseResponse::error(
                message: 'Check clock setting not found',
                code: 404
            );
        }

        $approval->update($data);

        return BaseResponse::success(
            data: $approval,
            message: 'Approval updated successfully',
            code: 200
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
            data: $approval,
            message: 'Approval approved successfully',
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
}
