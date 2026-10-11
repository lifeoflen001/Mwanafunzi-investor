<?php

namespace App\Http\Controllers;

use App\Support\ComplianceAudit;

class AdminComplianceController extends Controller
{
    public function index()
    {
        return view('admin.compliance.index', ['report' => ComplianceAudit::report()]);
    }
}
