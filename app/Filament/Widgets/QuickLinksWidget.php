<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\AccessLogsPage;
use App\Filament\Pages\AttendanceReport;
use App\Filament\Pages\FinancialReport;
use App\Filament\Pages\ScanAttendance;
use App\Filament\Pages\StudentReport;
use App\Filament\Resources\Academic\AcademicYearResource;
use App\Filament\Resources\Academic\CbtExams\CbtExamResource;
use App\Filament\Resources\Academic\ClassRoomResource;
use App\Filament\Resources\Academic\ClassSchedules\ClassScheduleResource;
use App\Filament\Resources\Academic\SemesterResource;
use App\Filament\Resources\Academic\StudyMaterialResource;
use App\Filament\Resources\Academic\SubjectResource;
use App\Filament\Resources\Evaluation\CbtAttemptResource;
use App\Filament\Resources\Evaluation\TestResource;
use App\Filament\Resources\Evaluation\TestScheduleRequestResource;
use App\Filament\Resources\Evaluation\TestSessionResource;
use App\Filament\Resources\Finance\Expenses\ExpenseResource;
use App\Filament\Resources\Finance\HonorResource;
use App\Filament\Resources\Finance\StudentPayments\StudentPaymentResource;
use App\Filament\Resources\Finance\TutorSalaryResource;
use App\Filament\Resources\HR\AttendanceResource;
use App\Filament\Resources\HR\Evaluations\EvaluationResource;
use App\Filament\Resources\HR\ExternalStudentResource;
use App\Filament\Resources\HR\SchoolResource;
use App\Filament\Resources\HR\StudentResource;
use App\Filament\Resources\HR\TutorResource;
use App\Filament\Resources\HR\UserResource;
use App\Filament\Resources\Operations\Admissions\AdmissionResource;
use App\Filament\Resources\Operations\JournalResource;
use App\Filament\Resources\Operations\JournalValidationResource;
use App\Filament\Resources\Operations\Schedules\ScheduleResource;
use App\Filament\Resources\Settings\SettingResource;
use App\Models\Admission;
use App\Models\Journal;
use App\Models\Student;
use App\Models\TestScheduleRequest;
use App\Models\User;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;

class QuickLinksWidget extends Widget
{
    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.quick-links-widget';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 3;

