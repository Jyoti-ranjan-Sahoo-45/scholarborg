<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Panel\Traits\DashboardTrait;
use App\Mixins\RegistrationPackage\UserPackage;
use App\Models\Comment;
use App\Models\Gift;
use App\Models\Meeting;
use App\Models\ReserveMeeting;
use App\Models\Sale;
use App\Models\Subscribe;
use App\Models\Support;
use App\Models\Webinar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use DashboardTrait;

    public function index(Request $request)
    {
        $user = auth()->user();

        // Check if parent user
        if ($user->isParent()) {
            $proxyStudent = $user->getActiveProxyStudent();
            if (!$proxyStudent) {
                return redirect('/panel/parent/dashboard');
            }
            // Parent is in Student View mode for proxy student
            $targetUser = $proxyStudent;
        } else {
            $targetUser = $user;
        }

        $data = [
            'pageTitle' => trans('panel.dashboard'),
        ];

        if ($user->isParent() && !empty($proxyStudent)) {
            $data['is_parent_proxy'] = true;
            $data['proxyStudent'] = $proxyStudent;
        }

        if ($targetUser->isUser() || (!empty($proxyStudent) && $proxyStudent->isUser())) {
            $data = array_merge($data, $this->getStudentDashboardData($request, $targetUser));
        } else {
            $data = array_merge($data, $this->getInstructorDashboardData($request, $targetUser));
        }

        // Upcoming Events
        $data = array_merge($data, $this->handleDashboardUpcomingEvents($targetUser));

        // Gifts Modal
        $data['giftModal'] = $this->showGiftModal($targetUser);


        $generalSettings = getGeneralSettings();
        $rtlLanguages = !empty($generalSettings['rtl_languages']) ? $generalSettings['rtl_languages'] : [];
        $data['isRtl'] = ((in_array(mb_strtoupper(app()->getLocale()), $rtlLanguages)) or (!empty($generalSettings['rtl_layout']) and $generalSettings['rtl_layout'] == 1));

        return view('design_1.panel.dashboard.index', $data);
    }

    private function getStudentDashboardData(Request $request, $user): array
    {
        $data = [];

        $data['activeSubscribe'] = Subscribe::getActiveSubscribe($user->id);
        $data['authUserBalanceCharge'] = $user->getAccountingCharge();
        $data['authUserReadyPayout'] = $user->getPayout();


        $userBoughtWebinarsIds = $user->getPurchasedCoursesIds();

        // hello_box
        $data['helloBox'] = $this->getStudentHelloBoxData($user, $userBoughtWebinarsIds);

        // Courses Overview
        $data['coursesOverview'] = $this->getStudentCoursesOverviewData($user, $userBoughtWebinarsIds);

        // My Assignments
        $data['myAssignments'] = $this->getStudentMyAssignmentsData($user, $userBoughtWebinarsIds);

        // Learning Activity
        $data['learningActivity'] = $this->getStudentLearningActivityData($user, $userBoughtWebinarsIds);

        // Noticeboard
        $data['unreadNoticeboards'] = $user->getUnreadNoticeboards();

        // Support Messages
        $data['supportMessages'] = $this->getStudentSupportMessagesData($user, $userBoughtWebinarsIds);

        // My quizzes
        $data['myQuizzes'] = $this->getStudentMyQuizzesData($user, $userBoughtWebinarsIds);

        // Upcoming Live Sessions
        $data['upcomingLiveSessions'] = $this->getStudentUpcomingLiveSessionsData($user, $userBoughtWebinarsIds);

        // Open Meetings
        $data['openMeetings'] = $this->getStudentOpenMeetingsData($user, $userBoughtWebinarsIds);

        return $data;
    }

    private function getInstructorDashboardData(Request $request, $user): array
    {
        $data = [];

        $userWebinars = Webinar::query()
            ->where(function (Builder $query) use ($user) {
                $query->where('webinars.creator_id', $user->id);
                $query->orWhere('webinars.teacher_id', $user->id);
            })
            ->leftJoin('sales', function ($join) use ($user) {
                $join->on('sales.webinar_id', '=', 'webinars.id');
                $join->whereNull('sales.refund_at');
                //$join->where('sales.amount', '>', '0');
            })
            ->select('webinars.*',
                DB::raw('count(sales.webinar_id) as sales_count'),
                DB::raw('sum(sales.total_amount) as sales_amount')
            )
            ->groupBy('webinars.id')
            ->orderBy('sales_count', 'desc')
            ->get();

        $userWebinarsIds = $userWebinars->pluck('id')->toArray();

        $meetingIds = Meeting::where('creator_id', $user->id)->pluck('id');


        // hello_box
        $data['helloBox'] = $this->getInstructorHelloBoxData($user, $meetingIds, $userWebinars);

        // Courses Overview
        $data['coursesOverview'] = $this->getInstructorCoursesOverviewData($user, $userWebinars);

        // Sales Overview
        $data['salesOverview'] = $this->getInstructorSalesOverviewData($user, $userWebinarsIds);

        // Pending Student Assignments
        $data['pendingStudentAssignments'] = $this->getInstructorStudentAssignmentsData($user, $userWebinarsIds);

        // Registration Plan
        $userPackage = new UserPackage($user);
        $data['registrationPlan'] = $userPackage->getPackage();

        // Current Balance
        $data['authUserBalanceCharge'] = $user->getAccountingCharge();
        $data['authUserReadyPayout'] = $user->getPayout();

        // Noticeboard
        $data['unreadNoticeboards'] = $user->getUnreadNoticeboards();

        // Support Messages
        $data['supportMessages'] = $this->getInstructorSupportMessagesData($user, $userWebinarsIds);

        // Visitors Statistics
        $data['visitorsStatistics'] = $this->getInstructorVisitorsStatisticsData($user, $userWebinarsIds);

        if ($user->isTeacher()) {
            // Upcoming Live Sessions
            $data['upcomingLiveSessions'] = $this->getInstructorUpcomingLiveSessionsData($user, $userWebinarsIds);

            // Review Student Quizzes
            $data['reviewStudentQuizzes'] = $this->getInstructorReviewStudentQuizzes($user, $userWebinarsIds);

            // Open Meetings
            $data['openMeetings'] = $this->getInstructorOpenMeetingsData($user, $userWebinarsIds);

        } else { // Organization
            // Top Instructors
            $data['topInstructors'] = $this->getOrganizationTopInstructorsData($user);

            // Top Students
            $data['topStudents'] = $this->getOrganizationTopStudentsData($user);
        }


        return $data;
    }

    private function handleDashboardUpcomingEvents($user)
    {
        $eventsController = (new EventsCalendarController());
        $eventsController->user = $user;
        $eventsController->userBoughtWebinarsIds = $user->getPurchasedCoursesIds();

        $eventsWithTimestamp = $eventsController->getAllEventsReturnWithTimestamp();
        $getUpcomingEvents = $eventsController->getUpcomingEvents(2);
        $upcomingEvents = $getUpcomingEvents['upcomingEvents'];
        $totalEvents = $getUpcomingEvents['total'];

        return [
            'upcomingEvents' => $upcomingEvents,
            'totalEvents' => $totalEvents,
            'eventsWithTimestamp' => $eventsWithTimestamp,
        ];
    }

    private function showGiftModal($user)
    {
        $gift = Gift::query()->where('email', $user->email)
            ->where('status', 'active')
            ->where('viewed', false)
            ->where(function ($query) {
                $query->whereNull('date');
                $query->orWhere('date', '<', time());
            })
            ->whereHas('sale')
            ->first();

        if (!empty($gift)) {
            $gift->update([
                'viewed' => true
            ]);

            $data = [
                'gift' => $gift
            ];

            $result = (string)view()->make('design_1.web.gift.modal.show_to_receipt', $data);
            $result = str_replace(array("\r\n", "\n", "  "), '', $result);

            return $result;
        }

        return null;
    }

}
