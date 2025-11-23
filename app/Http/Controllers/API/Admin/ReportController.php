<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Admin\ReportRequest;
use App\Http\Resources\Admin\ReportResource;
use App\Http\Resources\CollectionResource;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function getShowReports()
    {
        $reports = Report::query()->
        with('user','reportedOnUser')
        ->where('report_status',true)
        ->get();
        return $this->success(
            'Reports fetched successfully',
            ReportResource::collection(new CollectionResource($reports))
        );
    }
    public function getResolvedReports()
    {
        $reports = Report::query()->
        with('user','reportedOnUser')
        ->where('report_status',false)
        ->get();
        return $this->success(
            'Resolved reports fetched successfully',
            ReportResource::collection(new CollectionResource($reports))
        );
    }
    public function store(ReportRequest $request, $id)
    {
        try{
            $report = Report::query()->findOrFail($id);
            $report->update([
                'report_status' => true,
                'handled_by' => $request->user()->id,
                'reason_of_action' => $request->reason_of_action,
            ]);
            return $this->success(
                'Report handled successfully',
                null,
                200
            );
        } catch (\Exception $e) {
            return $this->error('Report not found', 404);
        } 
    }
}