    public function getShortcutGroupsProperty(): array
    {
        $isAdmin = auth()->user()?->isAdmin() ?? false;

        $internalCount = Student::query()
            ->where(fn (Builder $q) => $q->where('student_type', 'internal')->orWhereNull('student_type'))
            ->count();
        $externalCount = Student::where('student_type', 'external')->count();
        $schoolCount = User::where('role', 'school')->count();
        $pendingAdmissions = Admission::where('status', 'pending')->count();
        $pendingJournals = Journal::where('status', 'submitted')->count();
        $pendingScheduleReq = TestScheduleRequest::where('status', 'pending')->count();

        $groups = [];

        if ($isAdmin) {
            $groups[] = [
                'key' => 'students',
                'label' => __('Student Management'),
                'items' => [
                    [
                        'label' => __('Internal Students'),
                        'url' => StudentResource::getUrl('index'),
                        'icon' => 'heroicon-s-user-group',
                        'tone' => 'emerald',
                        'badge' => $internalCount > 0 ? (string) $internalCount : null,
                        'alert' => false,
                    ],
                    [
                        'label' => __('External Students'),
                        'url' => ExternalStudentResource::getUrl('index'),
                        'icon' => 'heroicon-s-trophy',
                        'tone' => 'amber',
                        'badge' => $externalCount > 0 ? (string) $externalCount : null,
                        'alert' => false,
                    ],
                    [
                        'label' => __('Manage Schools'),
                        'url' => SchoolResource::getUrl('index'),
                        'icon' => 'heroicon-s-building-office-2',
                        'tone' => 'blue',
                        'badge' => $schoolCount > 0 ? (string) $schoolCount : null,
                        'alert' => false,
                    ],
                    [
                        'label' => __('Admissions'),
                        'url' => AdmissionResource::getUrl('index'),
                        'icon' => 'heroicon-s-user-plus',
                        'tone' => 'teal',
                        'badge' => $pendingAdmissions > 0 ? (string) $pendingAdmissions : null,
                        'alert' => $pendingAdmissions > 0,
                    ],
                ],
            ];
        }

        $academicItems = [
            [
                'label' => __('Daily Schedules'),
                'url' => ScheduleResource::getUrl('index'),
                'icon' => 'heroicon-s-clock',
                'tone' => 'blue',
                'badge' => null,
                'alert' => false,
            ],
            [
                'label' => __('Scan Attendance'),
                'url' => ScanAttendance::getUrl(),
                'icon' => 'heroicon-s-qr-code',
                'tone' => 'rose',
                'badge' => null,
                'alert' => false,
            ],
            [
                'label' => __('Attendances'),
                'url' => AttendanceResource::getUrl('index'),
                'icon' => 'heroicon-s-clipboard-document-check',
                'tone' => 'sky',
                'badge' => null,
                'alert' => false,
            ],
            [
                'label' => __('Learning Journal'),
                'url' => JournalResource::getUrl('index'),
                'icon' => 'heroicon-s-document-text',
                'tone' => 'indigo',
                'badge' => null,
                'alert' => false,
            ],
        ];

        if ($isAdmin) {
            array_splice($academicItems, 3, 0, [[
                'label' => __('Study Materials'),
                'url' => StudyMaterialResource::getUrl('index'),
                'icon' => 'heroicon-s-book-open',
                'tone' => 'teal',
                'badge' => null,
                'alert' => false,
            ]]);

            $academicItems[] = [
                'label' => __('Journal Validations'),
                'url' => JournalValidationResource::getUrl('index'),
                'icon' => 'heroicon-s-check-badge',
                'tone' => 'orange',
                'badge' => $pendingJournals > 0 ? (string) $pendingJournals : null,
                'alert' => $pendingJournals > 0,
            ];
        }

        $groups[] = [
            'key' => 'academic',
            'label' => __('Academic'),
            'items' => $academicItems,
        ];

        $evalItems = [
            [
                'label' => __('Manage Tests'),
                'url' => TestResource::getUrl('index'),
                'icon' => 'heroicon-s-clipboard-document-list',
                'tone' => 'blue',
                'badge' => null,
                'alert' => false,
            ],
            [
                'label' => __('CBT Exams'),
                'url' => CbtExamResource::getUrl('index'),
                'icon' => 'heroicon-s-pencil-square',
                'tone' => 'indigo',
                'badge' => null,
                'alert' => false,
            ],
            [
                'label' => __('Test Sessions & Server'),
                'url' => TestSessionResource::getUrl('index'),
                'icon' => 'heroicon-s-server-stack',
                'tone' => 'teal',
                'badge' => null,
                'alert' => false,
            ],
            [
                'label' => __('Test Schedule Requests'),
                'url' => TestScheduleRequestResource::getUrl('index'),
                'icon' => 'heroicon-s-calendar-date-range',
                'tone' => 'amber',
                'badge' => $pendingScheduleReq > 0 ? (string) $pendingScheduleReq : null,
                'alert' => $pendingScheduleReq > 0,
            ],
            [
                'label' => __('Exam Monitoring'),
                'url' => CbtAttemptResource::getUrl('index'),
                'icon' => 'heroicon-s-computer-desktop',
                'tone' => 'rose',
                'badge' => null,
                'alert' => false,
            ],
            [
                'label' => __('Evaluations'),
                'url' => EvaluationResource::getUrl('index'),
                'icon' => 'heroicon-s-chart-bar-square',
                'tone' => 'violet',
                'badge' => null,
                'alert' => false,
            ],
        ];

        if ($isAdmin) {
            $evalItems[] = [
                'label' => __('Honors'),
                'url' => HonorResource::getUrl('index'),
                'icon' => 'heroicon-s-sparkles',
                'tone' => 'orange',
                'badge' => null,
                'alert' => false,
            ];
        }

        $groups[] = [
            'key' => 'evaluation',
            'label' => __('Exams & Evaluation'),
            'items' => $evalItems,
        ];

        if ($isAdmin) {
            $groups[] = [
                'key' => 'finance',
                'label' => __('Finance & Reports'),
                'items' => [
                    [
                        'label' => __('Student Payments'),
                        'url' => StudentPaymentResource::getUrl('index'),
                        'icon' => 'heroicon-s-credit-card',
                        'tone' => 'emerald',
                        'badge' => null,
                        'alert' => false,
                    ],
                    [
                        'label' => __('Tutor Salaries'),
                        'url' => TutorSalaryResource::getUrl('index'),
                        'icon' => 'heroicon-s-banknotes',
                        'tone' => 'violet',
                        'badge' => null,
                        'alert' => false,
                    ],
                    [
                        'label' => __('Expenses'),
                        'url' => ExpenseResource::getUrl('index'),
                        'icon' => 'heroicon-s-receipt-percent',
                        'tone' => 'rose',
                        'badge' => null,
                        'alert' => false,
                    ],
                    [
                        'label' => __('Attendance Report'),
                        'url' => AttendanceReport::getUrl(),
                        'icon' => 'heroicon-s-document-chart-bar',
                        'tone' => 'teal',
                        'badge' => null,
                        'alert' => false,
                    ],
                    [
                        'label' => __('Report Card'),
                        'url' => StudentReport::getUrl(),
                        'icon' => 'heroicon-s-academic-cap',
                        'tone' => 'indigo',
                        'badge' => null,
                        'alert' => false,
                    ],
                    [
                        'label' => __('Financial Report'),
                        'url' => FinancialReport::getUrl(),
                        'icon' => 'heroicon-s-presentation-chart-line',
                        'tone' => 'amber',
                        'badge' => null,
                        'alert' => false,
                    ],
                ],
            ];

            $groups[] = [
                'key' => 'master',
                'label' => __('Master Data & System'),
                'items' => [
                    [
                        'label' => __('Tutor Management'),
                        'url' => TutorResource::getUrl('index'),
                        'icon' => 'heroicon-s-academic-cap',
                        'tone' => 'violet',
                        'badge' => null,
                        'alert' => false,
                    ],
                    [
                        'label' => __('Manage Employees'),
                        'url' => UserResource::getUrl('index'),
                        'icon' => 'heroicon-s-users',
                        'tone' => 'indigo',
                        'badge' => null,
                        'alert' => false,
                    ],
                    [
                        'label' => __('Class Rooms'),
                        'url' => ClassRoomResource::getUrl('index'),
                        'icon' => 'heroicon-s-building-library',
                        'tone' => 'blue',
                        'badge' => null,
                        'alert' => false,
                    ],
                    [
                        'label' => __('Master Schedules'),
                        'url' => ClassScheduleResource::getUrl('index'),
                        'icon' => 'heroicon-s-calendar-days',
                        'tone' => 'amber',
                        'badge' => null,
                        'alert' => false,
                    ],
                    [
                        'label' => __('Subjects'),
                        'url' => SubjectResource::getUrl('index'),
                        'icon' => 'heroicon-s-bookmark',
                        'tone' => 'teal',
                        'badge' => null,
                        'alert' => false,
                    ],
                    [
                        'label' => __('Academic Years'),
                        'url' => AcademicYearResource::getUrl('index'),
                        'icon' => 'heroicon-s-calendar',
                        'tone' => 'orange',
                        'badge' => null,
                        'alert' => false,
                    ],
                    [
                        'label' => __('Semesters'),
                        'url' => SemesterResource::getUrl('index'),
                        'icon' => 'heroicon-s-squares-2x2',
                        'tone' => 'sky',
                        'badge' => null,
                        'alert' => false,
                    ],
                    [
                        'label' => __('Settings'),
                        'url' => SettingResource::getUrl('index'),
                        'icon' => 'heroicon-s-cog-6-tooth',
                        'tone' => 'slate',
                        'badge' => null,
                        'alert' => false,
                    ],
                    [
                        'label' => __('Roles & Permissions'),
                        'url' => RoleResource::getUrl('index'),
                        'icon' => 'heroicon-s-shield-check',
                        'tone' => 'emerald',
                        'badge' => null,
                        'alert' => false,
                    ],
                    [
                        'label' => __('User Access Logs'),
                        'url' => AccessLogsPage::getUrl(),
                        'icon' => 'heroicon-s-finger-print',
                        'tone' => 'blue',
                        'badge' => null,
                        'alert' => false,
                    ],
                ],
            ];
        }

        return $groups;
    }
}
