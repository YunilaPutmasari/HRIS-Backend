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
        $approval = Approval::whereIn('id_user', $userIds)
            ->select('id', 'id_user', 'request_type', 'status', 'created_at')
            ->with(['employee:id,id_user,first_name,last_name'])->get();

        return BaseResponse::success(
            data : $approval,
            message : 'Approval retrieved successfully',
            code : 200,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ApprovalStoreRequest $request)
    {
        $data = $request->validated();

        $approval = Approval::create($data);

        return BaseResponse::success(
            data: $approval,
            message: 'Approval created successfully',
            code: 201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $record = Approval::findOrFail($id);
        return BaseResponse::success(
            data: $record,
            message: 'Approval retrieved successfully',
            code: 200
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Approval $approval)
    {
        //
    }
}
