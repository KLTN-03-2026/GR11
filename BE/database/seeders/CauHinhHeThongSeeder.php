<?php

namespace Database\Seeders;

use App\Models\CauHinhHeThong;
use Illuminate\Database\Seeder;

class CauHinhHeThongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            'general' => [
                'logo_url' => null,
                'logo_icon' => 'fa fa-book-reader me-3',
                'site_name' => 'EchoKids',
                'hotline' => '1900 1234',
                'support_email' => 'hotro@echokids.vn',
                'facebook_url' => 'https://facebook.com/echokids',
            ],
            'ai' => [
                'speech_to_text' => [
                    'is_active' => true,
                    'api_key' => 'AIzaSyA_XXXXXXXXXXXXXXXXXXXXX_V8',
                    'monthly_limit' => 50000,
                    'current_usage' => 42150,
                ],
            ],
            'alert' => [
                'is_active' => false,
                'message' => 'Hệ thống đang tiến hành nâng cấp AI. Việc chấm điểm phát âm có thể chậm hơn bình thường 1-2 giây.',
            ],
        ];

        foreach ($items as $maCauHinh => $duLieu) {
            CauHinhHeThong::updateOrCreate(
                ['ma_cau_hinh' => $maCauHinh],
                ['du_lieu' => $duLieu]
            );
        }
    }
}
