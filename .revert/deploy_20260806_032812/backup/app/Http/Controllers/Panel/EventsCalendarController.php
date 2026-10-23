<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Mixins\RegistrationPackage\UserPackage;
use App\Models\Bundle;
use App\Models\InstallmentOrder;
use App\Models\InstallmentOrderPayment;
use App\Models\Event;
use App\Models\Meeting;
use App\Models\MeetingPackageSold;
use App\Models\MeetingTime;
use App\Models\Quiz;
use App\Models\ReserveMeeting;
use App\Models\RescheduleRequest;
use App\Models\Role;
use App\Models\Session;
use App\Models\SessionTranslation;
use App\Models\Subscribe;
use App\Models\Webinar;
use App\Models\WebinarAssignment;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Spatie\CalendarLinks\Link;

class EventsCalendarController extends Controller
{
    public $user;
    public $userBoughtWebinarsIds;

    /** Convenience: is the logged-in user an instructor or org? */
    private function isInstructor(): bool
    {
        $user = $this->user ?? auth()->user();
        return in_array($user->role_name, [Role::$teacher, Role::$organization]);
    }

    /** Convenience: is the logged-in user an admin? */
    private function isAdmin(): bool
    {
        $user = $this->user ?? auth()->user();
        return $user->role_name === Role::$admin;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $this->user = $user;
        $this->userBoughtWebinarsIds = $user->getPurchasedCoursesIds();


        $dayTimestamp = $request->get('date', time());

        $dayEvents = $this->handleEventsByDate($dayTimestamp);
        $getUpcomingEvents = $this->getUpcomingEvents();
        $upcomingEvents = $getUpcomingEvents['upcomingEvents'];

        $eventsWithTimestamp = $this->getAllEventsReturnWithTimestamp();

        $data = [
            'pageTitle' => trans('update.events_calendar'),
            'dayEvents' => $dayEvents,
            'dayTimestamp' => $dayTimestamp,
            'upcomingEvents' => $upcomingEvents,
            'eventsWithTimestamp' => $eventsWithTimestamp,
        ];

        return view('design_1.panel.events_calender.index', $data);
    }

    public function getEventsByDay(Request $request)
    {
        $this->validate($request, [
            'timestamp' => 'required',
        ]);

        $user = auth()->user();
        $this->user = $user;
        $this->userBoughtWebinarsIds = $user->getPurchasedCoursesIds();

        $dayTimestamp = $request->get('timestamp');
        $dayEvents = $this->handleEventsByDate($dayTimestamp);

        $html = (string)view()->make("design_1.panel.events_calender.day_events", [
            'dayEvents' => $dayEvents,
            'dayTimestamp' => $dayTimestamp,
        ]);

        return response()->json([
            'code' => 200,
            'html' => $html,
        ]);
    }

