<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'image' => 'https://via.placeholder.com/800x400?text=Banner+Khuyen+Mai+1',
                'link' => '/dang-ky-goi',
                'is_active' => true,
                'thu_tu' => 1,
            ],
            [
                'image' => 'https://via.placeholder.com/800x400?text=Banner+Huong+Dan+Phu+Huynh',
                'link' => '/blog/huong-dan',
                'is_active' => true,
                'thu_tu' => 2,
            ],
        ];

        foreach ($items as $item) {
            Banner::updateOrCreate(
                ['image' => $item['image']],
                $item
            );
        }
    }
}
