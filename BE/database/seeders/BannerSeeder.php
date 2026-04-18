<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Banner trang chủ — hình minh họa thân thiện, link nội bộ hợp lệ.
     */
    public function run(): void
{
    $items = [
        [
            'image' => 'https://images.unsplash.com/photo-1546410531-bb4caa1b424d?auto=format&fit=crop&w=1200&q=80',
            'link' => '/bai-hoc',
            'tieu_de' => 'Học phát âm cơ bản', 
            'is_active' => true,
            'thu_tu' => 1,
        ],
        [
            'image' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=1200&q=80', 
            'link' => '/luyen-tap',
            'tieu_de' => 'Thực hành tương tác',
            'is_active' => true,
            'thu_tu' => 2,
        ],
        [
            'image' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=1200&q=80', 
            'link' => '/on-tap-loi',
            'tieu_de' => 'Khắc phục lỗi phát âm',
            'is_active' => true,
            'thu_tu' => 3,
        ],
    ];

    foreach ($items as $item) {

        Banner::updateOrCreate(
            ['thu_tu' => $item['thu_tu']],
            [
                'image' => $item['image'],
                'link' => $item['link'],
                'is_active' => $item['is_active'],
                // 'tieu_de' => $item['tieu_de'], 
            ]
        );
    }
}
}