    public function getAllEventsReturnWithTimestamp()
    {
        $result = [];
        $events = $this->getAllEvents();

        if (!empty($events) and count($events)) {
            foreach ($events as $eventName => $eventItems) {
                if (!empty($eventItems) and is_array($eventItems)) {
                    foreach ($eventItems as $eventTimestamp => $eventItem) {
                        if (!empty($eventItem) and is_array($eventItem)) {
                            $startOfDayTimestamp = startOfDayTimestamp($eventTimestamp);
                            $endOfDayTimestamp = endOfDayTimestamp($eventTimestamp);

                            if ($startOfDayTimestamp) {
                                $result[$eventTimestamp] = [
                                    'title' => $eventName,
                                    'start_day' => $startOfDayTimestamp,
                                    'end_day' => $endOfDayTimestamp,
                                    ...$eventItem
                                ];
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function getUpcomingEvents($count = 5)
    {
        $result = [];
        $events = $this->getAllEvents();

        if (!empty($events) and count($events)) {
            foreach ($events as $eventName => $eventItems) {
                if (!empty($eventItems) and is_array($eventItems)) {
                    foreach ($eventItems as $eventTimestamp => $eventItem) {
                        if (!empty($eventItem) and is_array($eventItem)) {
                            $result[] = [
                                'title' => $eventName,
                                ...$eventItem
                            ];
                        }
                    }
                }
            }
        }

        $upcomingEvents = [];

        if (count($result) > 0) {
            uasort($result, function ($a, $b) {
                return $a['event_at'] <=> $b['event_at'];
            });

            $upcomingEvents = array_slice($result, 0, $count, true); // take 5 item
        }

        return [
            'upcomingEvents' => $upcomingEvents,
            'total' => (!empty($events['total'])) ? $events['total'] : 0,
        ];
    }

    private function handleEventsByDate($dateTimestamp)
    {
        $carbonDate = Carbon::createFromTimestamp($dateTimestamp);
        $carbonDate->setTimezone(getTimezone());
        $startAt = $carbonDate->startOfDay()->timestamp;
        $endAt = $carbonDate->endOfDay()->timestamp;

        return $this->getAllEvents($startAt, $endAt);
    }

    private function getAllEvents($startAt = null, $endAt = null)
    {
        $total = 0;
        $events = [];

        // Course Expiration
        $events['courses_expirations'] = $this->getCourseExpirationEvent($startAt, $endAt);
        $total += count($events['courses_expirations']);

        // Quiz Expiration
        $events['quiz_expirations'] = $this->getQuizExpirationEvent($startAt, $endAt);
        $total += count($events['quiz_expirations']);

        // Live Session
        $events['live_sessions'] = $this->getLiveSessionEvent($startAt, $endAt);
        $total += count($events['live_sessions']);

        // Assignment Expiration
        $events['assignment_expirations'] = $this->getAssignmentExpirationEvent($startAt, $endAt);
        $total += count($events['assignment_expirations']);

        // Bundle Expiration
        $events['bundle_expirations'] = $this->getBundleExpirationEvent($startAt, $endAt);
        $total += count($events['bundle_expirations']);

        // Subscription Expiration
        $events['subscription_expirations'] = $this->getSubscriptionExpirationEvent($startAt, $endAt);
        $total += count($events['subscription_expirations']);

        // Registration Package Expiration
        $events['registration_package_expirations'] = $this->getRegistrationPackageExpirationEvent($startAt, $endAt);
        $total += count($events['registration_package_expirations']);

        // Installment
        $events['installments'] = $this->getInstallmentExpirationEvent($startAt, $endAt);
        $total += count($events['installments']);

        // Meeting
        $events['meetings'] = $this->getMeetingExpirationEvent($startAt, $endAt);
        $total += count($events['meetings']);

        // Live Class Start
        $events['live_class_start'] = $this->getLiveClassStartEvent($startAt, $endAt);
        $total += count($events['live_class_start']);

        // Events Start Date
        $events['events_start_date'] = $this->getEventsStartDateEvent($startAt, $endAt);
        $total += count($events['events_start_date']);

        $events['total'] = $total;
        return $events;
    }

    private function getCourseExpirationEvent($startAt = null, $endAt = null)
    {
        $courses = [];

        if (!empty($this->userBoughtWebinarsIds) and count($this->userBoughtWebinarsIds)) {
            $webinars = Webinar::query()->whereIn('id', $this->userBoughtWebinarsIds)
                ->whereNotNull('access_days')
                ->get();

            foreach ($webinars as $webinar) {
                $expiredItem = false;
                $sale = $webinar->getSaleItem($this->user, true);


                if (!empty($sale)) {
                    $expireAt = $webinar->getExpiredAccessDays($sale->created_at, $sale->gift_id);

                    if (!empty($startAt) and !empty($endAt)) {
                        if ($expireAt >= $startAt and $expireAt <= $endAt) {
                            $expiredItem = true;
                        }
                    } elseif ($expireAt > time()) {
                        $expiredItem = true;
                    }

                    if ($expiredItem) {
                        $courses[$expireAt] = [
                            'subtitle' => $webinar->title,
                            'add_to_calendar_url' => $webinar->addToCalendarLink(),
                            'event_at' => $expireAt,
                            'time' => null,
                        ];;
                    }
                }
            }
        }

        return $courses;
    }

    private function getBundleExpirationEvent($startAt = null, $endAt = null)
    {
        $bundlesHasExpiration = [];
        $userBoughtBundlesIds = $this->user->getPurchasedBundlesIds();

        if (count($userBoughtBundlesIds)) {
            $bundles = Bundle::query()->whereIn('id', $userBoughtBundlesIds)
                ->whereNotNull('access_days')
                ->get();

            foreach ($bundles as $bundle) {
                $expiredItem = false;
                $sale = $bundle->getSaleItem($this->user);

                if (!empty($sale)) {
                    $expireAt = $bundle->getExpiredAccessDays($sale->created_at, $sale->gift_id);

                    if (!empty($startAt) and !empty($endAt)) {
                        if ($expireAt >= $startAt and $expireAt <= $endAt) {
                            $expiredItem = true;
                        }
                    } elseif ($expireAt > time()) {
                        $expiredItem = true;
                    }

                    if ($expiredItem) {
                        $bundlesHasExpiration[$expireAt] = [
                            'subtitle' => $bundle->title,
                            'add_to_calendar_url' => '',
                            'event_at' => $expireAt,
                            'time' => null,
                        ];;
                    }
                }
            }
        }

        return $bundlesHasExpiration;
    }

    private function getQuizExpirationEvent($startAt = null, $endAt = null)
    {
        $hasExpiration = [];

        if (!empty($this->userBoughtWebinarsIds) and count($this->userBoughtWebinarsIds)) {
            $quizzes = Quiz::whereIn('webinar_id', $this->userBoughtWebinarsIds)
                ->where('status', 'active')
                ->get();

            foreach ($quizzes as $quiz) {
                $expiredItem = false;
                $expireAt = $quiz->getExpireTimestamp($this->user);

                if (!empty($startAt) and !empty($endAt)) {
                    if ($expireAt >= $startAt and $expireAt <= $endAt) {
                        $expiredItem = true;
                    }
                } elseif ($expireAt > time()) {
                    $expiredItem = true;
                }

                if ($expiredItem) {
                    $title = $quiz->title;
                    if (!empty($quiz->webinar)) {
                        $title .= " - " . $quiz->webinar->title;
                    }

                    $hasExpiration[$expireAt] = [
                        'subtitle' => $title,
                        'add_to_calendar_url' => $this->addToCalendarLink($title, $expireAt),
                        'event_at' => $expireAt,
                        'time' => null,
                    ];
                }
            }
        }

        return $hasExpiration;
    }

    private function getLiveSessionEvent($startAt = null, $endAt = null)
    {
        $hasExpiration = [];

        if (!empty($this->userBoughtWebinarsIds) and count($this->userBoughtWebinarsIds)) {
            $sessions = Session::whereIn('webinar_id', $this->userBoughtWebinarsIds)
                ->where('status', 'active')
                ->where('date', '>=', time())
                ->get();

            foreach ($sessions as $session) {
                $expiredItem = false;
                $sessionDate = $session->date;

                if (!empty($startAt) and !empty($endAt)) {
                    if ($sessionDate >= $startAt and $sessionDate <= $endAt) {
                        $expiredItem = true;
                    }
                } elseif ($sessionDate > time()) {
                    $expiredItem = true;
                }

                if ($expiredItem) {
                    $title = $session->title;
                    if (!empty($session->webinar)) {
                        $title .= " - " . $session->webinar->title;
                    }

                    $hasExpiration[$sessionDate] = [
                        'subtitle' => $title,
                        'add_to_calendar_url' => $this->addToCalendarLink($title, $sessionDate),
                        'event_at' => $sessionDate,
                        'time' => dateTimeFormat($sessionDate, 'H:i'),
                    ];
                }
            }
        }

        return $hasExpiration;
    }

    private function getAssignmentExpirationEvent($startAt = null, $endAt = null)
    {
        $hasExpiration = [];

        if (!empty($this->userBoughtWebinarsIds) and count($this->userBoughtWebinarsIds)) {
            $assignments = WebinarAssignment::whereIn('webinar_id', $this->userBoughtWebinarsIds)
                ->where('status', 'active')
                ->get();

            foreach ($assignments as $assignment) {
                $expiredItem = false;
                $expireAt = $assignment->getDeadlineTimestamp($this->user);

                if (!empty($expireAt)) {
                    if (!empty($startAt) and !empty($endAt)) {
                        if ($expireAt >= $startAt and $expireAt <= $endAt) {
                            $expiredItem = true;
                        }
                    } elseif ($expireAt > time()) {
                        $expiredItem = true;
                    }
                }

                if ($expiredItem) {
                    $title = $assignment->title;
                    if (!empty($assignment->webinar)) {
                        $title .= " - " . $assignment->webinar->title;
                    }

                    $hasExpiration[$expireAt] = [
                        'subtitle' => $title,
                        'add_to_calendar_url' => $this->addToCalendarLink($title, $expireAt),
                        'event_at' => $expireAt,
                        'time' => null,
                    ];
                }
            }
        }

        return $hasExpiration;
    }

    private function getSubscriptionExpirationEvent($startAt = null, $endAt = null)
    {
        $hasExpiration = [];

        $activeSubscribe = Subscribe::getActiveSubscribe($this->user->id);

        if (!empty($activeSubscribe) and !empty($activeSubscribe->expire_at) and $activeSubscribe->expire_at > time()) {
            $expiredItem = false;
            $expireAt = $activeSubscribe->expire_at;

            if (!empty($startAt) and !empty($endAt)) {
                if ($expireAt >= $startAt and $expireAt <= $endAt) {
                    $expiredItem = true;
                }
            } elseif ($expireAt > time()) {
                $expiredItem = true;
            }

            if ($expiredItem) {
                $title = $activeSubscribe->title;

                $hasExpiration[$expireAt] = [
                    'subtitle' => $title,
                    'add_to_calendar_url' => $this->addToCalendarLink($title, $expireAt),
                    'event_at' => $expireAt,
                    'time' => null,
                ];
            }
        }

        return $hasExpiration;
    }

    private function getRegistrationPackageExpirationEvent($startAt = null, $endAt = null)
    {
        $hasExpiration = [];

        $userPackage = new UserPackage($this->user);
        $activePackage = $userPackage->getPackage();

        if (!empty($activePackage) and !empty($activePackage->expire_at) and $activePackage->expire_at > time()) {
            $expiredItem = false;
            $expireAt = $activePackage->expire_at;

            if (!empty($startAt) and !empty($endAt)) {
                if ($expireAt >= $startAt and $expireAt <= $endAt) {
                    $expiredItem = true;
                }
            } elseif ($expireAt > time()) {
                $expiredItem = true;
            }

            if ($expiredItem) {
                $title = $activePackage->title;

                $hasExpiration[$expireAt] = [
                    'subtitle' => $title,
                    'add_to_calendar_url' => $this->addToCalendarLink($title, $expireAt),
                    'event_at' => $expireAt,
                    'time' => null,
                ];
            }
        }

        return $hasExpiration;
    }

    private function getInstallmentExpirationEvent($startAt = null, $endAt = null)
    {
        $installmentOrders = InstallmentOrder::query()
            ->where('user_id', $this->user->id)
            ->where('status', '!=', 'paying')
            ->with([
                'selectedInstallment' => function ($query) {
                    $query->with([
                        'steps' => function ($query) {
                            $query->orderBy('deadline', 'asc');
                        }
                    ]);
                    $query->withCount([
                        'steps'
                    ]);
                }
            ])
            ->get();

        $hasExpiration = [];

        foreach ($installmentOrders as $installmentOrder) {

            foreach ($installmentOrder->selectedInstallment->steps as $step) {
                $expiredItem = false;

                $payment = InstallmentOrderPayment::query()
                    ->where('installment_order_id', $installmentOrder->id)
                    ->where('selected_installment_step_id', $step->id)
                    ->where('status', 'paid')
                    ->first();

                if (empty($payment)) {
                    $expireAt = ($step->deadline * 86400) + $installmentOrder->created_at;

                    if (!empty($startAt) and !empty($endAt)) {
                        if ($expireAt >= $startAt and $expireAt <= $endAt) {
                            $expiredItem = true;
                        }
                    } elseif ($expireAt > time()) {
                        $expiredItem = true;
                    }

                    if ($expiredItem) {
                        $title = $step->installmentStep->title;

                        if (!empty($step->selectedInstallment->order) and !empty($step->selectedInstallment->order->webinar)) {
                            $title .= " - " . $step->selectedInstallment->order->webinar->title;
                        }


                        $hasExpiration[$expireAt] = [
                            'subtitle' => $title,
                            'add_to_calendar_url' => $this->addToCalendarLink($title, $expireAt),
                            'event_at' => $expireAt,
                            'time' => null,
                        ];
                    }
                }
            }

        }

        return $hasExpiration;
    }

    private function getMeetingExpirationEvent($startAt = null, $endAt = null)
    {
        $hasExpiration = [];
        $user = $this->user;

        // Build query — for instructor/admin also pull their students' bookings
        $query = ReserveMeeting::query()
            ->whereNotNull('reserved_at')
            ->where('date', '>', time() - 86400); // include today's past ones too

        if ($this->isAdmin()) {
            // Admin sees everything
        } elseif ($this->isInstructor()) {
            // Instructor sees their own student bookings (creator_id) + meetings they booked as a student
            $meetingIds = Meeting::where('creator_id', $user->id)->pluck('id');
            $query->where(function ($q) use ($meetingIds, $user) {
                $q->whereIn('meeting_id', $meetingIds)  // bookings made WITH this instructor
                  ->orWhere('user_id', $user->id);       // meetings they booked as student
            });
        } else {
            // Regular student — only their own bookings
            $query->where('user_id', $user->id);
        }

        $reserveMeetings = $query->with([
            'meeting.creator',
            'user',
            'session',
        ])->get();

        foreach ($reserveMeetings as $reserveMeeting) {
            $expiredItem = false;
            $expireAt = $reserveMeeting->start_at;

            if (!empty($startAt) and !empty($endAt)) {
                if ($expireAt >= $startAt and $expireAt <= $endAt) {
                    $expiredItem = true;
                }
            } else {
                $expiredItem = true; // for upcoming events list — include all
            }

            if ($expiredItem) {
                // Label: if instructor/admin, show student name; if student, show instructor name
                if ($this->isInstructor() || $this->isAdmin()) {
                    $subtitle = !empty($reserveMeeting->user) ? $reserveMeeting->user->full_name : 'Student';
                    $subtitle .= ' — ' . ($reserveMeeting->is_demo ? 'Demo' : 'Session');
                } else {
                    $subtitle = !empty($reserveMeeting->meeting->creator) ? $reserveMeeting->meeting->creator->full_name : 'Instructor';
                }

                // Status badge color
                $statusColor = match($reserveMeeting->status) {
                    'open'     => 'success',
                    'finished' => 'secondary',
                    'canceled' => 'danger',
                    default    => 'warning',
                };

                // Pending reschedule request?
                $pendingReschedule = RescheduleRequest::where('reserve_meeting_id', $reserveMeeting->id)
                    ->where('status', RescheduleRequest::$pending)
                    ->exists();

                $hasExpiration[] = [
                    'reserve_meeting_id' => $reserveMeeting->id,
                    'subtitle'           => $subtitle,
                    'add_to_calendar_url'=> $this->addToCalendarLink("Meeting - $subtitle", $expireAt),
                    'event_at'           => $expireAt,
                    'time'               => dateTimeFormat($reserveMeeting->start_at, 'H:i') . ' - ' . dateTimeFormat($reserveMeeting->end_at, 'H:i'),
                    'status'             => $reserveMeeting->status,
                    'status_color'       => $statusColor,
                    'has_link'           => !empty($reserveMeeting->link),
                    'link'               => $reserveMeeting->link,
                    'is_demo'            => (bool)$reserveMeeting->is_demo,
                    'has_session'        => !empty($reserveMeeting->session),
                    'pending_reschedule' => $pendingReschedule,
                    'student_id'         => $reserveMeeting->user_id,
                    'student_name'       => !empty($reserveMeeting->user) ? $reserveMeeting->user->full_name : '',
                    'instructor_name'    => !empty($reserveMeeting->meeting->creator) ? $reserveMeeting->meeting->creator->full_name : '',
                ];
            }
        }

        $meetingPackagesSoldIds = MeetingPackageSold::query()
            ->where('user_id', $this->user->id)
            ->pluck('id')
            ->toArray();

        if (!empty($meetingPackagesSoldIds)) {
            $meetingPackageScheduledSessions = Session::query()
                ->whereIn('meeting_package_sold_id', $meetingPackagesSoldIds)
                ->where('status', '!=', 'finished')
                ->whereNotNull('date')
                ->with(['meetingPackageSold.meetingPackage'])
                ->get();

            foreach ($meetingPackageScheduledSessions as $scheduledSession) {
                $expiredItem = false;
                $expireAt = $scheduledSession->date;

                if (!empty($startAt) and !empty($endAt)) {
                    if ($expireAt >= $startAt and $expireAt <= $endAt) {
                        $expiredItem = true;
                    }
                } elseif ($expireAt > time()) {
                    $expiredItem = true;
                }

                if ($expiredItem) {
                    $title = $scheduledSession->meetingPackageSold->meetingPackage->title;
                    $title .= " (" . trans('update.session') . " #" . $scheduledSession->order . ")";

                    $hasExpiration[] = [
                        'reserve_meeting_id' => null,
                        'subtitle'    => $title,
                        'add_to_calendar_url' => $this->addToCalendarLink($title, $expireAt),
                        'event_at'    => $expireAt,
                        'time'        => dateTimeFormat($scheduledSession->date, 'H:i') . ' - ' . dateTimeFormat(($scheduledSession->date + ($scheduledSession->duration * 60)), 'H:i'),
                        'status'      => 'open',
                        'status_color'=> 'success',
                        'has_link'    => false,
                        'link'        => null,
                        'is_demo'     => false,
                        'has_session' => true,
                        'pending_reschedule' => false,
                    ];
                }
            }
        }

        return $hasExpiration;
    }

    private function getLiveClassStartEvent($startAt = null, $endAt = null)
    {
        $hasExpiration = [];

        if (!empty($this->userBoughtWebinarsIds) and count($this->userBoughtWebinarsIds)) {
            $webinars = Webinar::query()->whereIn('id', $this->userBoughtWebinarsIds)
                ->whereNotNull('access_days')
                ->where('type', Webinar::$webinar)
                ->where('start_date', '>', time())
                ->get();

            foreach ($webinars as $webinar) {
                $expiredItem = false;
                $expireAt = $webinar->start_date;

                if (!empty($startAt) and !empty($endAt)) {
                    if ($expireAt >= $startAt and $expireAt <= $endAt) {
                        $expiredItem = true;
                    }
                } elseif ($expireAt > time()) {
                    $expiredItem = true;
                }

                if ($expiredItem) {
                    $title = $webinar->title;

                    $hasExpiration[$expireAt] = [
                        'subtitle' => $title,
                        'add_to_calendar_url' => $this->addToCalendarLink($title, $expireAt),
                        'event_at' => $expireAt,
                        'time' => dateTimeFormat($webinar->start_date, 'H:i'),
                    ];
                }
            }
        }

        return $hasExpiration;
    }

    public function getEventsStartDateEvent($startAt = null, $endAt = null)
    {
        $hasExpiration = [];

        $eventsQuery = Event::query()
            ->where(function ($query) {
                // User Purchased
                $query->whereHas('tickets', function ($query) {
                    $query->whereHas('eventTicketsSolds', function ($query) {
                        $query->where('user_id', $this->user->id);
                    });
                });

                // User Created
                $query->orWhere('creator_id', $this->user->id);
            })
            ->whereNotNull('start_date');

        if (!empty($startAt) and !empty($endAt)) {
            $eventsQuery->where(function ($query) use ($startAt, $endAt) {
                $query->where('start_date', '>=', $startAt);
                $query->where('start_date', '<', $endAt);
            });
        } else {
            $eventsQuery->where('start_date', '>=', time());
        }

        $events = $eventsQuery->get();

        foreach ($events as $event) {
            $title = $event->title;
            $eventStartDate = $event->start_date;

            $hasExpiration[$eventStartDate] = [
                'subtitle' => $title,
                'add_to_calendar_url' => $this->addToCalendarLink($title, $eventStartDate),
                'event_at' => $eventStartDate,
                'time' => dateTimeFormat($eventStartDate, 'H:i'),
            ];
        }

        return $hasExpiration;
    }

    private function addToCalendarLink($title, $timestamp)
    {
        $date = \DateTime::createFromFormat('j M Y H:i', dateTimeFormat($timestamp, 'j M Y H:i', false));
        $link = Link::create($title, $date, $date);
        return $link->google();
    }

    // ─────────────────────────────────────────────────────────────────────
    //  JSON ENDPOINT — FullCalendar.js event feed
    // ─────────────────────────────────────────────────────────────────────
    public function getCalendarJson(Request $request)
    {
        $user = auth()->user();
        $this->user = $user;
        $this->userBoughtWebinarsIds = $user->getPurchasedCoursesIds();

        $start = $request->get('start') ? Carbon::parse($request->get('start'))->timestamp : null;
        $end   = $request->get('end')   ? Carbon::parse($request->get('end'))->timestamp   : null;

        $meetings = $this->getMeetingExpirationEvent($start, $end);

        $events = [];
        foreach ($meetings as $m) {
            $color = match($m['status'] ?? 'pending') {
                'open'     => '#22c55e',
                'finished' => '#6b7280',
                'canceled' => '#ef4444',
                default    => '#f59e0b',
            };
            if (!empty($m['is_demo'])) $color = '#8b5cf6';

            $events[] = [
                'id'            => $m['reserve_meeting_id'],
                'title'         => $m['subtitle'],
                'start'         => date('Y-m-d\TH:i:s', $m['event_at']),
                'color'         => $color,
                'extendedProps' => [
                    'status'             => $m['status'] ?? 'pending',
                    'status_color'       => $m['status_color'] ?? 'warning',
                    'has_link'           => $m['has_link'] ?? false,
                    'link'               => $m['link'] ?? null,
                    'is_demo'            => $m['is_demo'] ?? false,
                    'has_session'        => $m['has_session'] ?? false,
                    'pending_reschedule' => $m['pending_reschedule'] ?? false,
                    'time'               => $m['time'] ?? '',
                    'student_id'         => $m['student_id'] ?? null,
                    'student_name'       => $m['student_name'] ?? '',
                    'instructor_name'    => $m['instructor_name'] ?? '',
                ],
            ];
        }

        return response()->json($events);
    }

    // ─────────────────────────────────────────────────────────────────────
    //  STUDENT SEARCH (autocomplete)
    // ─────────────────────────────────────────────────────────────────────
    public function studentSearch(Request $request)
    {
        $user = auth()->user();
        $q = $request->get('q', '');

        $query = User::where('role_name', Role::$user)
            ->where(function ($query) use ($q) {
                $query->where('full_name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            });

        // If instructor, filter by their own students only
        if ($user->role_name !== Role::$admin) {
            $webinarIds = Webinar::where('creator_id', $user->id)->pluck('id');
            $webinarBuyerIds = Sale::whereIn('webinar_id', $webinarIds)
                ->whereNull('refund_at')
                ->pluck('buyer_id')
                ->toArray();

            $bundleIds = Bundle::where('creator_id', $user->id)->pluck('id');
            $bundleBuyerIds = Sale::whereIn('bundle_id', $bundleIds)
                ->whereNull('refund_at')
                ->pluck('buyer_id')
                ->toArray();

            $meeting = Meeting::where('creator_id', $user->id)->first();
            $meetingBuyerIds = [];
            if ($meeting) {
                $meetingBuyerIds = ReserveMeeting::where('meeting_id', $meeting->id)
                    ->pluck('user_id')
                    ->toArray();
            }

            $myStudentIds = array_unique(array_merge($webinarBuyerIds, $bundleBuyerIds, $meetingBuyerIds));

            $query->whereIn('id', $myStudentIds);
        }

        $users = $query->select('id', 'full_name', 'email')
            ->limit(10)->get();

        return response()->json($users);
    }

    // ─────────────────────────────────────────────────────────────────────
    //  SCHEDULE A CLASS FOR A STUDENT
    // ─────────────────────────────────────────────────────────────────────
    public function scheduleClassForStudent(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'student_ids'   => 'required|array',
            'student_ids.*' => 'exists:users,id',
            'day'           => 'required|date',
            'start_time'    => 'required',
            'end_time'      => 'required',
        ]);

        $studentIds = $request->student_ids;
        $day = $request->day;

        $meeting = Meeting::where('creator_id', $user->id)->first();
        if (!$meeting) {
            $meeting = Meeting::create(['creator_id' => $user->id, 'created_at' => time()]);
        }

        $startAt = strtotime($day . ' ' . $request->start_time);
        $endAt   = strtotime($day . ' ' . $request->end_time);
        $price   = $request->get('price', 0);
        $isDemo  = (bool)$request->get('is_demo', false);

        $teamsLink = null;
        $session = null;

        // Loop over each selected student to create bookings
        foreach ($studentIds as $index => $studentId) {
            $student = User::find($studentId);
            if (!$student) continue;

            $reserve = ReserveMeeting::create([
                'meeting_id'  => $meeting->id,
                'user_id'     => $student->id,
                'day'         => $day,
                'start_at'    => $startAt,
                'end_at'      => $endAt,
                'date'        => $startAt,
                'paid_amount' => $price,
                'status'      => ReserveMeeting::$pending,
                'reserved_at' => time(),
                'is_demo'     => $isDemo ? 1 : 0,
                'created_at'  => time(),
            ]);

            // Auto-generate Teams meeting on the first iteration
            if ($index === 0) {
                try {
                    $session = Session::create([
                        'creator_id'         => $user->id,
                        'reserve_meeting_id' => $reserve->id,
                        'date'               => $startAt,
                        'duration'           => max(15, (($endAt - $startAt) / 60)),
                        'session_api'        => 'microsoft_teams',
                        'status'             => Session::$Active,
                        'created_at'         => time(),
                    ]);
                    SessionTranslation::create([
                        'session_id'  => $session->id,
                        'locale'      => mb_strtolower(app()->getLocale()),
                        'title'       => 'Group Class with ' . $user->full_name,
                        'description' => 'Group Class with ' . $user->full_name,
                    ]);
                    $session->title = 'Group Class with ' . $user->full_name;
                    $teams = new \App\Sessions\MicrosoftTeams();
                    $teams->makeMeeting($session);
                    $teamsLink = $session->link;
                    
                    $reserve->update(['link' => $teamsLink, 'status' => ReserveMeeting::$open]);
                } catch (\Exception $e) {
                    // Teams failed — booking created without link
                }
            } else {
                // For other students, reuse the Teams link and associate the session
                if ($teamsLink) {
                    $reserve->update(['link' => $teamsLink, 'status' => ReserveMeeting::$open]);
                    if ($session) {
                        // Associate session in database if needed (optional)
                    }
                }
            }

            // Send notification to the student
            if ($teamsLink) {
                sendNotification('new_appointment_link', [
                    '[link]'            => $teamsLink,
                    '[instructor.name]' => $user->full_name,
                    '[time.date]'       => $day . ' ' . $request->start_time,
                ], $student->id);
            }

            // Recurring sessions for this student
            $repeat = $request->get('repeat', 0);
            $repeatType  = $request->get('repeat_type', 'weekly');
            $repeatCount = min((int)$request->get('repeat_count', 1), 52);
            if ($repeat && $repeatCount > 1) {
                $interval = $repeatType === 'daily' ? 1 : 7;
                for ($i = 1; $i < $repeatCount; $i++) {
                    $nextDay     = date('Y-m-d', strtotime($day . ' +' . ($i * $interval) . ' days'));
                    $nextStartAt = strtotime($nextDay . ' ' . $request->start_time);
                    $nextEndAt   = strtotime($nextDay . ' ' . $request->end_time);
                    $nextReserve = ReserveMeeting::create([
                        'meeting_id'  => $meeting->id,
                        'user_id'     => $student->id,
                        'day'         => $nextDay,
                        'start_at'    => $nextStartAt,
                        'end_at'      => $nextEndAt,
                        'date'        => $nextStartAt,
                        'paid_amount' => $price,
                        'status'      => $teamsLink ? ReserveMeeting::$open : ReserveMeeting::$pending,
                        'link'        => $teamsLink,
                        'reserved_at' => time(),
                        'is_demo'     => $isDemo ? 1 : 0,
                        'created_at'  => time(),
                    ]);
                }
            }
        }

        return response()->json(['code' => 200, 'message' => 'Class scheduled for ' . count($studentIds) . ' student(s).']);
    }

    // ─────────────────────────────────────────────────────────────────────
    //  CANCEL A BOOKING
    // ─────────────────────────────────────────────────────────────────────
    public function cancelBooking(Request $request, $id)
    {
        $user = auth()->user();
        $reserve = ReserveMeeting::with('meeting')->findOrFail($id);
        $isInstructorOfBooking = $reserve->meeting && $reserve->meeting->creator_id === $user->id;
        $isOwner  = $reserve->user_id === $user->id;
        $isAdmin  = $user->role_name === Role::$admin;

        if (!$isAdmin && !$isInstructorOfBooking && !$isOwner) {
            return response()->json(['code' => 403, 'message' => 'Unauthorized'], 403);
        }
        if ($reserve->status === ReserveMeeting::$finished) {
            return response()->json(['code' => 422, 'message' => 'Cannot cancel a finished booking.'], 422);
        }

        $cancelledBy = $isAdmin ? 'admin' : ($isInstructorOfBooking ? 'instructor' : 'student');
        $reserve->update([
            'status'        => ReserveMeeting::$canceled,
            'cancelled_by'  => $cancelledBy,
            'cancelled_at'  => time(),
            'cancel_reason' => $request->get('reason', ''),
        ]);

        $notifyUserId = $isOwner ? ($reserve->meeting->creator_id ?? null) : $reserve->user_id;
        if ($notifyUserId) {
            sendNotification('cancel_reserve_meeting', [
                '[instructor.name]' => !empty($reserve->meeting->creator) ? $reserve->meeting->creator->full_name : '',
                '[time.date]'       => $reserve->day,
            ], $notifyUserId);
        }
        return response()->json(['code' => 200, 'message' => 'Booking cancelled.']);
    }

    // ─────────────────────────────────────────────────────────────────────
    //  RESCHEDULE (instructor / admin)
    // ─────────────────────────────────────────────────────────────────────
    public function rescheduleBooking(Request $request, $id)
    {
        $user = auth()->user();
        $this->validate($request, [
            'new_day'        => 'required|date',
            'new_start_time' => 'required',
            'new_end_time'   => 'required',
        ]);
        $reserve = ReserveMeeting::with('meeting')->findOrFail($id);
        $isInstructorOfBooking = $reserve->meeting && $reserve->meeting->creator_id === $user->id;
        $isAdmin  = $user->role_name === Role::$admin;
        if (!$isAdmin && !$isInstructorOfBooking) {
            return response()->json(['code' => 403, 'message' => 'Unauthorized'], 403);
        }
        $newDay     = $request->new_day;
        $newStartAt = strtotime($newDay . ' ' . $request->new_start_time);
        $newEndAt   = strtotime($newDay . ' ' . $request->new_end_time);
        $reserve->update(['day' => $newDay, 'start_at' => $newStartAt, 'end_at' => $newEndAt, 'date' => $newStartAt]);
        RescheduleRequest::where('reserve_meeting_id', $id)->where('status', RescheduleRequest::$pending)
            ->update(['status' => RescheduleRequest::$approved, 'updated_at' => time()]);
        sendNotification('reserve_meeting_time_changed', [
            '[instructor.name]' => $user->full_name,
            '[time.date]'       => $newDay . ' ' . $request->new_start_time,
        ], $reserve->user_id);
        return response()->json(['code' => 200, 'message' => 'Booking rescheduled and student notified.']);
    }

    // ─────────────────────────────────────────────────────────────────────
    //  STUDENT REQUESTS RESCHEDULE
    // ─────────────────────────────────────────────────────────────────────
    public function requestReschedule(Request $request, $id)
    {
        $user = auth()->user();
        $this->validate($request, ['new_day' => 'required|date', 'new_start_time' => 'required']);
        $reserve = ReserveMeeting::with('meeting')->findOrFail($id);
        if ($reserve->user_id !== $user->id) {
            return response()->json(['code' => 403, 'message' => 'Unauthorized'], 403);
        }
        RescheduleRequest::where('reserve_meeting_id', $id)->where('status', RescheduleRequest::$pending)->delete();
        $newDay     = $request->new_day;
        $newStartAt = strtotime($newDay . ' ' . $request->new_start_time);
        RescheduleRequest::create([
            'reserve_meeting_id' => $id,
            'requested_by'       => $user->id,
            'new_day'            => $newDay,
            'new_start_at'       => $newStartAt,
            'note'               => $request->get('note', ''),
            'status'             => RescheduleRequest::$pending,
            'created_at'         => time(),
        ]);
        $instructorId = $reserve->meeting->creator_id ?? null;
        if ($instructorId) {
            sendNotification('new_reminder', [
                '[u.name]'  => $user->full_name,
                '[message]' => $user->full_name . ' requested reschedule to ' . $newDay,
            ], $instructorId);
        }
        return response()->json(['code' => 200, 'message' => 'Reschedule request sent to instructor.']);
    }

    // ─────────────────────────────────────────────────────────────────────
    //  APPROVE RESCHEDULE REQUEST
    // ─────────────────────────────────────────────────────────────────────
    public function approveReschedule(Request $request, $requestId)
    {
        $user = auth()->user();
        $rr = RescheduleRequest::with('reserveMeeting.meeting')->findOrFail($requestId);
        $isInstructorOfBooking = $rr->reserveMeeting->meeting && $rr->reserveMeeting->meeting->creator_id === $user->id;
        $isAdmin = $user->role_name === Role::$admin;
        if (!$isAdmin && !$isInstructorOfBooking) {
            return response()->json(['code' => 403, 'message' => 'Unauthorized'], 403);
        }
        $reserve = $rr->reserveMeeting;
        $reserve->update([
            'day'      => $rr->new_day,
            'start_at' => $rr->new_start_at,
            'end_at'   => $rr->new_end_at ?? ($rr->new_start_at + 3600),
            'date'     => $rr->new_start_at,
        ]);
        $rr->update(['status' => RescheduleRequest::$approved, 'updated_at' => time()]);
        sendNotification('reserve_meeting_time_changed', [
            '[instructor.name]' => $user->full_name,
            '[time.date]'       => $rr->new_day,
        ], $reserve->user_id);
        return response()->json(['code' => 200, 'message' => 'Reschedule approved.']);
    }

    // ─────────────────────────────────────────────────────────────────────
    //  REJECT RESCHEDULE REQUEST
    // ─────────────────────────────────────────────────────────────────────
    public function rejectReschedule(Request $request, $requestId)
    {
        $user = auth()->user();
        $rr = RescheduleRequest::with('reserveMeeting.meeting.creator', 'reserveMeeting.user')->findOrFail($requestId);
        $isInstructorOfBooking = $rr->reserveMeeting->meeting && $rr->reserveMeeting->meeting->creator_id === $user->id;
        $isAdmin = $user->role_name === Role::$admin;
        if (!$isAdmin && !$isInstructorOfBooking) {
            return response()->json(['code' => 403, 'message' => 'Unauthorized'], 403);
        }
        $rr->update(['status' => RescheduleRequest::$rejected, 'updated_at' => time()]);
        sendNotification('new_reminder', [
            '[u.name]'  => $rr->reserveMeeting->user->full_name ?? '',
            '[message]' => 'Your reschedule request for ' . $rr->new_day . ' was declined.',
        ], $rr->reserveMeeting->user_id);
        return response()->json(['code' => 200, 'message' => 'Reschedule request rejected.']);
    }

    // ─────────────────────────────────────────────────────────────────────
    //  GET PENDING RESCHEDULE REQUESTS (instructor/admin sidebar)
    // ─────────────────────────────────────────────────────────────────────
    public function getRescheduleRequests()
    {
        $user = auth()->user();
        $query = RescheduleRequest::with(['reserveMeeting.meeting.creator', 'requestedBy'])
            ->where('status', RescheduleRequest::$pending);
        if ($user->role_name !== Role::$admin) {
            $meetingIds = Meeting::where('creator_id', $user->id)->pluck('id');
            $reserveIds = ReserveMeeting::whereIn('meeting_id', $meetingIds)->pluck('id');
            $query->whereIn('reserve_meeting_id', $reserveIds);
        }
        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    // ─────────────────────────────────────────────────────────────────────
    //  MARK ATTENDANCE
    // ─────────────────────────────────────────────────────────────────────
    public function markAttendance(Request $request, $id)
    {
        $user = auth()->user();
        $this->validate($request, ['attendance_status' => 'required|in:present,absent,excused']);
        $reserve = ReserveMeeting::with('meeting')->findOrFail($id);
        $isInstructorOfBooking = $reserve->meeting && $reserve->meeting->creator_id === $user->id;
        $isAdmin = $user->role_name === Role::$admin;
        if (!$isAdmin && !$isInstructorOfBooking) {
            return response()->json(['code' => 403, 'message' => 'Unauthorized'], 403);
        }
        $reserve->update(['attendance_status' => $request->attendance_status, 'status' => ReserveMeeting::$finished]);
        return response()->json(['code' => 200, 'message' => 'Attendance marked.']);
    }

    // ─────────────────────────────────────────────────────────────────────
    //  RESEND INVITE
    // ─────────────────────────────────────────────────────────────────────
    public function resendInvite($id)
    {
        $user = auth()->user();
        $reserve = ReserveMeeting::with(['meeting.creator', 'user'])->findOrFail($id);
        $isInstructorOfBooking = $reserve->meeting && $reserve->meeting->creator_id === $user->id;
        $isAdmin = $user->role_name === Role::$admin;
        if (!$isAdmin && !$isInstructorOfBooking) {
            return response()->json(['code' => 403, 'message' => 'Unauthorized'], 403);
        }
        if (empty($reserve->link)) {
            return response()->json(['code' => 422, 'message' => 'No link set. Start the session first.'], 422);
        }
        sendNotification('new_appointment_link', [
            '[link]'            => $reserve->link,
            '[instructor.name]' => !empty($reserve->meeting->creator) ? $reserve->meeting->creator->full_name : $user->full_name,
            '[time.date]'       => $reserve->day,
        ], $reserve->user_id);
        return response()->json(['code' => 200, 'message' => 'Invite resent to ' . ($reserve->user->full_name ?? 'student') . '.']);
    }

    // ─────────────────────────────────────────────────────────────────────
    //  BOOKING DETAIL (for event-click popup)
    // ─────────────────────────────────────────────────────────────────────
    public function getBookingDetail($id)
    {
        $user = auth()->user();
        $reserve = ReserveMeeting::with(['meeting.creator', 'user', 'session'])->findOrFail($id);
        $isInstructorOfBooking = $reserve->meeting && $reserve->meeting->creator_id === $user->id;
        $isAdmin  = $user->role_name === Role::$admin;
        $isOwner  = $reserve->user_id === $user->id;
        if (!$isAdmin && !$isInstructorOfBooking && !$isOwner) {
            return response()->json(['code' => 403, 'message' => 'Unauthorized'], 403);
        }
        $pendingRequest = RescheduleRequest::where('reserve_meeting_id', $id)
            ->where('status', RescheduleRequest::$pending)
            ->with('requestedBy')->first();
        return response()->json([
            'id'                     => $reserve->id,
            'status'                 => $reserve->status,
            'day'                    => $reserve->day,
            'time'                   => dateTimeFormat($reserve->start_at, 'H:i') . ' - ' . dateTimeFormat($reserve->end_at, 'H:i'),
            'link'                   => $reserve->link,
            'is_demo'                => (bool)$reserve->is_demo,
            'student_name'           => !empty($reserve->user) ? $reserve->user->full_name : '',
            'student_email'          => !empty($reserve->user) ? $reserve->user->email : '',
            'instructor_name'        => !empty($reserve->meeting->creator) ? $reserve->meeting->creator->full_name : '',
            'attendance_status'      => $reserve->attendance_status,
            'cancelled_by'           => $reserve->cancelled_by,
            'cancel_reason'          => $reserve->cancel_reason,
            'pending_reschedule'     => $pendingRequest ? [
                'id'      => $pendingRequest->id,
                'new_day' => $pendingRequest->new_day,
                'new_start'=> $pendingRequest->new_start_at ? dateTimeFormat($pendingRequest->new_start_at, 'H:i') : null,
                'note'    => $pendingRequest->note,
                'by'      => !empty($pendingRequest->requestedBy) ? $pendingRequest->requestedBy->full_name : '',
            ] : null,
            'can_join'               => $reserve->status === 'open' && !empty($reserve->link),
            'can_cancel'             => in_array($reserve->status, ['pending', 'open']),
            'can_reschedule'         => $isInstructorOfBooking || $isAdmin,
            'can_request_reschedule' => $isOwner && in_array($reserve->status, ['pending', 'open']),
            'can_mark_attendance'    => ($isInstructorOfBooking || $isAdmin) && $reserve->status === ReserveMeeting::$open,
            'can_resend_invite'      => ($isInstructorOfBooking || $isAdmin) && !empty($reserve->link),
        ]);
    }

}
