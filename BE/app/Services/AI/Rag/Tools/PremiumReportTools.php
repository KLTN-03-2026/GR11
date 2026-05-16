<?php

namespace App\Services\AI\Rag\Tools;

use App\Models\NguoiDung;
use App\Services\AI\Rag\Reports\ReportSnapshotService;
use App\Services\AI\Rag\Support\PremiumFeatureService;

class PremiumReportTools
{
    public function __construct(
        private readonly ReportSnapshotService $reportSnapshotService,
        private readonly PremiumFeatureService $premiumFeatureService,
    ) {}

    /**
     * @return list<array{name:string,description:string,args:array<string,string>}>
     */
    public function definitions(): array
    {
        return [
            [
                'name' => 'student_get_weekly_report',
                'description' => 'Premium weekly learning report for student (7-day summary)',
                'args' => [
                    'weeks_back' => 'integer, 0=current week (default 0)',
                ],
            ],
            [
                'name' => 'student_get_monthly_report',
                'description' => 'Premium monthly learning report for student',
                'args' => [
                    'months_back' => 'integer, 0=current month (default 0)',
                ],
            ],
            [
                'name' => 'student_get_pronunciation_chart',
                'description' => 'Premium pronunciation error chart and trends for student',
                'args' => [
                    'months_back' => 'integer, 0=current month (default 0)',
                ],
            ],
            [
                'name' => 'teacher_get_class_report',
                'description' => 'Premium class report for teacher',
                'args' => [
                    'months_back' => 'integer, 0=current month (default 0)',
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $args
     * @return array<string, mixed>
     */
    public function execute(NguoiDung $user, string $toolName, array $args): array
    {
        if ($toolName === 'student_get_weekly_report') {
            $gate = $this->premiumFeatureService->gate($user, PremiumFeatureService::FEATURE_WEEKLY_REPORT);
            if (! $gate['allowed']) {
                return [
                    'ok' => false,
                    'premium_required' => true,
                    'message' => $gate['upsell_message'],
                    'action_url' => $gate['action_url'],
                ];
            }

            $snapshot = $this->reportSnapshotService->getOrCreateStudentWeekly(
                $user,
                max(0, (int) ($args['weeks_back'] ?? 0))
            );

            return ['ok' => true, 'data' => $snapshot];
        }

        if ($toolName === 'student_get_monthly_report') {
            $gate = $this->premiumFeatureService->gate($user, PremiumFeatureService::FEATURE_MONTHLY_REPORT);
            if (! $gate['allowed']) {
                return [
                    'ok' => false,
                    'premium_required' => true,
                    'message' => $gate['upsell_message'],
                    'action_url' => $gate['action_url'],
                ];
            }

            $snapshot = $this->reportSnapshotService->getOrCreateStudentMonthly(
                $user,
                max(0, (int) ($args['months_back'] ?? 0))
            );

            return ['ok' => true, 'data' => $snapshot];
        }

        if ($toolName === 'student_get_pronunciation_chart') {
            $gate = $this->premiumFeatureService->gate($user, PremiumFeatureService::FEATURE_PRONUNCIATION_CHART);
            if (! $gate['allowed']) {
                return [
                    'ok' => false,
                    'premium_required' => true,
                    'message' => $gate['upsell_message'],
                    'action_url' => $gate['action_url'],
                ];
            }

            $snapshot = $this->reportSnapshotService->getOrCreatePronunciationChart(
                $user,
                max(0, (int) ($args['months_back'] ?? 0))
            );

            return ['ok' => true, 'data' => $snapshot];
        }

        if ($toolName === 'teacher_get_class_report') {
            $gate = $this->premiumFeatureService->gate($user, PremiumFeatureService::FEATURE_CLASS_REPORT);
            if (! $gate['allowed']) {
                return [
                    'ok' => false,
                    'premium_required' => true,
                    'message' => $gate['upsell_message'],
                    'action_url' => $gate['action_url'],
                ];
            }

            $snapshot = $this->reportSnapshotService->getOrCreateTeacherMonthly(
                $user,
                max(0, (int) ($args['months_back'] ?? 0))
            );

            return ['ok' => true, 'data' => $snapshot];
        }

        return [
            'ok' => false,
            'message' => 'Công cụ không được hỗ trợ.',
        ];
    }

    /**
     * @return list<string>
     */
    public function toolNames(): array
    {
        return collect($this->definitions())->pluck('name')->all();
    }

    /**
     * @return list<string>
     */
    public static function studentPremiumToolNames(): array
    {
        return [
            'student_get_weekly_report',
            'student_get_monthly_report',
            'student_get_pronunciation_chart',
        ];
    }
}
