<?php

namespace App\Http\Controllers;

use App\Models\AdminAuditLog;

class AdminAuditController extends Controller
{
    public function index()
    {
        return view('admin.audit.index', ['logs' => AdminAuditLog::with('user')->latest()->paginate(30)]);
    }
}
