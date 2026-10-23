<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ParentStudent;
use App\Services\ZohoCrmService;
use App\User;
use Illuminate\Http\Request;

class ParentChildrenController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user->isParent()) {
            return redirect('/panel/dashboard');
        }

        $children = $user->children()->get();

        $data = [
            'pageTitle' => trans('panel.my_children') ?? 'My Children',
            'children' => $children,
        ];

        return view('design_1.panel.parent.children.index', $data);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->isParent()) {
            return redirect('/panel/dashboard');
        }

        $this->validate($request, [
            'student_code_or_email' => 'required|string',
        ]);

        $query = trim($request->input('student_code_or_email'));

        $student = User::where('student_code', $query)
            ->orWhere('email', $query)
            ->orWhere('username', $query)
            ->first();

        if (!$student) {
            return redirect()->back()->with('toast', [
                'status' => 'error',
                'title' => 'Error',
                'msg' => 'No student account found with the provided Code or Email.'
            ]);
        }

        if ($student->id == $user->id) {
            return redirect()->back()->with('toast', [
                'status' => 'error',
                'title' => 'Error',
                'msg' => 'You cannot link your own account as a child.'
            ]);
        }

        $existing = ParentStudent::where('parent_id', $user->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('toast', [
                'status' => 'info',
                'title' => 'Notice',
                'msg' => 'This student is already linked to your parent account.'
            ]);
        }

        ParentStudent::create([
            'parent_id' => $user->id,
            'student_id' => $student->id,
            'access_code' => $student->student_code,
            'status' => 'active',
            'created_at' => time(),
        ]);

        // Sync lead note to Zoho CRM if Parent has a Zoho Lead ID
        if (!empty($user->zoho_lead_id)) {
            try {
                $zohoService = new ZohoCrmService();
                $noteContent = "Linked Child: {$student->full_name} (Email: {$student->email}, Code: {$student->student_code}).";
                $zohoService->addLeadNote($user->zoho_lead_id, $noteContent);
            } catch (\Exception $e) {
                // Log silently
            }
        }

        return redirect()->back()->with('toast', [
            'status' => 'success',
            'title' => 'Success',
            'msg' => "Child {$student->full_name} linked successfully!"
        ]);
    }

    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user->isParent()) {
            return redirect('/panel/dashboard');
        }

        ParentStudent::where('parent_id', $user->id)
            ->where('student_id', $id)
            ->delete();

        return redirect()->back()->with('toast', [
            'status' => 'success',
            'title' => 'Success',
            'msg' => 'Child account unlinked.'
        ]);
    }
}
