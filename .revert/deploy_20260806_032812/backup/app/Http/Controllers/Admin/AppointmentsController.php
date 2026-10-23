<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accounting;
use App\Models\Meeting;
use App\Models\MeetingTime;
use App\Models\Order;
use App\Models\ReserveMeeting;
use App\Models\Sale;
use App\Models\Setting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class AppointmentsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_appointments_lists');

        $query = ReserveMeeting::whereNotNull('reserved_at');

        $totalAppointments = deepClone($query)->count();
        $openAppointments = deepClone($query)->where('status', ReserveMeeting::$open)->count();
        $finishedAppointments = deepClone($query)->where('status', ReserveMeeting::$finished)->count();
        $totalConsultants = Meeting::where('disabled', false)
            ->whereHas('meetingTimes')
            ->groupBy('creator_id')
            ->count();

        $query = $this->filters($query, $request);

        $appointments = $query->with([
            'meeting' => function ($query) {
                $query->with([
                    'creator' => function ($query) {
                        $query->select('id', 'full_name');
                    }
                ]);
            },
            'user' => function ($query) {
                $query->select('id', 'full_name');
            },
            'sale'
        ])->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/main.meetings_list_title'),
            'totalAppointments' => $totalAppointments,
            'openAppointments' => $openAppointments,
            'finishedAppointments' => $finishedAppointments,
            'totalConsultants' => $totalConsultants,
            'appointments' => $appointments,
        ];

        $consultant_ids = $request->get('consultant_ids', []);
        $user_ids = $request->get('user_ids', []);
        if (!empty($consultant_ids)) {
            $data['consultants'] = User::select('id', 'full_name')
                ->whereIn('id', $consultant_ids)->get();
        }

        if (!empty($user_ids)) {
            $data['users'] = User::select('id', 'full_name')
                ->whereIn('id', $user_ids)->get();
        }

        return view('admin.appointments.lists', $data);
    }

    private function filters($query, $request)
    {
        $search = $request->get('search');
        $from = $request->get('from');
        $to = $request->get('to');
        $status = $request->get('status');
        $sort = $request->get('sort');
        $consultant_ids = $request->get('consultant_ids', []);
        $user_ids = $request->get('user_ids', []);

        if (!empty($search)) {
            $userIds = User::where('full_name', 'like', "%$search%")->pluck('id')->toArray();
            $meetingIds = Meeting::where('disabled', false)
                ->whereIn('creator_id', $userIds)
                ->pluck('id')
                ->toArray();

            $query->where(function ($query) use ($userIds, $meetingIds) {
                $query->whereIn('user_id', $userIds)
                    ->orWhereIn('meeting_id', $meetingIds);
            });
        }

        // $from and $to
        $query = fromAndToDateFilter($from, $to, $query, 'date');

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($sort)) {
            switch ($sort) {
                case 'has_discount':
                    $query->whereNotNull('discount')
                        ->where('discount', '>', '0');
                    break;
                case 'free':
                    $query->where('paid_amount', '=', '0');
                    break;
                case 'demo':
                    $query->where('is_demo', 1);
                    break;
                case 'amount_asc':
                    $query->orderBy('paid_amount', 'asc');
                    break;
                case 'amount_desc':
                    $query->orderBy('paid_amount', 'desc');
                    break;
                case 'date_asc':
                    $query->orderBy('date', 'asc');
                    break;
                case 'date_desc':
                    $query->orderBy('date', 'desc');
                    break;
            }
        }

        if (!empty($consultant_ids)) {
            $meetingIds = Meeting::where('disabled', false)
                ->whereIn('creator_id', $consultant_ids)
                ->pluck('id')
                ->toArray();

            $query->whereIn('meeting_id', $meetingIds);
        }

        if (!empty($user_ids)) {
            $query->whereIn('user_id', $user_ids);
        }

        return $query;
    }

    public function join($id)
    {
        $this->authorize('admin_appointments_join');

        $ReserveMeeting = ReserveMeeting::where('id', $id)->first();

        if (!empty($ReserveMeeting)) {
            return Redirect::away($ReserveMeeting->link);
        }

        abort(404);
    }

    public function getReminderDetails($id)
    {
        $this->authorize('admin_appointments_send_reminder');

        $appointment = ReserveMeeting::where('id', $id)->first();

        $templateId = getNotificationTemplates('appointment_reminder');
        $notificationTemplate = \App\Models\NotificationTemplate::where('id', $templateId)->first();

        if (!empty($appointment) and $notificationTemplate) {

            $notifyOptions = [
                '[time.date]' => dateTimeFormat(strtotime($appointment->day), 'j M Y') . ' (' . $appointment->meetingTime->time . ')',
            ];

            $data = [
                'consultant' => $appointment->meeting->creator->full_name,
                'reservatore' => $appointment->user->full_name,
                'title' => $notificationTemplate->title,
                'content' => str_replace(array_keys($notifyOptions), array_values($notifyOptions), $notificationTemplate->template)
            ];

            return response()->json($data, 200);
        }

        return response()->json([], 422);
    }

    public function sendReminder($id)
    {
        $this->authorize('admin_appointments_send_reminder');

        $appointment = ReserveMeeting::where('id', $id)->first();

        if (!empty($appointment)) {
            $notifyOptions = [
                '[time.date]' => dateTimeFormat(strtotime($appointment->day), 'j M Y') . ' (' . $appointment->meetingTime->time . ')',
            ];

            sendNotification('appointment_reminder', $notifyOptions, $appointment->meeting->creator->id); // consultant
            sendNotification('appointment_reminder', $notifyOptions, $appointment->user_id); // reservatore

            $toastData = [
                'title' => trans('public.request_success'),
                'msg' => 'Reminder send successful',
                'status' => 'success'
            ];

            return back()->with(['toast' => $toastData]);
        }

        abort(404);
    }

    public function cancel($id)
    {
        $this->authorize('admin_appointments_cancel');

        $appointment = ReserveMeeting::where('id', $id)->first();

        if (!empty($appointment)) {

            $sale = Sale::where('id', $appointment->sale_id)
                ->whereNull('refund_at')
                ->first();

            if (!empty($sale)) {

                Accounting::refundAccounting($sale);

                $sale->update([
                    'refund_at' => time()
                ]);

                $appointment->update([
                    'status' => ReserveMeeting::$canceled
                ]);

                $toastData = [
                    'title' => trans('public.request_success'),
                    'msg' => 'Appointment canceled successful',
                    'status' => 'success'
                ];

                return back()->with(['toast' => $toastData]);
            }
        }

        abort(404);
    }

    // ─── Admin Manual Booking ─────────────────────────────────────────────────

    public function create()
    {
        $this->authorize('admin_appointments_lists');

        $instructors = User::whereHas('meeting')->select('id', 'full_name', 'username')->get();
        $students    = User::select('id', 'full_name', 'username')->get();

        return view('admin.appointments.create', [
            'pageTitle'   => 'Create Meeting Reservation',
            'instructors' => $instructors,
            'students'    => $students,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_appointments_lists');

        $data = $request->validate([
            'student_id'    => 'required|exists:users,id',
            'instructor_id' => 'required|exists:users,id',
            'meeting_time_id' => 'required|exists:meeting_times,id',
            'day'           => 'required|date',
            'meeting_type'  => 'required|in:online,in_person',
            'is_demo'       => 'nullable|boolean',
            'status'        => 'nullable|in:open,pending,finished,canceled',
            'link'          => 'nullable|url',
        ]);

        $meetingTime = MeetingTime::find($data['meeting_time_id']);
        $meeting     = Meeting::find($meetingTime->meeting_id);

        $explode  = explode('-', $meetingTime->time);
        $timezone = $meeting->getTimezone();
        $startAt  = convertTimeToUTCzone($data['day'] . ' ' . $explode[0], $timezone)->getTimestamp();
        $endAt    = convertTimeToUTCzone($data['day'] . ' ' . $explode[1], $timezone)->getTimestamp();

        $reserve = ReserveMeeting::create([
            'user_id'        => $data['student_id'],
            'meeting_id'     => $meeting->id,
            'meeting_time_id'=> $data['meeting_time_id'],
            'day'            => $data['day'],
            'date'           => strtotime($data['day']),
            'start_at'       => $startAt,
            'end_at'         => $endAt,
            'paid_amount'    => 0,
            'is_demo'        => !empty($data['is_demo']) ? 1 : 0,
            'meeting_type'   => $data['meeting_type'],
            'student_count'  => 1,
            'status'         => $data['status'] ?? ReserveMeeting::$pending,
            'link'           => $data['link'] ?? null,
            'reserved_at'    => time(),
            'created_at'     => time(),
        ]);

        // Create a $0 sale so the reservation is properly tracked
        $sale = Sale::create([
            'buyer_id'       => $data['student_id'],
            'seller_id'      => $meeting->creator_id,
            'meeting_id'     => $meeting->id,
            'type'           => Sale::$meeting,
            'payment_method' => Sale::$credit,
            'amount'         => 0,
            'total_amount'   => 0,
            'created_at'     => time(),
        ]);

        $reserve->update(['sale_id' => $sale->id]);

        $toastData = [
            'title'  => trans('public.request_success'),
            'msg'    => 'Meeting reservation created successfully.',
            'status' => 'success'
        ];

        return redirect(route('admin.appointments'))->with(['toast' => $toastData]);
    }

    public function edit($id)
    {
        $this->authorize('admin_appointments_lists');

        $appointment = ReserveMeeting::with(['meeting.creator', 'user', 'meetingTime'])->findOrFail($id);
        $instructors = User::whereHas('meeting')->select('id', 'full_name', 'username')->get();
        $students    = User::select('id', 'full_name', 'username')->get();

        return view('admin.appointments.edit', [
            'pageTitle'   => 'Edit / Reschedule Reservation',
            'appointment' => $appointment,
            'instructors' => $instructors,
            'students'    => $students,
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_appointments_lists');

        $appointment = ReserveMeeting::findOrFail($id);

        $data = $request->validate([
            'student_id'     => 'required|exists:users,id',
            'instructor_id'  => 'required|exists:users,id',
            'meeting_time_id'=> 'required|exists:meeting_times,id',
            'day'            => 'required|date',
            'meeting_type'   => 'required|in:online,in_person',
            'is_demo'        => 'nullable|boolean',
            'status'         => 'required|in:open,pending,finished,canceled',
            'link'           => 'nullable|url',
        ]);

        $meetingTime = MeetingTime::find($data['meeting_time_id']);
        $meeting     = Meeting::where('creator_id', $data['instructor_id'])->first();

        $explode  = explode('-', $meetingTime->time);
        $timezone = $meeting->getTimezone();
        $startAt  = convertTimeToUTCzone($data['day'] . ' ' . $explode[0], $timezone)->getTimestamp();
        $endAt    = convertTimeToUTCzone($data['day'] . ' ' . $explode[1], $timezone)->getTimestamp();

        $appointment->update([
            'user_id'        => $data['student_id'],
            'meeting_id'     => $meeting->id,
            'meeting_time_id'=> $data['meeting_time_id'],
            'day'            => $data['day'],
            'date'           => strtotime($data['day']),
            'start_at'       => $startAt,
            'end_at'         => $endAt,
            'is_demo'        => !empty($data['is_demo']) ? 1 : 0,
            'meeting_type'   => $data['meeting_type'],
            'status'         => $data['status'],
            'link'           => $data['link'] ?? $appointment->link,
        ]);

        $toastData = [
            'title'  => trans('public.request_success'),
            'msg'    => 'Meeting reservation updated successfully.',
            'status' => 'success'
        ];

        return redirect(route('admin.appointments'))->with(['toast' => $toastData]);
    }

    public function getTimes(Request $request)
    {
        $this->authorize('admin_appointments_lists');

        $instructorId = $request->get('instructor_id');
        $meeting = Meeting::where('creator_id', $instructorId)->first();

        if (!$meeting) {
            return response()->json([]);
        }

        $times = MeetingTime::where('meeting_id', $meeting->id)->get(['id', 'day_label', 'time']);

        return response()->json($times);
    }
}
