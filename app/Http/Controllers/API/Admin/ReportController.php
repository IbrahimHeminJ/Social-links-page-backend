<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
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

    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $report->update($request->all());
        return $this->success(
            'Report updated successfully',
            new ReportResource($report)
        );
    }
}
