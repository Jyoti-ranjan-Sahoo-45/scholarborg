<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CallLog;
use Illuminate\Http\Request;

class CallAuditController extends Controller
{
    /**
     * Display a listing of the call logs for auditing.
     */
    public function index(Request $request)
    {
        $this->authorize('admin_webinars_list'); // Fallback to webinars admin privilege

        $query = CallLog::query()->with(['caller', 'receiver', 'booking', 'auditedBy']);

        // Filter by status
        if ($request->has('status') && !empty($request->input('status'))) {
            $query->where('status', $request->input('status'));
        }

        // Filter by audit status
        if ($request->has('audit_status') && !empty($request->input('audit_status'))) {
            $query->where('audit_status', $request->input('audit_status'));
        }

        $calls = $query->orderBy('id', 'desc')->paginate(10);

        $data = [
            'pageTitle' => 'VoIP Call Audits',
            'calls' => $calls
        ];

        return view('admin.calls.audit', $data);
    }

    /**
     * Update call log audit notes and status.
     */
    public function update(Request $request, $id)
    {
        $this->authorize('admin_webinars_list');

        $request->validate([
            'audit_status' => 'required|in:pending,reviewed,flagged',
            'audit_notes' => 'nullable|string'
        ]);

        $call = CallLog::findOrFail($id);

        $call->update([
            'audit_status' => $request->input('audit_status'),
            'audit_notes' => $request->input('audit_notes'),
            'audited_by' => auth()->user()->id,
            'updated_at' => time()
        ]);

        return response()->json([
            'code' => 200,
            'message' => 'Call log audit status updated successfully.'
        ]);
    }
}
