<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyBannerRequest;
use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateAiSettingsRequest;
use App\Http\Requests\UpdateAlertSettingsRequest;
use App\Http\Requests\UpdateBannerRequest;
use App\Http\Requests\UpdateGeneralSettingsRequest;
use App\Models\Banner;
use App\Models\CauHinhHeThong;
use Illuminate\Http\JsonResponse;

class CauHinhController extends Controller
{
    public function getFooterSettings(): JsonResponse
    {
        $general = $this->getSettingValue('general', $this->defaultSettings()['general']);
        $alert = $this->getSettingValue('alert', $this->defaultSettings()['alert']);

        return response()->json([
            'status' => true,
            'data' => [
                'site_name' => $general['site_name'] ?? 'EchoKids',
                'logo_url' => $general['logo_url'] ?? null,
                'logo_icon' => $general['logo_icon'] ?? 'fa fa-book-reader me-3',
                'hotline' => $general['hotline'] ?? '',
                'support_email' => $general['support_email'] ?? '',
                'facebook_url' => $general['facebook_url'] ?? '',
                'alert' => $alert,
            ],
        ]);
    }

    public function getGeneralSettings(): JsonResponse
    {
        $settings = $this->getSettingValue('general', $this->defaultSettings()['general']);
        return response()->json([
            'status' => true,
            'data' => $settings,
        ]);
    }

    public function updateGeneralSettings(UpdateGeneralSettingsRequest $request): JsonResponse
    {
        $settings = [
            'logo_url' => $request->input('logo_url'),
            'logo_icon' => $request->input('logo_icon'),
            'site_name' => $request->input('site_name'),
            'hotline' => $request->input('hotline'),
            'support_email' => $request->input('support_email'),
            'facebook_url' => $request->input('facebook_url'),
        ];
        $this->setSettingValue('general', $settings);

        return response()->json([
            'status' => true,
            'message' => 'Đã cập nhật cấu hình chung.',
            'data' => $settings,
        ]);
    }

    public function getAiSettings(): JsonResponse
    {
        $settings = $this->getSettingValue('ai', $this->defaultSettings()['ai']);
        return response()->json([
            'status' => true,
            'data' => $settings,
        ]);
    }

    public function updateAiSettings(UpdateAiSettingsRequest $request): JsonResponse
    {
        $settings = [
            'speech_to_text' => [
                'is_active' => (bool) $request->input('speech_to_text.is_active'),
                'api_key' => $request->input('speech_to_text.api_key'),
                'monthly_limit' => (int) $request->input('speech_to_text.monthly_limit'),
                'current_usage' => (int) $request->input('speech_to_text.current_usage', 0),
            ],
        ];
        $this->setSettingValue('ai', $settings);

        return response()->json([
            'status' => true,
            'message' => 'Đã cập nhật cấu hình AI/API.',
            'data' => $settings,
        ]);
    }

    public function getAlertSettings(): JsonResponse
    {
        $settings = $this->getSettingValue('alert', $this->defaultSettings()['alert']);
        return response()->json([
            'status' => true,
            'data' => $settings,
        ]);
    }

    public function updateAlertSettings(UpdateAlertSettingsRequest $request): JsonResponse
    {
        $settings = [
            'is_active' => (bool) $request->input('is_active'),
            'message' => $request->input('message'),
        ];
        $this->setSettingValue('alert', $settings);

        return response()->json([
            'status' => true,
            'message' => 'Đã cập nhật thông báo hệ thống.',
            'data' => $settings,
        ]);
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => Banner::query()->orderBy('thu_tu')->orderBy('id')->get(),
        ]);
    }

    public function store(StoreBannerRequest $request): JsonResponse
    {
        $banner = Banner::create([
            'image' => $request->input('image'),
            'link' => $request->input('link'),
            'is_active' => (bool) $request->input('is_active'),
            'thu_tu' => 0,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Đã thêm banner thành công.',
            'data' => $banner,
        ]);
    }

    public function update(UpdateBannerRequest $request, int $id): JsonResponse
    {
        $banner = Banner::find($id);
        if (!$banner) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy banner.',
            ], 404);
        }

        $payload = [
            'image' => $request->input('image', $banner->image),
            'link' => $request->input('link', $banner->link),
        ];
        if ($request->has('is_active')) {
            $payload['is_active'] = (bool) $request->input('is_active');
        }
        $banner->update($payload);

        return response()->json([
            'status' => true,
            'message' => 'Đã cập nhật banner thành công.',
            'data' => $banner->fresh(),
        ]);
    }

    public function destroy(DestroyBannerRequest $request, int $id): JsonResponse
    {
        $banner = Banner::find($id);
        if (!$banner) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy banner.',
            ], 404);
        }
        $banner->delete();

        return response()->json([
            'status' => true,
            'message' => 'Đã xóa banner thành công.',
        ]);
    }

    private function getSettingValue(string $maCauHinh, array $default): array
    {
        $record = CauHinhHeThong::where('ma_cau_hinh', $maCauHinh)->first();
        if (!$record || !is_array($record->du_lieu)) {
            return $default;
        }
        return array_merge($default, $record->du_lieu);
    }

    private function setSettingValue(string $maCauHinh, array $duLieu): void
    {
        CauHinhHeThong::updateOrCreate(
            ['ma_cau_hinh' => $maCauHinh],
            ['du_lieu' => $duLieu]
        );
    }

    private function defaultSettings(): array
    {
        return [
            'general' => [
                'logo_url' => null,
                'logo_icon' => 'fa fa-book-reader me-3',
                'site_name' => 'Hệ thống Trị liệu Ngôn ngữ EchoKids',
                'hotline' => null,
                'support_email' => null,
                'facebook_url' => null,
            ],
            'ai' => [
                'speech_to_text' => [
                    'is_active' => false,
                    'api_key' => null,
                    'monthly_limit' => 0,
                    'current_usage' => 0,
                ],
            ],
            'alert' => [
                'is_active' => false,
                'message' => null,
            ],
            'banners' => [],
        ];
    }
}
