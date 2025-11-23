<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\User\ReportRequest;
use App\Models\Report;

class ReportController extends Controller
{
    public function store(ReportRequest $request)
    {
        Report::create([
            'email_of_reporter' => $request->user()->email,
            'user_id' => $request->user_id,
            'title' => $request->title,
            'description' => $request->description,
            'report_type' => $request->report_type,
        ]);
        return $this->success(
            'Report created successfully', 
            null,
            201
        );
    }
}
