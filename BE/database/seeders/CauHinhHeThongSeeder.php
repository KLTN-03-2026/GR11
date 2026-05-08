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
                'support_email' => 'supportechokids@gmail.com',
                'facebook_url' => 'https://facebook.com/echokids',
            ],
            'ai' => [
                'speech_to_text' => [
                    'is_active' => true,
                    'api_key' => '[GCP_API_KEY]',
                    'monthly_limit' => 500000,
                    'current_usage' => 0,
                ],
            ],
            'alert' => [
                'is_active' => false,
                'message' => 'Hệ thống đang tiến hành nâng cấp AI. Việc chấm điểm phát âm có thể chậm hơn bình thường 1-2 giây.',
            ],
            'ti_le_hoa_hong_platform' => [
                'phan_tram' => 20,
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
