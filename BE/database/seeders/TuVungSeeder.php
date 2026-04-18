<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TuVungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $baiHocLN = DB::table('bai_hocs')->where('tieu_de', 'Phát âm phụ âm L và N')->first();
        $baiHocHocTap = DB::table('bai_hocs')->where('tieu_de', 'Từ vựng: Đồ dùng học tập')->first();
        $baiHocPhongKhach = DB::table('bai_hocs')->where('tieu_de', 'Từ vựng: Phòng khách')->first();
        $baiHocPhongBep = DB::table('bai_hocs')->where('tieu_de', 'Từ vựng: Phòng bếp')->first();

        $data = [
            ['tu' => 'Quả na', 'pa' => 'Quả na', 'cd' => 'de', 'bh' => $baiHocLN->id ?? null, 'img' => 'https://images.unsplash.com/photo-1619566636858-adf3ef46400b?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Cái lá', 'pa' => 'Cái lá', 'cd' => 'de', 'bh' => $baiHocLN->id ?? null, 'img' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Lúa ngô', 'pa' => 'Lúa ngô', 'cd' => 'trung_binh', 'bh' => $baiHocLN->id ?? null, 'img' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Con lợn', 'pa' => 'Con lợn', 'cd' => 'de', 'bh' => $baiHocLN->id ?? null, 'img' => 'https://images.unsplash.com/photo-1516467508483-a7212febe31a?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Nụ cười', 'pa' => 'Nụ cười', 'cd' => 'trung_binh', 'bh' => $baiHocLN->id ?? null, 'img' => 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=600&q=80'],

            ['tu' => 'Bút chì', 'pa' => 'Bút chì', 'cd' => 'de', 'bh' => $baiHocHocTap->id ?? null, 'img' => 'https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Thước kẻ', 'pa' => 'Thước kẻ', 'cd' => 'de', 'bh' => $baiHocHocTap->id ?? null, 'img' => 'https://images.unsplash.com/photo-1632571401005-458e9d244591?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Cặp sách', 'pa' => 'Cặp sách', 'cd' => 'trung_binh', 'bh' => $baiHocHocTap->id ?? null, 'img' => 'https://images.unsplash.com/photo-1622560463248-7f71329aa2be?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Quyển vở', 'pa' => 'Quyển vở', 'cd' => 'trung_binh', 'bh' => $baiHocHocTap->id ?? null, 'img' => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Cục tẩy', 'pa' => 'Cục tẩy', 'cd' => 'de', 'bh' => $baiHocHocTap->id ?? null, 'img' => 'https://images.unsplash.com/photo-1513542789411-b6a5d4f31634?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Hộp màu', 'pa' => 'Hộp màu', 'cd' => 'de', 'bh' => $baiHocHocTap->id ?? null, 'img' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=600&q=80'],

            ['tu' => 'Bàn ghế', 'pa' => 'Bàn ghế', 'cd' => 'de', 'bh' => $baiHocPhongKhach->id ?? null, 'img' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Tivi', 'pa' => 'Ti vi', 'cd' => 'de', 'bh' => $baiHocPhongKhach->id ?? null, 'img' => 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Quạt trần', 'pa' => 'Quạt trần', 'cd' => 'trung_binh', 'bh' => $baiHocPhongKhach->id ?? null, 'img' => 'https://images.unsplash.com/photo-1585338447937-7082f8fc763d?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Bình hoa', 'pa' => 'Bình hoa', 'cd' => 'de', 'bh' => $baiHocPhongKhach->id ?? null, 'img' => 'https://images.unsplash.com/photo-1490750967868-88aa4486c946?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Đồng hồ', 'pa' => 'Đồng hồ', 'cd' => 'de', 'bh' => $baiHocPhongKhach->id ?? null, 'img' => 'https://images.unsplash.com/photo-1563861826100-9cb868fdbe1c?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Thảm cỏ', 'pa' => 'Thảm cỏ', 'cd' => 'trung_binh', 'bh' => $baiHocPhongKhach->id ?? null, 'img' => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=600&q=80'],

            ['tu' => 'Cái bát', 'pa' => 'Cái bát', 'cd' => 'de', 'bh' => $baiHocPhongBep->id ?? null, 'img' => 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Đôi đũa', 'pa' => 'Đôi đũa', 'cd' => 'trung_binh', 'bh' => $baiHocPhongBep->id ?? null, 'img' => 'https://images.unsplash.com/photo-1615937657715-bc7b4b7962c1?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Tủ lạnh', 'pa' => 'Tủ lạnh', 'cd' => 'trung_binh', 'bh' => $baiHocPhongBep->id ?? null, 'img' => 'https://images.unsplash.com/photo-1571175443880-49e1d25b2bc5?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Cái thìa', 'pa' => 'Cái thìa', 'cd' => 'de', 'bh' => $baiHocPhongBep->id ?? null, 'img' => 'https://images.unsplash.com/photo-1584990347449-a8b2917adc67?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Nồi cơm', 'pa' => 'Nồi cơm', 'cd' => 'de', 'bh' => $baiHocPhongBep->id ?? null, 'img' => 'https://images.unsplash.com/photo-1585659722983-3a675dabf23d?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Bếp gas', 'pa' => 'Bếp ga', 'cd' => 'trung_binh', 'bh' => $baiHocPhongBep->id ?? null, 'img' => 'https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=600&q=80'],
            ['tu' => 'Ấm nước', 'pa' => 'Ấm nước', 'cd' => 'de', 'bh' => $baiHocPhongBep->id ?? null, 'img' => 'https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?auto=format&fit=crop&w=600&q=80'],
        ];

        foreach ($data as $index => $item) {
            if ($item['bh']) {
                DB::table('tu_vungs')->updateOrInsert(
                    ['tu_chuan' => $item['tu']],
                    [
                        'bai_hoc_id' => $item['bh'],
                        'phien_am' => $item['pa'],
                        'cap_do' => $item['cd'],
                        'hinh_anh_url' => $item['img'],
                        'am_thanh_mau_url' => null,
                        'thu_tu' => $index + 1,
                        'ngay_tao' => now(),
                    ]
                );
            }
        }
    }
}
