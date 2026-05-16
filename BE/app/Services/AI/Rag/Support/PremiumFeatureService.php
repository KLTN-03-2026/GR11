<?php

namespace App\Services\AI\Rag\Support;

use App\GoiPremium\GoiPremiumDoiTuong;
use App\Models\GoiNguoiDung;
use App\Models\GoiPremium;
use App\Models\NguoiDung;
use App\Models\Vi;

class PremiumFeatureService
{
    public const FEATURE_WEEKLY_REPORT = 'bao_cao_tuan';

    public const FEATURE_MONTHLY_REPORT = 'bao_cao_thang';

    public const FEATURE_PRONUNCIATION_CHART = 'bieu_do_phat_am';

    public const FEATURE_CLASS_REPORT = 'bao_cao_lop';

    public function hasActivePremium(NguoiDung $user): bool
    {
        return GoiNguoiDung::query()
            ->where('nguoi_dung_id', $user->id)
            ->where('trang_thai', GoiNguoiDung::TRANG_THAI_HOAT_DONG)
            ->where('ngay_het_han', '>', now())
            ->exists();
    }

    public function can(NguoiDung $user, string $feature): bool
    {
        $subscription = GoiNguoiDung::query()
            ->where('nguoi_dung_id', $user->id)
            ->where('trang_thai', GoiNguoiDung::TRANG_THAI_HOAT_DONG)
            ->where('ngay_het_han', '>', now())
            ->with('goiPremium')
            ->orderByDesc('ngay_het_han')
            ->first();

        if ($subscription?->goiPremium === null) {
            return false;
        }

        $features = (array) ($subscription->goiPremium->tinh_nang ?? []);

        return ($features[$feature] ?? false) === true;
    }

    /**
     * @return array{allowed:bool, upsell_message:?string, action_url:?string}
     */
    public function gate(NguoiDung $user, string $feature): array
    {
        if ($this->can($user, $feature)) {
            return ['allowed' => true, 'upsell_message' => null, 'action_url' => null];
        }

        $isTeacher = (int) $user->vai_tro_id === NguoiDung::ROLE_TEACHER;
        $actionUrl = $isTeacher ? '/teacher/profile' : '/profile';

        return [
            'allowed' => false,
            'upsell_message' => $isTeacher
                ? 'Tính năng này nằm trong gói Premium Giáo viên. Thầy cô có thể nâng cấp tại trang hồ sơ.'
                : 'Tính năng này nằm trong gói Premium. Con nhờ ba mẹ nâng cấp tại trang hồ sơ nhé.',
            'action_url' => $actionUrl,
        ];
    }

    public function defaultFeatureForRole(NguoiDung $user): string
    {
        return (int) $user->vai_tro_id === NguoiDung::ROLE_TEACHER
            ? self::FEATURE_CLASS_REPORT
            : self::FEATURE_MONTHLY_REPORT;
    }

    public function catalogForUser(NguoiDung $user): ?GoiPremium
    {
        $doiTuong = GoiPremiumDoiTuong::tryFromVaiTroId((int) $user->vai_tro_id);
        if ($doiTuong === null) {
            return null;
        }

        return GoiPremium::query()
            ->where('doi_tuong', $doiTuong->value)
            ->where('trang_thai', 1)
            ->first();
    }

    /**
     * @return array<string, mixed>
     */
    public function purchaseGuide(NguoiDung $user): array
    {
        $isTeacher = (int) $user->vai_tro_id === NguoiDung::ROLE_TEACHER;
        $goi = $this->catalogForUser($user);
        $hasActive = $this->hasActivePremium($user);

        $activeRow = GoiNguoiDung::query()
            ->where('nguoi_dung_id', $user->id)
            ->where('trang_thai', GoiNguoiDung::TRANG_THAI_HOAT_DONG)
            ->where('ngay_het_han', '>', now())
            ->with('goiPremium')
            ->orderByDesc('ngay_het_han')
            ->first();

        $balance = (int) (Vi::query()->where('nguoi_dung_id', $user->id)->value('so_du') ?? 0);
        $price = $goi !== null ? (int) $goi->gia : 0;

        return [
            'role' => $isTeacher ? 'teacher' : 'student',
            'has_active_premium' => $hasActive,
            'can_purchase' => $goi !== null && ! $hasActive,
            'wallet_balance' => $balance,
            'sufficient_balance' => $goi !== null && $balance >= $price,
            'profile_path' => $isTeacher ? '/teacher/thong-tin-ca-nhan' : '/thong-tin-ca-nhan',
            'package' => $goi === null ? null : [
                'ten_goi' => (string) $goi->ten_goi,
                'mo_ta' => (string) ($goi->mo_ta ?? ''),
                'gia' => $price,
                'thoi_han_ngay' => (int) $goi->thoi_han_ngay,
                'tinh_nang' => $this->featureLines((array) ($goi->tinh_nang ?? []), $isTeacher),
            ],
            'active_subscription' => $activeRow === null ? null : [
                'ten_goi' => (string) ($activeRow->goiPremium?->ten_goi ?? ''),
                'ngay_het_han' => $activeRow->ngay_het_han?->format('d/m/Y'),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $features
     * @return list<string>
     */
    private function featureLines(array $features, bool $isTeacher): array
    {
        if ($isTeacher) {
            $lines = [];
            if (($features['bao_cao_lop'] ?? false) === true) {
                $lines[] = 'Báo cáo lớp học (Premium)';
            }
            if (isset($features['gioi_han_hoc_vien'])) {
                $lines[] = 'Quản lý tối đa ' . (int) $features['gioi_han_hoc_vien'] . ' học viên';
            }

            return $lines;
        }

        $map = [
            'uu_tien_cham_diem' => 'Ưu tiên chấm phát âm',
            'bao_cao_tuan' => 'Báo cáo tuần',
            'bao_cao_thang' => 'Báo cáo tháng',
            'bieu_do_phat_am' => 'Biểu đồ phát âm',
        ];
        $lines = [];
        foreach ($map as $key => $label) {
            if (($features[$key] ?? false) === true) {
                $lines[] = $label;
            }
        }

        return $lines;
    }
}
