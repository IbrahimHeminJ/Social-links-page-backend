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
        // Get reports that are NOT handled (handled_by is null)
        $reports = Report::query()
            ->with('user','reportedOnUser')
            ->whereNull('handled_by')
            ->get();
        return $this->success(
            'Reports fetched successfully',
            ReportResource::collection(new CollectionResource($reports))
        );
    }
    public function getResolvedReports()
    {
        // Get reports that ARE handled (handled_by is not null)
        $reports = Report::query()
            ->with('user','reportedOnUser')
            ->whereNotNull('handled_by')
            ->get();
        return $this->success(
            'Resolved reports fetched successfully',
            ReportResource::collection(new CollectionResource($reports))
        );
    }
    public function show($id)
    {
        $report = Report::query()
            ->with('user', 'reportedOnUser')
            ->findOrFail($id);
        
        return $this->success(
            'Report fetched successfully',
            new ReportResource($report)
        );
    }
    public function store(ReportRequest $request, $id)
    {
        try{
            $report = Report::query()->findOrFail($id);
            $report->update([
                'handled_by' => $request->user()->id, // Set handled_by to mark as handled
                'reason_of_action' => $request->validated()['reason_of_action'],
                'report_status' => 1, // Automatically set to 1 when handled
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
