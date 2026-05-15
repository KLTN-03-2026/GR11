<?php

namespace App\Services\AI\Rag\Tools;

use App\Models\NguoiDung;
use App\Services\AI\Rag\Support\PremiumFeatureService;

class PremiumGuideTools
{
    public function __construct(
        private readonly PremiumFeatureService $premium,
    ) {}

    /**
     * @return list<array{name:string,description:string,args:array<string,string>}>
     */
    public function definitions(NguoiDung $user): array
    {
        $isTeacher = (int) $user->vai_tro_id === NguoiDung::ROLE_TEACHER;
        $name = $isTeacher ? 'teacher_get_premium_purchase_guide' : 'student_get_premium_purchase_guide';

        return [
            [
                'name' => $name,
                'description' => 'Hướng dẫn mua/nâng cấp gói Premium trên EchoKids: giá, thời hạn, số dư ví, các bước trong trang Hồ sơ.',
                'args' => [],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(NguoiDung $user, string $toolName): array
    {
        return match ($toolName) {
            'student_get_premium_purchase_guide', 'teacher_get_premium_purchase_guide' => [
                'ok' => true,
                'data' => $this->premium->purchaseGuide($user),
            ],
            default => [
                'ok' => false,
                'message' => 'Công cụ không được hỗ trợ.',
            ],
        };
    }
}
