<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TuVungSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ---------- helpers ----------
        $img = fn(string $slug): string => "/storage/images/tu_vung/{$slug}.webp";
        $snd = fn(string $slug): string => "/storage/audio/tu_vung/{$slug}.mp3";

        $tuVungs = [

            // =====================================================================
            // BÀI 1 — id=1 | Danh mục: Âm cơ bản | Nguyên âm đơn: a, ă, â
            // Mục tiêu: trẻ nhận biết và phát âm đúng 3 nguyên âm a / ă / â
            // =====================================================================
            ['bai_hoc_id' => 1, 'tu_chuan' => 'a',    'phien_am' => 'aː',   'cap_do' => 'basic', 'hinh_anh_url' => $img('a'),    'am_thanh_mau_url' => $snd('a'),    'thu_tu' => 1,  'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'ă',    'phien_am' => 'a',    'cap_do' => 'basic', 'hinh_anh_url' => $img('a-ngan'), 'am_thanh_mau_url' => $snd('a-ngan'), 'thu_tu' => 2, 'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'â',    'phien_am' => 'ɤ̞',   'cap_do' => 'basic', 'hinh_anh_url' => $img('a-mu'),  'am_thanh_mau_url' => $snd('a-mu'),  'thu_tu' => 3,  'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'ba',   'phien_am' => 'ɓaː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('ba'),   'am_thanh_mau_url' => $snd('ba'),   'thu_tu' => 4,  'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'bà',   'phien_am' => 'ɓàː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('ba-huyen'), 'am_thanh_mau_url' => $snd('ba-huyen'), 'thu_tu' => 5, 'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'má',   'phien_am' => 'maː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('ma'),   'am_thanh_mau_url' => $snd('ma'),   'thu_tu' => 6,  'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'mắt',  'phien_am' => 'mɐt',  'cap_do' => 'basic', 'hinh_anh_url' => $img('mat'),  'am_thanh_mau_url' => $snd('mat'),  'thu_tu' => 7,  'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'ăn',   'phien_am' => 'an',   'cap_do' => 'basic', 'hinh_anh_url' => $img('an'),   'am_thanh_mau_url' => $snd('an'),   'thu_tu' => 8,  'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'nắm',  'phien_am' => 'nam',  'cap_do' => 'basic', 'hinh_anh_url' => $img('nam'),  'am_thanh_mau_url' => $snd('nam'),  'thu_tu' => 9,  'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'ấm',   'phien_am' => 'ɤm',   'cap_do' => 'basic', 'hinh_anh_url' => $img('am-sac'), 'am_thanh_mau_url' => $snd('am-sac'), 'thu_tu' => 10, 'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'ầu',   'phien_am' => 'ɤːw',  'cap_do' => 'basic', 'hinh_anh_url' => $img('au'),   'am_thanh_mau_url' => $snd('au'),   'thu_tu' => 11, 'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'cá',   'phien_am' => 'kaː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('ca'),   'am_thanh_mau_url' => $snd('ca'),   'thu_tu' => 12, 'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'câu',  'phien_am' => 'kɤw',  'cap_do' => 'basic', 'hinh_anh_url' => $img('cau'),  'am_thanh_mau_url' => $snd('cau'),  'thu_tu' => 13, 'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'đá',   'phien_am' => 'ɗaː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('da'),   'am_thanh_mau_url' => $snd('da'),   'thu_tu' => 14, 'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'gà',   'phien_am' => 'ɣàː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('ga'),   'am_thanh_mau_url' => $snd('ga'),   'thu_tu' => 15, 'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'na',   'phien_am' => 'naː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('na'),   'am_thanh_mau_url' => $snd('na'),   'thu_tu' => 16, 'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'lá',   'phien_am' => 'laː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('la'),   'am_thanh_mau_url' => $snd('la'),   'thu_tu' => 17, 'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'tay',  'phien_am' => 'taj',  'cap_do' => 'basic', 'hinh_anh_url' => $img('tay'),  'am_thanh_mau_url' => $snd('tay'),  'thu_tu' => 18, 'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'tai',  'phien_am' => 'taj',  'cap_do' => 'basic', 'hinh_anh_url' => $img('tai'),  'am_thanh_mau_url' => $snd('tai'),  'thu_tu' => 19, 'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'sa',   'phien_am' => 'saː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('sa'),   'am_thanh_mau_url' => $snd('sa'),   'thu_tu' => 20, 'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'nhà',  'phien_am' => 'ɲàː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('nha'),  'am_thanh_mau_url' => $snd('nha'),  'thu_tu' => 21, 'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'con',  'phien_am' => 'kɔn',  'cap_do' => 'basic', 'hinh_anh_url' => $img('con'),  'am_thanh_mau_url' => $snd('con'),  'thu_tu' => 22, 'ngay_tao' => $now],
            ['bai_hoc_id' => 1, 'tu_chuan' => 'cha',  'phien_am' => 'tʃaː', 'cap_do' => 'basic', 'hinh_anh_url' => $img('cha'),  'am_thanh_mau_url' => $snd('cha'),  'thu_tu' => 23, 'ngay_tao' => $now],

            // =====================================================================
            // BÀI 2 — id=2 | Danh mục: Âm cơ bản | Phụ âm đơn: b, m, p
            // =====================================================================
            ['bai_hoc_id' => 2, 'tu_chuan' => 'bò',   'phien_am' => 'ɓɔ̀',  'cap_do' => 'basic', 'hinh_anh_url' => $img('bo'),   'am_thanh_mau_url' => $snd('bo'),   'thu_tu' => 1,  'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'bé',   'phien_am' => 'ɓɛ',   'cap_do' => 'basic', 'hinh_anh_url' => $img('be'),   'am_thanh_mau_url' => $snd('be'),   'thu_tu' => 2,  'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'bóng', 'phien_am' => 'ɓoŋ',  'cap_do' => 'basic', 'hinh_anh_url' => $img('bong'), 'am_thanh_mau_url' => $snd('bong'), 'thu_tu' => 3,  'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'bàn',  'phien_am' => 'ɓàn',  'cap_do' => 'basic', 'hinh_anh_url' => $img('ban'),  'am_thanh_mau_url' => $snd('ban'),  'thu_tu' => 4,  'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'bi',   'phien_am' => 'ɓiː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('bi'),   'am_thanh_mau_url' => $snd('bi'),   'thu_tu' => 5,  'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'bu',   'phien_am' => 'ɓuː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('bu'),   'am_thanh_mau_url' => $snd('bu'),   'thu_tu' => 6,  'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'mẹ',   'phien_am' => 'mɛ̰',   'cap_do' => 'basic', 'hinh_anh_url' => $img('me'),   'am_thanh_mau_url' => $snd('me'),   'thu_tu' => 7,  'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'mũi',  'phien_am' => 'muːj',  'cap_do' => 'basic', 'hinh_anh_url' => $img('mui'),  'am_thanh_mau_url' => $snd('mui'),  'thu_tu' => 8,  'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'môi',  'phien_am' => 'moːj',  'cap_do' => 'basic', 'hinh_anh_url' => $img('moi'),  'am_thanh_mau_url' => $snd('moi'),  'thu_tu' => 9,  'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'miệng','phien_am' => 'miəŋ',  'cap_do' => 'basic', 'hinh_anh_url' => $img('mieng'),'am_thanh_mau_url' => $snd('mieng'),'thu_tu' => 10, 'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'mặt',  'phien_am' => 'mat',   'cap_do' => 'basic', 'hinh_anh_url' => $img('mat-nang'),'am_thanh_mau_url' => $snd('mat-nang'),'thu_tu' => 11,'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'pa',   'phien_am' => 'paː',   'cap_do' => 'basic', 'hinh_anh_url' => $img('pa'),   'am_thanh_mau_url' => $snd('pa'),   'thu_tu' => 12, 'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'pin',  'phien_am' => 'piːn',  'cap_do' => 'basic', 'hinh_anh_url' => $img('pin'),  'am_thanh_mau_url' => $snd('pin'),  'thu_tu' => 13, 'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'pháo', 'phien_am' => 'faːw',  'cap_do' => 'basic', 'hinh_anh_url' => $img('phao'), 'am_thanh_mau_url' => $snd('phao'), 'thu_tu' => 14, 'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'phim', 'phien_am' => 'fiːm',  'cap_do' => 'basic', 'hinh_anh_url' => $img('phim'), 'am_thanh_mau_url' => $snd('phim'), 'thu_tu' => 15, 'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'bơm',  'phien_am' => 'ɓɤm',   'cap_do' => 'basic', 'hinh_anh_url' => $img('bom'),  'am_thanh_mau_url' => $snd('bom'),  'thu_tu' => 16, 'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'mưa',  'phien_am' => 'mɯəː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('mua'),  'am_thanh_mau_url' => $snd('mua'),  'thu_tu' => 17, 'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'bút',  'phien_am' => 'ɓut',   'cap_do' => 'basic', 'hinh_anh_url' => $img('but'),  'am_thanh_mau_url' => $snd('but'),  'thu_tu' => 18, 'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'bọ',   'phien_am' => 'ɓɔ̰',   'cap_do' => 'basic', 'hinh_anh_url' => $img('bo-nang'),'am_thanh_mau_url' => $snd('bo-nang'),'thu_tu' => 19,'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'muỗi', 'phien_am' => 'muːj',  'cap_do' => 'basic', 'hinh_anh_url' => $img('muoi'), 'am_thanh_mau_url' => $snd('muoi'), 'thu_tu' => 20, 'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'mèo',  'phien_am' => 'mɛ̀w',  'cap_do' => 'basic', 'hinh_anh_url' => $img('meo'),  'am_thanh_mau_url' => $snd('meo'),  'thu_tu' => 21, 'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'phố',  'phien_am' => 'foː',   'cap_do' => 'basic', 'hinh_anh_url' => $img('pho-sac'),'am_thanh_mau_url' => $snd('pho-sac'),'thu_tu' => 22,'ngay_tao' => $now],
            ['bai_hoc_id' => 2, 'tu_chuan' => 'bầu',  'phien_am' => 'ɓɤːw',  'cap_do' => 'basic', 'hinh_anh_url' => $img('bau'),  'am_thanh_mau_url' => $snd('bau'),  'thu_tu' => 23, 'ngay_tao' => $now],

            // =====================================================================
            // BÀI 3 — id=3 | Danh mục: Từ và âm thanh quen thuộc
            //          Từ hàng ngày: mẹ, ba, ăn, chơi
            // =====================================================================
            ['bai_hoc_id' => 3, 'tu_chuan' => 'mẹ',    'phien_am' => 'mɛ̰',   'cap_do' => 'basic', 'hinh_anh_url' => $img('me'),    'am_thanh_mau_url' => $snd('me'),    'thu_tu' => 1,  'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'ba',     'phien_am' => 'ɓaː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('ba'),    'am_thanh_mau_url' => $snd('ba'),    'thu_tu' => 2,  'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'ăn',     'phien_am' => 'ən',   'cap_do' => 'basic', 'hinh_anh_url' => $img('an-com'),'am_thanh_mau_url' => $snd('an-com'),'thu_tu' => 3,  'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'chơi',   'phien_am' => 'tʃɤːj','cap_do' => 'basic', 'hinh_anh_url' => $img('choi'),  'am_thanh_mau_url' => $snd('choi'),  'thu_tu' => 4,  'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'ngủ',    'phien_am' => 'ŋu',   'cap_do' => 'basic', 'hinh_anh_url' => $img('ngu'),   'am_thanh_mau_url' => $snd('ngu'),   'thu_tu' => 5,  'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'uống',   'phien_am' => 'uəŋ',  'cap_do' => 'basic', 'hinh_anh_url' => $img('uong'),  'am_thanh_mau_url' => $snd('uong'),  'thu_tu' => 6,  'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'đi',     'phien_am' => 'ɗiː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('di'),    'am_thanh_mau_url' => $snd('di'),    'thu_tu' => 7,  'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'chạy',   'phien_am' => 'tʃaj',  'cap_do' => 'basic', 'hinh_anh_url' => $img('chay'),  'am_thanh_mau_url' => $snd('chay'),  'thu_tu' => 8,  'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'cười',   'phien_am' => 'kɯəj', 'cap_do' => 'basic', 'hinh_anh_url' => $img('cuoi'),  'am_thanh_mau_url' => $snd('cuoi'),  'thu_tu' => 9,  'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'khóc',   'phien_am' => 'xɔk',  'cap_do' => 'basic', 'hinh_anh_url' => $img('khoc'),  'am_thanh_mau_url' => $snd('khoc'),  'thu_tu' => 10, 'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'nhìn',   'phien_am' => 'ɲiːn', 'cap_do' => 'basic', 'hinh_anh_url' => $img('nhin'),  'am_thanh_mau_url' => $snd('nhin'),  'thu_tu' => 11, 'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'nghe',   'phien_am' => 'ŋɛ',   'cap_do' => 'basic', 'hinh_anh_url' => $img('nghe'),  'am_thanh_mau_url' => $snd('nghe'),  'thu_tu' => 12, 'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'nói',    'phien_am' => 'noːj',  'cap_do' => 'basic', 'hinh_anh_url' => $img('noi'),   'am_thanh_mau_url' => $snd('noi'),   'thu_tu' => 13, 'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'cơm',    'phien_am' => 'kɤm',  'cap_do' => 'basic', 'hinh_anh_url' => $img('com'),   'am_thanh_mau_url' => $snd('com'),   'thu_tu' => 14, 'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'nước',   'phien_am' => 'nɯək', 'cap_do' => 'basic', 'hinh_anh_url' => $img('nuoc'),  'am_thanh_mau_url' => $snd('nuoc'),  'thu_tu' => 15, 'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'sách',   'phien_am' => 'saːk', 'cap_do' => 'basic', 'hinh_anh_url' => $img('sach'),  'am_thanh_mau_url' => $snd('sach'),  'thu_tu' => 16, 'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'bóng',   'phien_am' => 'ɓoŋ',  'cap_do' => 'basic', 'hinh_anh_url' => $img('bong-tron'),'am_thanh_mau_url' => $snd('bong-tron'),'thu_tu' => 17,'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'đồ chơi','phien_am' => 'ɗo tʃɤːj','cap_do' => 'basic','hinh_anh_url' => $img('do-choi'),'am_thanh_mau_url' => $snd('do-choi'),'thu_tu' => 18,'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'giày',   'phien_am' => 'zaːj', 'cap_do' => 'basic', 'hinh_anh_url' => $img('giay'),  'am_thanh_mau_url' => $snd('giay'),  'thu_tu' => 19, 'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'áo',     'phien_am' => 'aw',   'cap_do' => 'basic', 'hinh_anh_url' => $img('ao'),    'am_thanh_mau_url' => $snd('ao'),    'thu_tu' => 20, 'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'quần',   'phien_am' => 'kwɤn', 'cap_do' => 'basic', 'hinh_anh_url' => $img('quan'),  'am_thanh_mau_url' => $snd('quan'),  'thu_tu' => 21, 'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'tay',    'phien_am' => 'taj',  'cap_do' => 'basic', 'hinh_anh_url' => $img('tay-ban-tay'),'am_thanh_mau_url' => $snd('tay-ban-tay'),'thu_tu' => 22,'ngay_tao' => $now],
            ['bai_hoc_id' => 3, 'tu_chuan' => 'chân',   'phien_am' => 'tʃɤn', 'cap_do' => 'basic', 'hinh_anh_url' => $img('chan'),  'am_thanh_mau_url' => $snd('chan'),  'thu_tu' => 23, 'ngay_tao' => $now],

            // =====================================================================
            // BÀI 4 — id=4 | Danh mục: Âm đôi và phụ âm khó
            //          Nguyên âm đôi: ai, ao, au
            // =====================================================================
            ['bai_hoc_id' => 4, 'tu_chuan' => 'ai',    'phien_am' => 'aj',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ai'),   'am_thanh_mau_url' => $snd('ai'),   'thu_tu' => 1,  'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'ao',    'phien_am' => 'aːw',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ao-nuoc'),'am_thanh_mau_url' => $snd('ao-nuoc'),'thu_tu' => 2, 'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'au',    'phien_am' => 'aːw',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('au-2'),  'am_thanh_mau_url' => $snd('au-2'),  'thu_tu' => 3,  'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'tai',   'phien_am' => 'taj',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('tai-nghe'),'am_thanh_mau_url' => $snd('tai-nghe'),'thu_tu' => 4,'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'mai',   'phien_am' => 'maj',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('mai'),  'am_thanh_mau_url' => $snd('mai'),  'thu_tu' => 5,  'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'gai',   'phien_am' => 'ɣaj',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('gai'),  'am_thanh_mau_url' => $snd('gai'),  'thu_tu' => 6,  'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'hai',   'phien_am' => 'haj',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('hai'),  'am_thanh_mau_url' => $snd('hai'),  'thu_tu' => 7,  'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'bao',   'phien_am' => 'ɓaːw', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('bao'),  'am_thanh_mau_url' => $snd('bao'),  'thu_tu' => 8,  'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'dao',   'phien_am' => 'ɗaːw', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('dao'),  'am_thanh_mau_url' => $snd('dao'),  'thu_tu' => 9,  'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'mao',   'phien_am' => 'maːw', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('mao'),  'am_thanh_mau_url' => $snd('mao'),  'thu_tu' => 10, 'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'sao',   'phien_am' => 'saːw', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('sao'),  'am_thanh_mau_url' => $snd('sao'),  'thu_tu' => 11, 'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'cau',   'phien_am' => 'kaːw', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('cau-cay'),'am_thanh_mau_url' => $snd('cau-cay'),'thu_tu' => 12,'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'lau',   'phien_am' => 'laːw', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('lau'),  'am_thanh_mau_url' => $snd('lau'),  'thu_tu' => 13, 'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'đau',   'phien_am' => 'ɗaːw', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('dau'),  'am_thanh_mau_url' => $snd('dau'),  'thu_tu' => 14, 'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'mái',   'phien_am' => 'maːj', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('mai-sac'),'am_thanh_mau_url' => $snd('mai-sac'),'thu_tu' => 15,'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'trai',  'phien_am' => 'tʃaj', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('trai'), 'am_thanh_mau_url' => $snd('trai'), 'thu_tu' => 16, 'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'gái',   'phien_am' => 'ɣaːj', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('gai-sac'),'am_thanh_mau_url' => $snd('gai-sac'),'thu_tu' => 17,'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'sáo',   'phien_am' => 'saːw', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('sao-sac'),'am_thanh_mau_url' => $snd('sao-sac'),'thu_tu' => 18,'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'báo',   'phien_am' => 'ɓaːw', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('bao-sac'),'am_thanh_mau_url' => $snd('bao-sac'),'thu_tu' => 19,'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'tàu',   'phien_am' => 'taːw', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('tau'),  'am_thanh_mau_url' => $snd('tau'),  'thu_tu' => 20, 'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'máu',   'phien_am' => 'maːw', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('mau'),  'am_thanh_mau_url' => $snd('mau'),  'thu_tu' => 21, 'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'cáo',   'phien_am' => 'kaːw', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('cao-con'),'am_thanh_mau_url' => $snd('cao-con'),'thu_tu' => 22,'ngay_tao' => $now],
            ['bai_hoc_id' => 4, 'tu_chuan' => 'rau',   'phien_am' => 'zaːw', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('rau'),  'am_thanh_mau_url' => $snd('rau'),  'thu_tu' => 23, 'ngay_tao' => $now],

            // =====================================================================
            // BÀI 5 — id=5 | Danh mục: Âm đôi và phụ âm khó
            //          Phụ âm khó: tr, ch, r
            // =====================================================================
            ['bai_hoc_id' => 5, 'tu_chuan' => 'trăng',  'phien_am' => 'tʂaŋ', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('trang'), 'am_thanh_mau_url' => $snd('trang'), 'thu_tu' => 1,  'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'trâu',   'phien_am' => 'tʂɤw',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('trau'),  'am_thanh_mau_url' => $snd('trau'),  'thu_tu' => 2,  'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'trái',   'phien_am' => 'tʂaj',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('trai-cay'),'am_thanh_mau_url' => $snd('trai-cay'),'thu_tu' => 3,'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'tre',    'phien_am' => 'tʂɛ',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('tre'),   'am_thanh_mau_url' => $snd('tre'),   'thu_tu' => 4,  'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'trời',   'phien_am' => 'tʂɤːj', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('troi'),  'am_thanh_mau_url' => $snd('troi'),  'thu_tu' => 5,  'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'trống',  'phien_am' => 'tʂoŋ',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('trong'), 'am_thanh_mau_url' => $snd('trong'), 'thu_tu' => 6,  'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'chim',   'phien_am' => 'tʃiːm', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('chim'),  'am_thanh_mau_url' => $snd('chim'),  'thu_tu' => 7,  'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'chén',   'phien_am' => 'tʃɛn',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('chen'),  'am_thanh_mau_url' => $snd('chen'),  'thu_tu' => 8,  'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'chân',   'phien_am' => 'tʃɤn',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('chan-2'),'am_thanh_mau_url' => $snd('chan-2'),'thu_tu' => 9,  'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'chai',   'phien_am' => 'tʃaj',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('chai'),  'am_thanh_mau_url' => $snd('chai'),  'thu_tu' => 10, 'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'cháo',   'phien_am' => 'tʃaːw', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('chao'),  'am_thanh_mau_url' => $snd('chao'),  'thu_tu' => 11, 'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'rồng',   'phien_am' => 'zoŋ',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('rong'),  'am_thanh_mau_url' => $snd('rong'),  'thu_tu' => 12, 'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'rừng',   'phien_am' => 'zɯŋ',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('rung'),  'am_thanh_mau_url' => $snd('rung'),  'thu_tu' => 13, 'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'rổ',     'phien_am' => 'zo',    'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ro'),    'am_thanh_mau_url' => $snd('ro'),    'thu_tu' => 14, 'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'ruồi',   'phien_am' => 'zuəj',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ruoi'),  'am_thanh_mau_url' => $snd('ruoi'),  'thu_tu' => 15, 'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'rể',     'phien_am' => 'zɛ',    'cap_do' => 'intermediate', 'hinh_anh_url' => $img('re'),    'am_thanh_mau_url' => $snd('re'),    'thu_tu' => 16, 'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'chùa',   'phien_am' => 'tʃuə',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('chua'),  'am_thanh_mau_url' => $snd('chua'),  'thu_tu' => 17, 'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'tre nứa','phien_am' => 'tʂɛ nɯə','cap_do' => 'intermediate','hinh_anh_url' => $img('tre-nua'),'am_thanh_mau_url' => $snd('tre-nua'),'thu_tu' => 18,'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'chợ',    'phien_am' => 'tʃɤ',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('cho'),   'am_thanh_mau_url' => $snd('cho'),   'thu_tu' => 19, 'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'rán',    'phien_am' => 'zan',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ran'),   'am_thanh_mau_url' => $snd('ran'),   'thu_tu' => 20, 'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'chữa',   'phien_am' => 'tʃɯə',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('chua-benh'),'am_thanh_mau_url' => $snd('chua-benh'),'thu_tu' => 21,'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'trứng',  'phien_am' => 'tʂɯŋ',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('trung'), 'am_thanh_mau_url' => $snd('trung'), 'thu_tu' => 22, 'ngay_tao' => $now],
            ['bai_hoc_id' => 5, 'tu_chuan' => 'rắn',    'phien_am' => 'zan',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ran-con'),'am_thanh_mau_url' => $snd('ran-con'),'thu_tu' => 23,'ngay_tao' => $now],

            // =====================================================================
            // BÀI 6 — id=6 | Danh mục: Thanh điệu tiếng Việt
            //          Thanh ngang và thanh huyền: phân biệt cơ bản
            // =====================================================================
            ['bai_hoc_id' => 6, 'tu_chuan' => 'ma',  'phien_am' => 'maː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ma-ngang'), 'am_thanh_mau_url' => $snd('ma-ngang'), 'thu_tu' => 1,  'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'mà',  'phien_am' => 'màː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ma-huyen'), 'am_thanh_mau_url' => $snd('ma-huyen'), 'thu_tu' => 2,  'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'la',  'phien_am' => 'laː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('la-ngang'), 'am_thanh_mau_url' => $snd('la-ngang'), 'thu_tu' => 3,  'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'là',  'phien_am' => 'làː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('la-huyen'), 'am_thanh_mau_url' => $snd('la-huyen'), 'thu_tu' => 4,  'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'ca',  'phien_am' => 'kaː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ca-ngang'), 'am_thanh_mau_url' => $snd('ca-ngang'), 'thu_tu' => 5,  'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'cà',  'phien_am' => 'kàː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ca-huyen'), 'am_thanh_mau_url' => $snd('ca-huyen'), 'thu_tu' => 6,  'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'ta',  'phien_am' => 'taː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ta-ngang'), 'am_thanh_mau_url' => $snd('ta-ngang'), 'thu_tu' => 7,  'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'tà',  'phien_am' => 'tàː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ta-huyen'), 'am_thanh_mau_url' => $snd('ta-huyen'), 'thu_tu' => 8,  'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'na',  'phien_am' => 'naː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('na-ngang'), 'am_thanh_mau_url' => $snd('na-ngang'), 'thu_tu' => 9,  'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'nà',  'phien_am' => 'nàː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('na-huyen'), 'am_thanh_mau_url' => $snd('na-huyen'), 'thu_tu' => 10, 'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'bo',  'phien_am' => 'bɔ',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('bo-ngang'), 'am_thanh_mau_url' => $snd('bo-ngang'), 'thu_tu' => 11, 'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'bò',  'phien_am' => 'bɔ̀',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('bo-con'), 'am_thanh_mau_url' => $snd('bo-con'), 'thu_tu' => 12, 'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'cu',  'phien_am' => 'kuː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('cu-ngang'), 'am_thanh_mau_url' => $snd('cu-ngang'), 'thu_tu' => 13, 'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'cù',  'phien_am' => 'kùː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('cu-huyen'), 'am_thanh_mau_url' => $snd('cu-huyen'), 'thu_tu' => 14, 'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'di',  'phien_am' => 'diː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('di-ngang'), 'am_thanh_mau_url' => $snd('di-ngang'), 'thu_tu' => 15, 'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'dì',  'phien_am' => 'dìː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('di-huyen'), 'am_thanh_mau_url' => $snd('di-huyen'), 'thu_tu' => 16, 'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'mu',  'phien_am' => 'muː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('mu-ngang'), 'am_thanh_mau_url' => $snd('mu-ngang'), 'thu_tu' => 17, 'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'mù',  'phien_am' => 'mùː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('mu-huyen'), 'am_thanh_mau_url' => $snd('mu-huyen'), 'thu_tu' => 18, 'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'tu',  'phien_am' => 'tuː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('tu-ngang'), 'am_thanh_mau_url' => $snd('tu-ngang'), 'thu_tu' => 19, 'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'tù',  'phien_am' => 'tùː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('tu-huyen'), 'am_thanh_mau_url' => $snd('tu-huyen'), 'thu_tu' => 20, 'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'sa',  'phien_am' => 'saː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('sa-ngang'), 'am_thanh_mau_url' => $snd('sa-ngang'), 'thu_tu' => 21, 'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'sà',  'phien_am' => 'sàː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('sa-huyen'), 'am_thanh_mau_url' => $snd('sa-huyen'), 'thu_tu' => 22, 'ngay_tao' => $now],
            ['bai_hoc_id' => 6, 'tu_chuan' => 'ha',  'phien_am' => 'haː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ha-ngang'), 'am_thanh_mau_url' => $snd('ha-ngang'), 'thu_tu' => 23, 'ngay_tao' => $now],

            // =====================================================================
            // BÀI 7 — id=7 | Danh mục: Từ vựng theo chủ đề
            //          Chủ đề: Gia đình (mẹ, cha, anh, chị)
            // =====================================================================
            ['bai_hoc_id' => 7, 'tu_chuan' => 'mẹ',     'phien_am' => 'mɛ̰',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('me-gd'),   'am_thanh_mau_url' => $snd('me-gd'),   'thu_tu' => 1,  'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'cha',     'phien_am' => 'tʃaː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('cha-gd'),  'am_thanh_mau_url' => $snd('cha-gd'),  'thu_tu' => 2,  'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'anh',     'phien_am' => 'aːɲ',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('anh-gd'),  'am_thanh_mau_url' => $snd('anh-gd'),  'thu_tu' => 3,  'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'chị',     'phien_am' => 'tʃi',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('chi-gd'),  'am_thanh_mau_url' => $snd('chi-gd'),  'thu_tu' => 4,  'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'em',      'phien_am' => 'ɛm',    'cap_do' => 'intermediate', 'hinh_anh_url' => $img('em-gd'),   'am_thanh_mau_url' => $snd('em-gd'),   'thu_tu' => 5,  'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'ông',     'phien_am' => 'oŋ',    'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ong-gd'),  'am_thanh_mau_url' => $snd('ong-gd'),  'thu_tu' => 6,  'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'bà',      'phien_am' => 'ɓàː',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ba-gd'),   'am_thanh_mau_url' => $snd('ba-gd'),   'thu_tu' => 7,  'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'chú',     'phien_am' => 'tʃuː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('chu-gd'),  'am_thanh_mau_url' => $snd('chu-gd'),  'thu_tu' => 8,  'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'thím',    'phien_am' => 'tiːm',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('thim-gd'), 'am_thanh_mau_url' => $snd('thim-gd'), 'thu_tu' => 9,  'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'cô',      'phien_am' => 'koː',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('co-gd'),   'am_thanh_mau_url' => $snd('co-gd'),   'thu_tu' => 10, 'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'dì',      'phien_am' => 'diː',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('di-gd'),   'am_thanh_mau_url' => $snd('di-gd'),   'thu_tu' => 11, 'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'con',     'phien_am' => 'kɔn',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('con-gd'),  'am_thanh_mau_url' => $snd('con-gd'),  'thu_tu' => 12, 'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'cháu',    'phien_am' => 'tʃaːw', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('chau-gd'), 'am_thanh_mau_url' => $snd('chau-gd'), 'thu_tu' => 13, 'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'gia đình','phien_am' => 'zaː ɗiːɲ','cap_do' => 'intermediate','hinh_anh_url' => $img('gia-dinh'),'am_thanh_mau_url' => $snd('gia-dinh'),'thu_tu' => 14,'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'yêu',     'phien_am' => 'iəw',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('yeu'),     'am_thanh_mau_url' => $snd('yeu'),     'thu_tu' => 15, 'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'ôm',      'phien_am' => 'oːm',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('om'),      'am_thanh_mau_url' => $snd('om'),      'thu_tu' => 16, 'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'hôn',     'phien_am' => 'hoːn',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('hon'),     'am_thanh_mau_url' => $snd('hon'),     'thu_tu' => 17, 'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'giúp',    'phien_am' => 'zuːp',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('giup'),    'am_thanh_mau_url' => $snd('giup'),    'thu_tu' => 18, 'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'nhà',     'phien_am' => 'ɲàː',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('nha-gd'),  'am_thanh_mau_url' => $snd('nha-gd'),  'thu_tu' => 19, 'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'bố',      'phien_am' => 'ɓoː',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('bo-gd'),   'am_thanh_mau_url' => $snd('bo-gd'),   'thu_tu' => 20, 'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'mẹ ơi',   'phien_am' => 'mɛ̰ ɤːj','cap_do' => 'intermediate','hinh_anh_url' => $img('me-oi'),   'am_thanh_mau_url' => $snd('me-oi'),   'thu_tu' => 21, 'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'ba ơi',   'phien_am' => 'ɓaː ɤːj','cap_do' => 'intermediate','hinh_anh_url' => $img('ba-oi'),  'am_thanh_mau_url' => $snd('ba-oi'),   'thu_tu' => 22, 'ngay_tao' => $now],
            ['bai_hoc_id' => 7, 'tu_chuan' => 'anh ơi',  'phien_am' => 'aːɲ ɤːj','cap_do' => 'intermediate','hinh_anh_url' => $img('anh-oi'), 'am_thanh_mau_url' => $snd('anh-oi'),  'thu_tu' => 23, 'ngay_tao' => $now],

            // =====================================================================
            // BÀI 8 — id=8 | Danh mục: Từ vựng theo chủ đề
            //          Chủ đề: Động vật (mèo, chó, gà)
            // =====================================================================
            ['bai_hoc_id' => 8, 'tu_chuan' => 'mèo',    'phien_am' => 'mɛ̀w',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('meo-con'),  'am_thanh_mau_url' => $snd('meo-con'),  'thu_tu' => 1,  'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'chó',    'phien_am' => 'tʃoː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('cho-con'),  'am_thanh_mau_url' => $snd('cho-con'),  'thu_tu' => 2,  'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'gà',     'phien_am' => 'ɣàː',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ga-con'),   'am_thanh_mau_url' => $snd('ga-con'),   'thu_tu' => 3,  'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'vịt',    'phien_am' => 'vit',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('vit-con'),  'am_thanh_mau_url' => $snd('vit-con'),  'thu_tu' => 4,  'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'bò',     'phien_am' => 'ɓɔ̀',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('bo-dong-vat'),'am_thanh_mau_url' => $snd('bo-dong-vat'),'thu_tu' => 5,'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'heo',    'phien_am' => 'hɛw',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('heo'),      'am_thanh_mau_url' => $snd('heo'),      'thu_tu' => 6,  'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'ngựa',   'phien_am' => 'ŋɯə',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ngua'),     'am_thanh_mau_url' => $snd('ngua'),     'thu_tu' => 7,  'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'thỏ',    'phien_am' => 'tʰɔ',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('tho'),      'am_thanh_mau_url' => $snd('tho'),      'thu_tu' => 8,  'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'chim',   'phien_am' => 'tʃiːm', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('chim-bay'), 'am_thanh_mau_url' => $snd('chim-bay'), 'thu_tu' => 9,  'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'cá',     'phien_am' => 'kaː',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ca-boi'),   'am_thanh_mau_url' => $snd('ca-boi'),   'thu_tu' => 10, 'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'ếch',    'phien_am' => 'ɛk',    'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ech'),      'am_thanh_mau_url' => $snd('ech'),      'thu_tu' => 11, 'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'rùa',    'phien_am' => 'zuə',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('rua'),      'am_thanh_mau_url' => $snd('rua'),      'thu_tu' => 12, 'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'khỉ',    'phien_am' => 'xi',    'cap_do' => 'intermediate', 'hinh_anh_url' => $img('khi'),      'am_thanh_mau_url' => $snd('khi'),      'thu_tu' => 13, 'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'voi',    'phien_am' => 'vɔːj',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('voi'),      'am_thanh_mau_url' => $snd('voi'),      'thu_tu' => 14, 'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'hổ',     'phien_am' => 'hoː',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ho'),       'am_thanh_mau_url' => $snd('ho'),       'thu_tu' => 15, 'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'sư tử',  'phien_am' => 'sɯ tɯ', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('su-tu'),    'am_thanh_mau_url' => $snd('su-tu'),    'thu_tu' => 16, 'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'gấu',    'phien_am' => 'ɣɤw',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('gau'),      'am_thanh_mau_url' => $snd('gau'),      'thu_tu' => 17, 'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'bướm',   'phien_am' => 'ɓɯəm',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('buom'),     'am_thanh_mau_url' => $snd('buom'),     'thu_tu' => 18, 'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'sâu',    'phien_am' => 'sɤw',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('sau'),      'am_thanh_mau_url' => $snd('sau'),      'thu_tu' => 19, 'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'kiến',   'phien_am' => 'kiən',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('kien'),     'am_thanh_mau_url' => $snd('kien'),     'thu_tu' => 20, 'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'ong',    'phien_am' => 'ɔŋ',    'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ong-mat'),  'am_thanh_mau_url' => $snd('ong-mat'),  'thu_tu' => 21, 'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'gà trống','phien_am' => 'ɣaː tʂoŋ','cap_do' => 'intermediate','hinh_anh_url' => $img('ga-trong'),'am_thanh_mau_url' => $snd('ga-trong'),'thu_tu' => 22,'ngay_tao' => $now],
            ['bai_hoc_id' => 8, 'tu_chuan' => 'chim sẻ','phien_am' => 'tʃiːm zɛ','cap_do' => 'intermediate','hinh_anh_url' => $img('chim-se'),'am_thanh_mau_url' => $snd('chim-se'),'thu_tu' => 23,'ngay_tao' => $now],

            // =====================================================================
            // BÀI 9 — id=9 | Danh mục: Thanh điệu tiếng Việt
            //          Thanh hỏi, ngã và nặng: luyện nâng cao
            // =====================================================================
            ['bai_hoc_id' => 9, 'tu_chuan' => 'mả',   'phien_am' => 'mǎː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('ma-hoi'),  'am_thanh_mau_url' => $snd('ma-hoi'),  'thu_tu' => 1,  'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'mã',   'phien_am' => 'mãː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('ma-nga'),  'am_thanh_mau_url' => $snd('ma-nga'),  'thu_tu' => 2,  'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'mạ',   'phien_am' => 'mâ̰ː', 'cap_do' => 'advanced', 'hinh_anh_url' => $img('ma-nang'), 'am_thanh_mau_url' => $snd('ma-nang'), 'thu_tu' => 3,  'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'lả',   'phien_am' => 'lǎː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('la-hoi'),  'am_thanh_mau_url' => $snd('la-hoi'),  'thu_tu' => 4,  'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'lã',   'phien_am' => 'lãː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('la-nga'),  'am_thanh_mau_url' => $snd('la-nga'),  'thu_tu' => 5,  'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'lạ',   'phien_am' => 'lâ̰ː', 'cap_do' => 'advanced', 'hinh_anh_url' => $img('la-nang'), 'am_thanh_mau_url' => $snd('la-nang'), 'thu_tu' => 6,  'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'cả',   'phien_am' => 'kǎː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('ca-hoi'),  'am_thanh_mau_url' => $snd('ca-hoi'),  'thu_tu' => 7,  'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'cã',   'phien_am' => 'kãː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('ca-nga'),  'am_thanh_mau_url' => $snd('ca-nga'),  'thu_tu' => 8,  'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'cạ',   'phien_am' => 'kâ̰ː', 'cap_do' => 'advanced', 'hinh_anh_url' => $img('ca-nang'), 'am_thanh_mau_url' => $snd('ca-nang'), 'thu_tu' => 9,  'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'ngả',  'phien_am' => 'ŋǎː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('nga-hoi'), 'am_thanh_mau_url' => $snd('nga-hoi'), 'thu_tu' => 10, 'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'ngã',  'phien_am' => 'ŋãː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('nga-nga'), 'am_thanh_mau_url' => $snd('nga-nga'), 'thu_tu' => 11, 'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'ngạ',  'phien_am' => 'ŋâ̰ː', 'cap_do' => 'advanced', 'hinh_anh_url' => $img('nga-nang'),'am_thanh_mau_url' => $snd('nga-nang'),'thu_tu' => 12, 'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'bỏ',   'phien_am' => 'ɓǒː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('bo-hoi'),  'am_thanh_mau_url' => $snd('bo-hoi'),  'thu_tu' => 13, 'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'bõ',   'phien_am' => 'ɓõː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('bo-nga'),  'am_thanh_mau_url' => $snd('bo-nga'),  'thu_tu' => 14, 'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'bọ',   'phien_am' => 'ɓô̰ː', 'cap_do' => 'advanced', 'hinh_anh_url' => $img('bo-nang'), 'am_thanh_mau_url' => $snd('bo-nang'), 'thu_tu' => 15, 'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'tổ',   'phien_am' => 'tǒː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('to-hoi'),  'am_thanh_mau_url' => $snd('to-hoi'),  'thu_tu' => 16, 'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'tỗ',   'phien_am' => 'tõː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('to-nga'),  'am_thanh_mau_url' => $snd('to-nga'),  'thu_tu' => 17, 'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'tộ',   'phien_am' => 'tô̰ː', 'cap_do' => 'advanced', 'hinh_anh_url' => $img('to-nang'), 'am_thanh_mau_url' => $snd('to-nang'), 'thu_tu' => 18, 'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'hỏi',  'phien_am' => 'hǒːj', 'cap_do' => 'advanced', 'hinh_anh_url' => $img('hoi-thanh'),'am_thanh_mau_url' => $snd('hoi-thanh'),'thu_tu' => 19,'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'ngã',  'phien_am' => 'ŋãː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('nga-thanh'),'am_thanh_mau_url' => $snd('nga-thanh'),'thu_tu' => 20,'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'nặng', 'phien_am' => 'naŋ',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('nang-thanh'),'am_thanh_mau_url' => $snd('nang-thanh'),'thu_tu' => 21,'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'gỏi',  'phien_am' => 'ɣǒːj', 'cap_do' => 'advanced', 'hinh_anh_url' => $img('goi-hoi'), 'am_thanh_mau_url' => $snd('goi-hoi'), 'thu_tu' => 22, 'ngay_tao' => $now],
            ['bai_hoc_id' => 9, 'tu_chuan' => 'gọi',  'phien_am' => 'ɣô̰ːj','cap_do' => 'advanced', 'hinh_anh_url' => $img('goi-nang'),'am_thanh_mau_url' => $snd('goi-nang'),'thu_tu' => 23,'ngay_tao' => $now],

            // =====================================================================
            // BÀI 10 — id=10 | Danh mục: Câu giao tiếp ngắn
            //           Câu ngắn: chào hỏi và giới thiệu bản thân
            // =====================================================================
            ['bai_hoc_id' => 10, 'tu_chuan' => 'xin chào',    'phien_am' => 'siːn tʃaːw',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('xin-chao'),    'am_thanh_mau_url' => $snd('xin-chao'),    'thu_tu' => 1,  'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'tạm biệt',    'phien_am' => 'tam biəkt',    'cap_do' => 'advanced', 'hinh_anh_url' => $img('tam-biet'),    'am_thanh_mau_url' => $snd('tam-biet'),    'thu_tu' => 2,  'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'tên tôi là',  'phien_am' => 'ten toj laː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('ten-toi-la'),  'am_thanh_mau_url' => $snd('ten-toi-la'),  'thu_tu' => 3,  'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'bạn khoẻ không','phien_am' => 'ban xwɛ xoŋ','cap_do' => 'advanced','hinh_anh_url' => $img('ban-khoe-khong'),'am_thanh_mau_url' => $snd('ban-khoe-khong'),'thu_tu' => 4,'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'tôi khoẻ',    'phien_am' => 'toj xwɛ',     'cap_do' => 'advanced', 'hinh_anh_url' => $img('toi-khoe'),    'am_thanh_mau_url' => $snd('toi-khoe'),    'thu_tu' => 5,  'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'cảm ơn',      'phien_am' => 'kam ɤn',       'cap_do' => 'advanced', 'hinh_anh_url' => $img('cam-on'),      'am_thanh_mau_url' => $snd('cam-on'),      'thu_tu' => 6,  'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'không có gì', 'phien_am' => 'xoŋ kɔ ziː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('khong-co-gi'), 'am_thanh_mau_url' => $snd('khong-co-gi'), 'thu_tu' => 7,  'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'xin lỗi',     'phien_am' => 'siːn lôːj',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('xin-loi'),     'am_thanh_mau_url' => $snd('xin-loi'),     'thu_tu' => 8,  'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'không sao',   'phien_am' => 'xoŋ saːw',    'cap_do' => 'advanced', 'hinh_anh_url' => $img('khong-sao'),   'am_thanh_mau_url' => $snd('khong-sao'),   'thu_tu' => 9,  'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'vâng',        'phien_am' => 'vɤŋ',          'cap_do' => 'advanced', 'hinh_anh_url' => $img('vang'),        'am_thanh_mau_url' => $snd('vang'),        'thu_tu' => 10, 'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'dạ',          'phien_am' => 'ɗa',           'cap_do' => 'advanced', 'hinh_anh_url' => $img('da-vang'),     'am_thanh_mau_url' => $snd('da-vang'),     'thu_tu' => 11, 'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'không',       'phien_am' => 'xoŋ',          'cap_do' => 'advanced', 'hinh_anh_url' => $img('khong'),       'am_thanh_mau_url' => $snd('khong'),       'thu_tu' => 12, 'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'được',        'phien_am' => 'ɗɯək',         'cap_do' => 'advanced', 'hinh_anh_url' => $img('duoc'),        'am_thanh_mau_url' => $snd('duoc'),        'thu_tu' => 13, 'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'tôi muốn',   'phien_am' => 'toj muən',     'cap_do' => 'advanced', 'hinh_anh_url' => $img('toi-muon'),    'am_thanh_mau_url' => $snd('toi-muon'),    'thu_tu' => 14, 'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'tôi thích',  'phien_am' => 'toj tʰik',     'cap_do' => 'advanced', 'hinh_anh_url' => $img('toi-thich'),   'am_thanh_mau_url' => $snd('toi-thich'),   'thu_tu' => 15, 'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'tôi hiểu',   'phien_am' => 'toj hiəw',     'cap_do' => 'advanced', 'hinh_anh_url' => $img('toi-hieu'),    'am_thanh_mau_url' => $snd('toi-hieu'),    'thu_tu' => 16, 'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'chào buổi sáng','phien_am' => 'tʃaːw ɓuəj saŋ','cap_do' => 'advanced','hinh_anh_url' => $img('chao-buoi-sang'),'am_thanh_mau_url' => $snd('chao-buoi-sang'),'thu_tu' => 17,'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'chào buổi chiều','phien_am' => 'tʃaːw ɓuəj tʃiəw','cap_do' => 'advanced','hinh_anh_url' => $img('chao-buoi-chieu'),'am_thanh_mau_url' => $snd('chao-buoi-chieu'),'thu_tu' => 18,'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'chúc ngủ ngon','phien_am' => 'tʃuk ŋu ŋɔn','cap_do' => 'advanced', 'hinh_anh_url' => $img('chuc-ngu-ngon'),'am_thanh_mau_url' => $snd('chuc-ngu-ngon'),'thu_tu' => 19,'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'mấy tuổi',   'phien_am' => 'mɤːj tuəj',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('may-tuoi'),    'am_thanh_mau_url' => $snd('may-tuoi'),    'thu_tu' => 20, 'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'ở đâu',      'phien_am' => 'ɤː ɗɤw',      'cap_do' => 'advanced', 'hinh_anh_url' => $img('o-dau'),       'am_thanh_mau_url' => $snd('o-dau'),       'thu_tu' => 21, 'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'bạn tên gì', 'phien_am' => 'ban ten ziː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('ban-ten-gi'),  'am_thanh_mau_url' => $snd('ban-ten-gi'),  'thu_tu' => 22, 'ngay_tao' => $now],
            ['bai_hoc_id' => 10, 'tu_chuan' => 'hẹn gặp lại','phien_am' => 'hɛn ɣap laj',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('hen-gap-lai'), 'am_thanh_mau_url' => $snd('hen-gap-lai'), 'thu_tu' => 23, 'ngay_tao' => $now],

            // =====================================================================
            // BÀI 11 — id=11 | Danh mục: Ngữ điệu và nhịp điệu
            //           Ngữ điệu: câu hỏi và câu khẳng định
            // =====================================================================
            ['bai_hoc_id' => 11, 'tu_chuan' => 'đây là gì',     'phien_am' => 'ɗɤːj laː ziː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('day-la-gi'),     'am_thanh_mau_url' => $snd('day-la-gi'),     'thu_tu' => 1,  'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'đây là bút',    'phien_am' => 'ɗɤːj laː ɓut',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('day-la-but'),    'am_thanh_mau_url' => $snd('day-la-but'),    'thu_tu' => 2,  'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'con thích không','phien_am' => 'kɔn tʰik xoŋ','cap_do' => 'advanced', 'hinh_anh_url' => $img('con-thich-khong'),'am_thanh_mau_url' => $snd('con-thich-khong'),'thu_tu' => 3,'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'con thích',      'phien_am' => 'kɔn tʰik',     'cap_do' => 'advanced', 'hinh_anh_url' => $img('con-thich'),     'am_thanh_mau_url' => $snd('con-thich'),     'thu_tu' => 4,  'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'mẹ ơi con đói', 'phien_am' => 'mɛ̰ ɤːj kɔn ɗoj','cap_do' => 'advanced','hinh_anh_url' => $img('me-oi-con-doi'),'am_thanh_mau_url' => $snd('me-oi-con-doi'),'thu_tu' => 5,'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'ai đó',         'phien_am' => 'aj ɗoː',       'cap_do' => 'advanced', 'hinh_anh_url' => $img('ai-do'),         'am_thanh_mau_url' => $snd('ai-do'),         'thu_tu' => 6,  'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'cái này',        'phien_am' => 'kaj naːj',     'cap_do' => 'advanced', 'hinh_anh_url' => $img('cai-nay'),       'am_thanh_mau_url' => $snd('cai-nay'),       'thu_tu' => 7,  'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'cái kia',        'phien_am' => 'kaj kiə',      'cap_do' => 'advanced', 'hinh_anh_url' => $img('cai-kia'),       'am_thanh_mau_url' => $snd('cai-kia'),       'thu_tu' => 8,  'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'bao nhiêu',      'phien_am' => 'ɓaːw ɲiəw',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('bao-nhieu'),     'am_thanh_mau_url' => $snd('bao-nhieu'),     'thu_tu' => 9,  'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'ở đâu',          'phien_am' => 'ɤː ɗɤw',      'cap_do' => 'advanced', 'hinh_anh_url' => $img('o-dau-2'),       'am_thanh_mau_url' => $snd('o-dau-2'),       'thu_tu' => 10, 'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'khi nào',        'phien_am' => 'xi naːw',      'cap_do' => 'advanced', 'hinh_anh_url' => $img('khi-nao'),       'am_thanh_mau_url' => $snd('khi-nao'),       'thu_tu' => 11, 'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'tại sao',        'phien_am' => 'taj saːw',     'cap_do' => 'advanced', 'hinh_anh_url' => $img('tai-sao'),       'am_thanh_mau_url' => $snd('tai-sao'),       'thu_tu' => 12, 'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'có không',       'phien_am' => 'kɔ xoŋ',      'cap_do' => 'advanced', 'hinh_anh_url' => $img('co-khong'),      'am_thanh_mau_url' => $snd('co-khong'),      'thu_tu' => 13, 'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'có',             'phien_am' => 'kɔ',           'cap_do' => 'advanced', 'hinh_anh_url' => $img('co-la'),         'am_thanh_mau_url' => $snd('co-la'),         'thu_tu' => 14, 'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'đúng rồi',       'phien_am' => 'ɗuŋ zoːj',    'cap_do' => 'advanced', 'hinh_anh_url' => $img('dung-roi'),      'am_thanh_mau_url' => $snd('dung-roi'),      'thu_tu' => 15, 'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'sai rồi',        'phien_am' => 'saj zoːj',     'cap_do' => 'advanced', 'hinh_anh_url' => $img('sai-roi'),       'am_thanh_mau_url' => $snd('sai-roi'),       'thu_tu' => 16, 'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'giỏi lắm',       'phien_am' => 'zoːj lam',     'cap_do' => 'advanced', 'hinh_anh_url' => $img('gioi-lam'),      'am_thanh_mau_url' => $snd('gioi-lam'),      'thu_tu' => 17, 'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'cố lên',         'phien_am' => 'koː lɛn',      'cap_do' => 'advanced', 'hinh_anh_url' => $img('co-len'),        'am_thanh_mau_url' => $snd('co-len'),        'thu_tu' => 18, 'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'làm lại',        'phien_am' => 'lam laj',      'cap_do' => 'advanced', 'hinh_anh_url' => $img('lam-lai'),       'am_thanh_mau_url' => $snd('lam-lai'),       'thu_tu' => 19, 'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'thử lại',        'phien_am' => 'tʰɯ laj',      'cap_do' => 'advanced', 'hinh_anh_url' => $img('thu-lai'),       'am_thanh_mau_url' => $snd('thu-lai'),       'thu_tu' => 20, 'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'nói chậm thôi', 'phien_am' => 'noːj tʃɤm tʰoj','cap_do' => 'advanced','hinh_anh_url' => $img('noi-cham-thoi'),'am_thanh_mau_url' => $snd('noi-cham-thoi'),'thu_tu' => 21,'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'nói to hơn',    'phien_am' => 'noːj tɔ hɤn',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('noi-to-hon'),    'am_thanh_mau_url' => $snd('noi-to-hon'),    'thu_tu' => 22, 'ngay_tao' => $now],
            ['bai_hoc_id' => 11, 'tu_chuan' => 'nói lại nào',   'phien_am' => 'noːj laj naːw', 'cap_do' => 'advanced', 'hinh_anh_url' => $img('noi-lai-nao'),   'am_thanh_mau_url' => $snd('noi-lai-nao'),   'thu_tu' => 23, 'ngay_tao' => $now],

            // =====================================================================
            // BÀI 12 — id=12 | Danh mục: Âm đôi và phụ âm khó
            //           Bài tập phân biệt r/gi/tr cho trẻ gặp khó khăn
            // =====================================================================
            ['bai_hoc_id' => 12, 'tu_chuan' => 'ra',    'phien_am' => 'zaː',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('ra-r'),    'am_thanh_mau_url' => $snd('ra-r'),    'thu_tu' => 1,  'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'gia',   'phien_am' => 'zaː',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('gia-gi'),  'am_thanh_mau_url' => $snd('gia-gi'),  'thu_tu' => 2,  'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'tra',   'phien_am' => 'tʂaː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('tra-tr'),  'am_thanh_mau_url' => $snd('tra-tr'),  'thu_tu' => 3,  'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'rõ',    'phien_am' => 'zoː',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('ro-nga'),  'am_thanh_mau_url' => $snd('ro-nga'),  'thu_tu' => 4,  'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'giỡ',   'phien_am' => 'zɤː',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('gio-r'),   'am_thanh_mau_url' => $snd('gio-r'),   'thu_tu' => 5,  'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'trỡ',   'phien_am' => 'tʂɤː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('tro-tr'),  'am_thanh_mau_url' => $snd('tro-tr'),  'thu_tu' => 6,  'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'ru',    'phien_am' => 'zuː',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('ru-con'),  'am_thanh_mau_url' => $snd('ru-con'),  'thu_tu' => 7,  'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'riu',   'phien_am' => 'ziw',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('riu'),     'am_thanh_mau_url' => $snd('riu'),     'thu_tu' => 8,  'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'giun',  'phien_am' => 'zuːn',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('giun'),    'am_thanh_mau_url' => $snd('giun'),    'thu_tu' => 9,  'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'trụ',   'phien_am' => 'tʂu',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('tru'),     'am_thanh_mau_url' => $snd('tru'),     'thu_tu' => 10, 'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'rễ',    'phien_am' => 'zɛ',    'cap_do' => 'advanced', 'hinh_anh_url' => $img('re-cay'),  'am_thanh_mau_url' => $snd('re-cay'),  'thu_tu' => 11, 'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'giẻ',   'phien_am' => 'zɛ',    'cap_do' => 'advanced', 'hinh_anh_url' => $img('gie'),     'am_thanh_mau_url' => $snd('gie'),     'thu_tu' => 12, 'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'trẻ',   'phien_am' => 'tʂɛ',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('tre-nho'), 'am_thanh_mau_url' => $snd('tre-nho'), 'thu_tu' => 13, 'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'rèm',   'phien_am' => 'zɛm',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('rem'),     'am_thanh_mau_url' => $snd('rem'),     'thu_tu' => 14, 'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'giếng', 'phien_am' => 'ziəŋ',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('gieng'),   'am_thanh_mau_url' => $snd('gieng'),   'thu_tu' => 15, 'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'triết', 'phien_am' => 'tʂiət',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('triet'),   'am_thanh_mau_url' => $snd('triet'),   'thu_tu' => 16, 'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'riêng', 'phien_am' => 'ziəŋ',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('rieng'),   'am_thanh_mau_url' => $snd('rieng'),   'thu_tu' => 17, 'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'gian',  'phien_am' => 'zan',    'cap_do' => 'advanced', 'hinh_anh_url' => $img('gian'),    'am_thanh_mau_url' => $snd('gian'),    'thu_tu' => 18, 'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'tran',  'phien_am' => 'tʂan',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('tran'),    'am_thanh_mau_url' => $snd('tran'),    'thu_tu' => 19, 'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'rang',  'phien_am' => 'zaŋ',    'cap_do' => 'advanced', 'hinh_anh_url' => $img('rang-rang'),'am_thanh_mau_url' => $snd('rang-rang'),'thu_tu' => 20,'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'giang', 'phien_am' => 'zaŋ',    'cap_do' => 'advanced', 'hinh_anh_url' => $img('giang'),   'am_thanh_mau_url' => $snd('giang'),   'thu_tu' => 21, 'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'trang', 'phien_am' => 'tʂaŋ',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('trang-sach'),'am_thanh_mau_url' => $snd('trang-sach'),'thu_tu' => 22,'ngay_tao' => $now],
            ['bai_hoc_id' => 12, 'tu_chuan' => 'rong',  'phien_am' => 'zoŋ',    'cap_do' => 'advanced', 'hinh_anh_url' => $img('rong-bien'),'am_thanh_mau_url' => $snd('rong-bien'),'thu_tu' => 23,'ngay_tao' => $now],

            // =====================================================================
            // BÀI 13 — id=13 | Danh mục: Ngữ điệu và nhịp điệu
            //           Nhịp câu và ngắt nghỉ cho trẻ chậm phát triển ngôn ngữ
            // =====================================================================
            ['bai_hoc_id' => 13, 'tu_chuan' => 'con / ăn',         'phien_am' => 'kɔn | an',        'cap_do' => 'advanced', 'hinh_anh_url' => $img('con-an'),          'am_thanh_mau_url' => $snd('con-an'),          'thu_tu' => 1,  'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'mẹ / nấu cơm',     'phien_am' => 'mɛ̰ | nɤw kɤm',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('me-nau-com'),      'am_thanh_mau_url' => $snd('me-nau-com'),      'thu_tu' => 2,  'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'con / uống nước',   'phien_am' => 'kɔn | uəŋ nɯək', 'cap_do' => 'advanced', 'hinh_anh_url' => $img('con-uong-nuoc'),   'am_thanh_mau_url' => $snd('con-uong-nuoc'),   'thu_tu' => 3,  'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'bé / ngủ rồi',     'phien_am' => 'ɓɛ | ŋu zoːj',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('be-ngu-roi'),      'am_thanh_mau_url' => $snd('be-ngu-roi'),      'thu_tu' => 4,  'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'bé / đi học',      'phien_am' => 'ɓɛ | ɗiː hɔk',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('be-di-hoc'),       'am_thanh_mau_url' => $snd('be-di-hoc'),       'thu_tu' => 5,  'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'ông / đọc sách',   'phien_am' => 'oŋ | ɗɔk saːk',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('ong-doc-sach'),    'am_thanh_mau_url' => $snd('ong-doc-sach'),    'thu_tu' => 6,  'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'bà / nấu ăn',      'phien_am' => 'ɓàː | nɤw an',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('ba-nau-an'),       'am_thanh_mau_url' => $snd('ba-nau-an'),       'thu_tu' => 7,  'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'chị / hát',        'phien_am' => 'tʃi | hat',       'cap_do' => 'advanced', 'hinh_anh_url' => $img('chi-hat'),         'am_thanh_mau_url' => $snd('chi-hat'),         'thu_tu' => 8,  'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'anh / chạy',       'phien_am' => 'aːɲ | tʃaj',     'cap_do' => 'advanced', 'hinh_anh_url' => $img('anh-chay'),        'am_thanh_mau_url' => $snd('anh-chay'),        'thu_tu' => 9,  'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'con / yêu mẹ',     'phien_am' => 'kɔn | iəw mɛ̰',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('con-yeu-me'),      'am_thanh_mau_url' => $snd('con-yeu-me'),      'thu_tu' => 10, 'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'hít vào / thở ra', 'phien_am' => 'hit vaːw | tʰɤ zaː','cap_do' => 'advanced','hinh_anh_url' => $img('hit-vao-tho-ra'),'am_thanh_mau_url' => $snd('hit-vao-tho-ra'),'thu_tu' => 11,'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'một / hai / ba',   'phien_am' => 'mot | haj | ɓaː', 'cap_do' => 'advanced', 'hinh_anh_url' => $img('mot-hai-ba'),      'am_thanh_mau_url' => $snd('mot-hai-ba'),      'thu_tu' => 12, 'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'vỗ tay / dậm chân','phien_am' => 'voː taj | ɗam tʃɤn','cap_do' => 'advanced','hinh_anh_url' => $img('vo-tay-dam-chan'),'am_thanh_mau_url' => $snd('vo-tay-dam-chan'),'thu_tu' => 13,'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'nhún vai / lắc đầu','phien_am' => 'ɲun vaj | lak ɗɤw','cap_do' => 'advanced','hinh_anh_url' => $img('nhun-vai-lac-dau'),'am_thanh_mau_url' => $snd('nhun-vai-lac-dau'),'thu_tu' => 14,'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'nói nhanh',        'phien_am' => 'noːj ɲaːɲ',      'cap_do' => 'advanced', 'hinh_anh_url' => $img('noi-nhanh'),       'am_thanh_mau_url' => $snd('noi-nhanh'),       'thu_tu' => 15, 'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'nói chậm',         'phien_am' => 'noːj tʃɤm',      'cap_do' => 'advanced', 'hinh_anh_url' => $img('noi-cham'),        'am_thanh_mau_url' => $snd('noi-cham'),        'thu_tu' => 16, 'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'đọc to',           'phien_am' => 'ɗɔk tɔ',         'cap_do' => 'advanced', 'hinh_anh_url' => $img('doc-to'),          'am_thanh_mau_url' => $snd('doc-to'),          'thu_tu' => 17, 'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'thở đều',          'phien_am' => 'tʰɤ ɗiəw',       'cap_do' => 'advanced', 'hinh_anh_url' => $img('tho-deu'),         'am_thanh_mau_url' => $snd('tho-deu'),         'thu_tu' => 18, 'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'ngừng lại',        'phien_am' => 'ŋɯŋ laj',        'cap_do' => 'advanced', 'hinh_anh_url' => $img('ngung-lai'),       'am_thanh_mau_url' => $snd('ngung-lai'),       'thu_tu' => 19, 'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'tiếp tục',         'phien_am' => 'tiəp tuk',        'cap_do' => 'advanced', 'hinh_anh_url' => $img('tiep-tuc'),        'am_thanh_mau_url' => $snd('tiep-tuc'),        'thu_tu' => 20, 'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'lắng nghe',        'phien_am' => 'laŋ ŋɛ',         'cap_do' => 'advanced', 'hinh_anh_url' => $img('lang-nghe'),       'am_thanh_mau_url' => $snd('lang-nghe'),       'thu_tu' => 21, 'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'bắt chước',        'phien_am' => 'ɓak tʃɯək',      'cap_do' => 'advanced', 'hinh_anh_url' => $img('bat-chuoc'),       'am_thanh_mau_url' => $snd('bat-chuoc'),       'thu_tu' => 22, 'ngay_tao' => $now],
            ['bai_hoc_id' => 13, 'tu_chuan' => 'làm theo',         'phien_am' => 'lam tʰɛw',        'cap_do' => 'advanced', 'hinh_anh_url' => $img('lam-theo'),        'am_thanh_mau_url' => $snd('lam-theo'),        'thu_tu' => 23, 'ngay_tao' => $now],

            // =====================================================================
            // BÀI 14 — id=14 | Danh mục: Ôn tập và kiểm tra tiến bộ
            //           Bài kiểm tra cơ bản: âm và từ
            // =====================================================================
            ['bai_hoc_id' => 14, 'tu_chuan' => 'a',    'phien_am' => 'aː',    'cap_do' => 'basic', 'hinh_anh_url' => $img('on-a'),    'am_thanh_mau_url' => $snd('on-a'),    'thu_tu' => 1,  'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'ă',    'phien_am' => 'a',     'cap_do' => 'basic', 'hinh_anh_url' => $img('on-a-ngan'),'am_thanh_mau_url' => $snd('on-a-ngan'),'thu_tu' => 2, 'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'â',    'phien_am' => 'ɤ̞',   'cap_do' => 'basic', 'hinh_anh_url' => $img('on-a-mu'), 'am_thanh_mau_url' => $snd('on-a-mu'), 'thu_tu' => 3,  'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'b',    'phien_am' => 'ɓ',    'cap_do' => 'basic', 'hinh_anh_url' => $img('on-b'),    'am_thanh_mau_url' => $snd('on-b'),    'thu_tu' => 4,  'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'm',    'phien_am' => 'm',     'cap_do' => 'basic', 'hinh_anh_url' => $img('on-m'),    'am_thanh_mau_url' => $snd('on-m'),    'thu_tu' => 5,  'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'p',    'phien_am' => 'p',     'cap_do' => 'basic', 'hinh_anh_url' => $img('on-p'),    'am_thanh_mau_url' => $snd('on-p'),    'thu_tu' => 6,  'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'mẹ',  'phien_am' => 'mɛ̰',   'cap_do' => 'basic', 'hinh_anh_url' => $img('on-me'),   'am_thanh_mau_url' => $snd('on-me'),   'thu_tu' => 7,  'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'ba',  'phien_am' => 'ɓaː',   'cap_do' => 'basic', 'hinh_anh_url' => $img('on-ba'),   'am_thanh_mau_url' => $snd('on-ba'),   'thu_tu' => 8,  'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'ăn',  'phien_am' => 'an',    'cap_do' => 'basic', 'hinh_anh_url' => $img('on-an'),   'am_thanh_mau_url' => $snd('on-an'),   'thu_tu' => 9,  'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'chơi','phien_am' => 'tʃɤːj', 'cap_do' => 'basic', 'hinh_anh_url' => $img('on-choi'), 'am_thanh_mau_url' => $snd('on-choi'), 'thu_tu' => 10, 'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'bóng','phien_am' => 'ɓoŋ',   'cap_do' => 'basic', 'hinh_anh_url' => $img('on-bong'), 'am_thanh_mau_url' => $snd('on-bong'), 'thu_tu' => 11, 'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'sách','phien_am' => 'saːk',  'cap_do' => 'basic', 'hinh_anh_url' => $img('on-sach'), 'am_thanh_mau_url' => $snd('on-sach'), 'thu_tu' => 12, 'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'cá',  'phien_am' => 'kaː',   'cap_do' => 'basic', 'hinh_anh_url' => $img('on-ca'),   'am_thanh_mau_url' => $snd('on-ca'),   'thu_tu' => 13, 'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'gà',  'phien_am' => 'ɣàː',   'cap_do' => 'basic', 'hinh_anh_url' => $img('on-ga'),   'am_thanh_mau_url' => $snd('on-ga'),   'thu_tu' => 14, 'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'mèo', 'phien_am' => 'mɛ̀w',  'cap_do' => 'basic', 'hinh_anh_url' => $img('on-meo'),  'am_thanh_mau_url' => $snd('on-meo'),  'thu_tu' => 15, 'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'chó', 'phien_am' => 'tʃoː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('on-cho'),  'am_thanh_mau_url' => $snd('on-cho'),  'thu_tu' => 16, 'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'mưa', 'phien_am' => 'mɯəː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('on-mua'),  'am_thanh_mau_url' => $snd('on-mua'),  'thu_tu' => 17, 'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'trời','phien_am' => 'tʂɤːj', 'cap_do' => 'basic', 'hinh_anh_url' => $img('on-troi'), 'am_thanh_mau_url' => $snd('on-troi'), 'thu_tu' => 18, 'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'lá',  'phien_am' => 'laː',   'cap_do' => 'basic', 'hinh_anh_url' => $img('on-la'),   'am_thanh_mau_url' => $snd('on-la'),   'thu_tu' => 19, 'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'hoa', 'phien_am' => 'hwaː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('on-hoa'),  'am_thanh_mau_url' => $snd('on-hoa'),  'thu_tu' => 20, 'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'nước','phien_am' => 'nɯək',  'cap_do' => 'basic', 'hinh_anh_url' => $img('on-nuoc'), 'am_thanh_mau_url' => $snd('on-nuoc'), 'thu_tu' => 21, 'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'cơm', 'phien_am' => 'kɤm',   'cap_do' => 'basic', 'hinh_anh_url' => $img('on-com'),  'am_thanh_mau_url' => $snd('on-com'),  'thu_tu' => 22, 'ngay_tao' => $now],
            ['bai_hoc_id' => 14, 'tu_chuan' => 'nhà', 'phien_am' => 'ɲàː',   'cap_do' => 'basic', 'hinh_anh_url' => $img('on-nha'),  'am_thanh_mau_url' => $snd('on-nha'),  'thu_tu' => 23, 'ngay_tao' => $now],

            // =====================================================================
            // BÀI 15 — id=15 | Danh mục: Ôn tập và kiểm tra tiến bộ
            //           Bài kiểm tra nâng cao: thanh điệu và ngữ điệu
            // =====================================================================
            ['bai_hoc_id' => 15, 'tu_chuan' => 'ma',   'phien_am' => 'maː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-ma-ngang'),  'am_thanh_mau_url' => $snd('kt-ma-ngang'),  'thu_tu' => 1,  'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'mà',   'phien_am' => 'màː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-ma-huyen'),  'am_thanh_mau_url' => $snd('kt-ma-huyen'),  'thu_tu' => 2,  'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'má',   'phien_am' => 'máː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-ma-sac'),    'am_thanh_mau_url' => $snd('kt-ma-sac'),    'thu_tu' => 3,  'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'mả',   'phien_am' => 'mǎː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-ma-hoi'),    'am_thanh_mau_url' => $snd('kt-ma-hoi'),    'thu_tu' => 4,  'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'mã',   'phien_am' => 'mãː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-ma-nga'),    'am_thanh_mau_url' => $snd('kt-ma-nga'),    'thu_tu' => 5,  'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'mạ',   'phien_am' => 'mâ̰ː', 'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-ma-nang'),   'am_thanh_mau_url' => $snd('kt-ma-nang'),   'thu_tu' => 6,  'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'bo',   'phien_am' => 'bɔ',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-bo-ngang'),  'am_thanh_mau_url' => $snd('kt-bo-ngang'),  'thu_tu' => 7,  'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'bò',   'phien_am' => 'bɔ̀',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-bo-huyen'),  'am_thanh_mau_url' => $snd('kt-bo-huyen'),  'thu_tu' => 8,  'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'bó',   'phien_am' => 'bóː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-bo-sac'),    'am_thanh_mau_url' => $snd('kt-bo-sac'),    'thu_tu' => 9,  'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'bỏ',   'phien_am' => 'bǒː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-bo-hoi'),    'am_thanh_mau_url' => $snd('kt-bo-hoi'),    'thu_tu' => 10, 'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'bõ',   'phien_am' => 'bõː',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-bo-nga'),    'am_thanh_mau_url' => $snd('kt-bo-nga'),    'thu_tu' => 11, 'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'bọ',   'phien_am' => 'bô̰ː', 'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-bo-nang'),   'am_thanh_mau_url' => $snd('kt-bo-nang'),   'thu_tu' => 12, 'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'xin chào',    'phien_am' => 'siːn tʃaːw',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-xin-chao'),   'am_thanh_mau_url' => $snd('kt-xin-chao'),   'thu_tu' => 13, 'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'tạm biệt',    'phien_am' => 'tam biəkt',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-tam-biet'),   'am_thanh_mau_url' => $snd('kt-tam-biet'),   'thu_tu' => 14, 'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'cảm ơn',      'phien_am' => 'kam ɤn',      'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-cam-on'),     'am_thanh_mau_url' => $snd('kt-cam-on'),     'thu_tu' => 15, 'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'xin lỗi',     'phien_am' => 'siːn lôːj',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-xin-loi'),    'am_thanh_mau_url' => $snd('kt-xin-loi'),    'thu_tu' => 16, 'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'trăng',  'phien_am' => 'tʂaŋ',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-trang'),   'am_thanh_mau_url' => $snd('kt-trang'),   'thu_tu' => 17, 'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'rồng',   'phien_am' => 'zoŋ',    'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-rong'),    'am_thanh_mau_url' => $snd('kt-rong'),    'thu_tu' => 18, 'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'chim',   'phien_am' => 'tʃiːm',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-chim'),    'am_thanh_mau_url' => $snd('kt-chim'),    'thu_tu' => 19, 'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'trứng',  'phien_am' => 'tʂɯŋ',   'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-trung'),   'am_thanh_mau_url' => $snd('kt-trung'),   'thu_tu' => 20, 'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'r/gi/tr luyện','phien_am' => 'r | zi | tʂ', 'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-r-gi-tr'),  'am_thanh_mau_url' => $snd('kt-r-gi-tr'),  'thu_tu' => 21, 'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'câu hỏi','phien_am' => 'kɤw hoj',  'cap_do' => 'advanced', 'hinh_anh_url' => $img('kt-cau-hoi'), 'am_thanh_mau_url' => $snd('kt-cau-hoi'), 'thu_tu' => 22, 'ngay_tao' => $now],
            ['bai_hoc_id' => 15, 'tu_chuan' => 'câu khẳng định','phien_am' => 'kɤw xɤŋ ɗiːɲ','cap_do' => 'advanced','hinh_anh_url' => $img('kt-cau-khang-dinh'),'am_thanh_mau_url' => $snd('kt-cau-khang-dinh'),'thu_tu' => 23,'ngay_tao' => $now],

            // =====================================================================
            // BÀI 16 — id=16 | Danh mục: Âm ghép đơn giản
            //           Ghép âm: ba, be, bi, bo, bu
            // =====================================================================
            ['bai_hoc_id' => 16, 'tu_chuan' => 'ba',  'phien_am' => 'ɓaː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('ghep-ba'),  'am_thanh_mau_url' => $snd('ghep-ba'),  'thu_tu' => 1,  'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'be',  'phien_am' => 'ɓɛ',   'cap_do' => 'basic', 'hinh_anh_url' => $img('ghep-be'),  'am_thanh_mau_url' => $snd('ghep-be'),  'thu_tu' => 2,  'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bi',  'phien_am' => 'ɓiː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('ghep-bi'),  'am_thanh_mau_url' => $snd('ghep-bi'),  'thu_tu' => 3,  'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bo',  'phien_am' => 'ɓɔ',   'cap_do' => 'basic', 'hinh_anh_url' => $img('ghep-bo'),  'am_thanh_mau_url' => $snd('ghep-bo'),  'thu_tu' => 4,  'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bu',  'phien_am' => 'ɓuː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('ghep-bu'),  'am_thanh_mau_url' => $snd('ghep-bu'),  'thu_tu' => 5,  'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bà',  'phien_am' => 'ɓàː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('ghep-ba-huyen'),'am_thanh_mau_url' => $snd('ghep-ba-huyen'),'thu_tu' => 6,'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bé',  'phien_am' => 'ɓɛ',   'cap_do' => 'basic', 'hinh_anh_url' => $img('ghep-be-sac'),'am_thanh_mau_url' => $snd('ghep-be-sac'),'thu_tu' => 7,'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bí',  'phien_am' => 'ɓiː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('ghep-bi-sac'),'am_thanh_mau_url' => $snd('ghep-bi-sac'),'thu_tu' => 8,'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bò',  'phien_am' => 'ɓɔ̀',  'cap_do' => 'basic', 'hinh_anh_url' => $img('ghep-bo-huyen'),'am_thanh_mau_url' => $snd('ghep-bo-huyen'),'thu_tu' => 9,'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bú',  'phien_am' => 'ɓuː',  'cap_do' => 'basic', 'hinh_anh_url' => $img('ghep-bu-sac'),'am_thanh_mau_url' => $snd('ghep-bu-sac'),'thu_tu' => 10,'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bàn', 'phien_am' => 'ɓàn',  'cap_do' => 'basic', 'hinh_anh_url' => $img('ghep-ban'),  'am_thanh_mau_url' => $snd('ghep-ban'),  'thu_tu' => 11, 'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bếp', 'phien_am' => 'ɓɛp',  'cap_do' => 'basic', 'hinh_anh_url' => $img('ghep-bep'),  'am_thanh_mau_url' => $snd('ghep-bep'),  'thu_tu' => 12, 'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bình','phien_am' => 'ɓiːɲ', 'cap_do' => 'basic', 'hinh_anh_url' => $img('ghep-binh'), 'am_thanh_mau_url' => $snd('ghep-binh'), 'thu_tu' => 13, 'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bọng','phien_am' => 'ɓɔŋ',  'cap_do' => 'basic', 'hinh_anh_url' => $img('ghep-bong-2'),'am_thanh_mau_url' => $snd('ghep-bong-2'),'thu_tu' => 14,'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bụi', 'phien_am' => 'ɓui',  'cap_do' => 'basic', 'hinh_anh_url' => $img('ghep-bui'),  'am_thanh_mau_url' => $snd('ghep-bui'),  'thu_tu' => 15, 'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bước','phien_am' => 'ɓɯək', 'cap_do' => 'basic', 'hinh_anh_url' => $img('ghep-buoc'), 'am_thanh_mau_url' => $snd('ghep-buoc'), 'thu_tu' => 16, 'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bướm','phien_am' => 'ɓɯəm', 'cap_do' => 'basic', 'hinh_anh_url' => $img('ghep-buom'), 'am_thanh_mau_url' => $snd('ghep-buom'), 'thu_tu' => 17, 'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bây giờ','phien_am' => 'ɓɤj zɤː','cap_do' => 'basic','hinh_anh_url' => $img('ghep-bay-gio'),'am_thanh_mau_url' => $snd('ghep-bay-gio'),'thu_tu' => 18,'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bên cạnh','phien_am' => 'ɓɛn kaːɲ','cap_do' => 'basic','hinh_anh_url' => $img('ghep-ben-canh'),'am_thanh_mau_url' => $snd('ghep-ben-canh'),'thu_tu' => 19,'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bố mẹ','phien_am' => 'ɓoː mɛ̰','cap_do' => 'basic','hinh_anh_url' => $img('ghep-bo-me'),'am_thanh_mau_url' => $snd('ghep-bo-me'),'thu_tu' => 20,'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bình thường','phien_am' => 'ɓiːɲ tʰɯəŋ','cap_do' => 'basic','hinh_anh_url' => $img('ghep-binh-thuong'),'am_thanh_mau_url' => $snd('ghep-binh-thuong'),'thu_tu' => 21,'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'bốc hơi','phien_am' => 'ɓok hɤːj','cap_do' => 'basic','hinh_anh_url' => $img('ghep-boc-hoi'),'am_thanh_mau_url' => $snd('ghep-boc-hoi'),'thu_tu' => 22,'ngay_tao' => $now],
            ['bai_hoc_id' => 16, 'tu_chuan' => 'buồn','phien_am' => 'ɓuən',  'cap_do' => 'basic', 'hinh_anh_url' => $img('ghep-buon'), 'am_thanh_mau_url' => $snd('ghep-buon'), 'thu_tu' => 23, 'ngay_tao' => $now],

            // =====================================================================
            // BÀI 17 — id=17 | Danh mục: Âm ghép đơn giản
            //           Ghép âm: ca, co, cu, câ, cươ (luyện âm c/k)
            // =====================================================================
            ['bai_hoc_id' => 17, 'tu_chuan' => 'ca',   'phien_am' => 'kaː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ck-ca'),   'am_thanh_mau_url' => $snd('ck-ca'),   'thu_tu' => 1,  'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'co',   'phien_am' => 'kɔ',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ck-co'),   'am_thanh_mau_url' => $snd('ck-co'),   'thu_tu' => 2,  'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'cu',   'phien_am' => 'kuː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ck-cu'),   'am_thanh_mau_url' => $snd('ck-cu'),   'thu_tu' => 3,  'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'câ',   'phien_am' => 'kɤː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ck-cau'),  'am_thanh_mau_url' => $snd('ck-cau'),  'thu_tu' => 4,  'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'cươ',  'phien_am' => 'kɯə',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ck-cuoc'), 'am_thanh_mau_url' => $snd('ck-cuoc'), 'thu_tu' => 5,  'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'ki',   'phien_am' => 'kiː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ck-ki'),   'am_thanh_mau_url' => $snd('ck-ki'),   'thu_tu' => 6,  'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'ke',   'phien_am' => 'kɛ',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ck-ke'),   'am_thanh_mau_url' => $snd('ck-ke'),   'thu_tu' => 7,  'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'cá',   'phien_am' => 'kaː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ck-ca-sac'),'am_thanh_mau_url' => $snd('ck-ca-sac'),'thu_tu' => 8,'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'cô',   'phien_am' => 'koː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ck-co-sac'),'am_thanh_mau_url' => $snd('ck-co-sac'),'thu_tu' => 9,'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'cú',   'phien_am' => 'kuː',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ck-cu-sac'),'am_thanh_mau_url' => $snd('ck-cu-sac'),'thu_tu' => 10,'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'cây',  'phien_am' => 'kɤːj', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ck-cay'),  'am_thanh_mau_url' => $snd('ck-cay'),  'thu_tu' => 11, 'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'cửa',  'phien_am' => 'kɯə',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ck-cua'),  'am_thanh_mau_url' => $snd('ck-cua'),  'thu_tu' => 12, 'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'kính', 'phien_am' => 'kiːɲ', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ck-kinh'), 'am_thanh_mau_url' => $snd('ck-kinh'), 'thu_tu' => 13, 'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'kéo',  'phien_am' => 'kɛw',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ck-keo'),  'am_thanh_mau_url' => $snd('ck-keo'),  'thu_tu' => 14, 'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'cắt',  'phien_am' => 'kat',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ck-cat'),  'am_thanh_mau_url' => $snd('ck-cat'),  'thu_tu' => 15, 'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'cuốc', 'phien_am' => 'kuək', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ck-cuoc-2'),'am_thanh_mau_url' => $snd('ck-cuoc-2'),'thu_tu' => 16,'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'kể',   'phien_am' => 'kɛ',   'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ck-ke-2'), 'am_thanh_mau_url' => $snd('ck-ke-2'), 'thu_tu' => 17, 'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'cắn',  'phien_am' => 'kan',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ck-can'),  'am_thanh_mau_url' => $snd('ck-can'),  'thu_tu' => 18, 'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'cánh', 'phien_am' => 'kaːɲ', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('ck-canh'), 'am_thanh_mau_url' => $snd('ck-canh'), 'thu_tu' => 19, 'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'con kiến','phien_am' => 'kɔn kiən','cap_do' => 'intermediate','hinh_anh_url' => $img('ck-con-kien'),'am_thanh_mau_url' => $snd('ck-con-kien'),'thu_tu' => 20,'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'con cua','phien_am' => 'kɔn kuə','cap_do' => 'intermediate','hinh_anh_url' => $img('ck-con-cua'),'am_thanh_mau_url' => $snd('ck-con-cua'),'thu_tu' => 21,'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'con cá','phien_am' => 'kɔn kaː','cap_do' => 'intermediate','hinh_anh_url' => $img('ck-con-ca'),'am_thanh_mau_url' => $snd('ck-con-ca'),'thu_tu' => 22,'ngay_tao' => $now],
            ['bai_hoc_id' => 17, 'tu_chuan' => 'cúc cung cúc','phien_am' => 'kuk kuŋ kuk','cap_do' => 'intermediate','hinh_anh_url' => $img('ck-cuc-cung-cuc'),'am_thanh_mau_url' => $snd('ck-cuc-cung-cuc'),'thu_tu' => 23,'ngay_tao' => $now],

            // =====================================================================
            // BÀI 18 — id=18 | Danh mục: Từ vựng theo chủ đề
            //           Chủ đề: Trường học (bút, sách, bảng)
            // =====================================================================
            ['bai_hoc_id' => 18, 'tu_chuan' => 'bút',        'phien_am' => 'ɓut',      'cap_do' => 'intermediate', 'hinh_anh_url' => $img('th-but'),        'am_thanh_mau_url' => $snd('th-but'),        'thu_tu' => 1,  'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'sách',       'phien_am' => 'saːk',     'cap_do' => 'intermediate', 'hinh_anh_url' => $img('th-sach'),       'am_thanh_mau_url' => $snd('th-sach'),       'thu_tu' => 2,  'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'bảng',       'phien_am' => 'ɓaŋ',      'cap_do' => 'intermediate', 'hinh_anh_url' => $img('th-bang'),       'am_thanh_mau_url' => $snd('th-bang'),       'thu_tu' => 3,  'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'lớp học',    'phien_am' => 'lɤp hɔk',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('th-lop-hoc'),    'am_thanh_mau_url' => $snd('th-lop-hoc'),    'thu_tu' => 4,  'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'thầy giáo',  'phien_am' => 'tʰɤːj zaːw','cap_do' => 'intermediate','hinh_anh_url' => $img('th-thay-giao'),  'am_thanh_mau_url' => $snd('th-thay-giao'),  'thu_tu' => 5,  'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'cô giáo',    'phien_am' => 'koː zaːw',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('th-co-giao'),    'am_thanh_mau_url' => $snd('th-co-giao'),    'thu_tu' => 6,  'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'học sinh',   'phien_am' => 'hɔk ʃiːɲ', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('th-hoc-sinh'),   'am_thanh_mau_url' => $snd('th-hoc-sinh'),   'thu_tu' => 7,  'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'ghế',        'phien_am' => 'ɣɛ',       'cap_do' => 'intermediate', 'hinh_anh_url' => $img('th-ghe'),        'am_thanh_mau_url' => $snd('th-ghe'),        'thu_tu' => 8,  'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'bàn học',    'phien_am' => 'ɓàn hɔk',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('th-ban-hoc'),    'am_thanh_mau_url' => $snd('th-ban-hoc'),    'thu_tu' => 9,  'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'thước kẻ',   'phien_am' => 'tʰɯək kɛ', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('th-thuoc-ke'),   'am_thanh_mau_url' => $snd('th-thuoc-ke'),   'thu_tu' => 10, 'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'tẩy',        'phien_am' => 'tɤːj',     'cap_do' => 'intermediate', 'hinh_anh_url' => $img('th-tay-but'),    'am_thanh_mau_url' => $snd('th-tay-but'),    'thu_tu' => 11, 'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'kéo',        'phien_am' => 'kɛw',      'cap_do' => 'intermediate', 'hinh_anh_url' => $img('th-keo-cat'),    'am_thanh_mau_url' => $snd('th-keo-cat'),    'thu_tu' => 12, 'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'hồ dán',     'phien_am' => 'hoː zan',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('th-ho-dan'),     'am_thanh_mau_url' => $snd('th-ho-dan'),     'thu_tu' => 13, 'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'màu vẽ',     'phien_am' => 'maːw vɛ',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('th-mau-ve'),     'am_thanh_mau_url' => $snd('th-mau-ve'),     'thu_tu' => 14, 'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'cặp sách',   'phien_am' => 'kap saːk', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('th-cap-sach'),   'am_thanh_mau_url' => $snd('th-cap-sach'),   'thu_tu' => 15, 'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'đọc sách',   'phien_am' => 'ɗɔk saːk', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('th-doc-sach'),   'am_thanh_mau_url' => $snd('th-doc-sach'),   'thu_tu' => 16, 'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'viết bài',   'phien_am' => 'viət ɓaj', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('th-viet-bai'),   'am_thanh_mau_url' => $snd('th-viet-bai'),   'thu_tu' => 17, 'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'tô màu',     'phien_am' => 'toː maːw', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('th-to-mau'),     'am_thanh_mau_url' => $snd('th-to-mau'),     'thu_tu' => 18, 'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'vẽ tranh',   'phien_am' => 'vɛ tʂaːɲ', 'cap_do' => 'intermediate', 'hinh_anh_url' => $img('th-ve-tranh'),   'am_thanh_mau_url' => $snd('th-ve-tranh'),   'thu_tu' => 19, 'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'trường học', 'phien_am' => 'tʂɯəŋ hɔk','cap_do' => 'intermediate','hinh_anh_url' => $img('th-truong-hoc'),  'am_thanh_mau_url' => $snd('th-truong-hoc'),  'thu_tu' => 20, 'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'sân trường', 'phien_am' => 'sɤn tʂɯəŋ','cap_do' => 'intermediate','hinh_anh_url' => $img('th-san-truong'),  'am_thanh_mau_url' => $snd('th-san-truong'),  'thu_tu' => 21, 'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'bạn học',    'phien_am' => 'ɓan hɔk',  'cap_do' => 'intermediate', 'hinh_anh_url' => $img('th-ban-hoc-2'), 'am_thanh_mau_url' => $snd('th-ban-hoc-2'), 'thu_tu' => 22, 'ngay_tao' => $now],
            ['bai_hoc_id' => 18, 'tu_chuan' => 'giờ ra chơi','phien_am' => 'zɤː zaː tʃɤːj','cap_do' => 'intermediate','hinh_anh_url' => $img('th-gio-ra-choi'),'am_thanh_mau_url' => $snd('th-gio-ra-choi'),'thu_tu' => 23,'ngay_tao' => $now],
        ];

        // Thêm cột ngay_tao = timestamp hiện tại vào tất cả bản ghi trước khi insert
        $tuVungs = array_map(function (array $row) {
            $row['ngay_tao'] = $row['ngay_tao'] instanceof \Carbon\Carbon
                ? $row['ngay_tao']
                : \Carbon\Carbon::now();
            return $row;
        }, $tuVungs);

        // Chia nhỏ thành batch 50 để tránh lỗi "too many parameters"
        foreach (array_chunk($tuVungs, 50) as $chunk) {
            DB::table('tu_vungs')->upsert(
                $chunk,
                ['bai_hoc_id', 'tu_chuan', 'thu_tu'],       // khoá xác định bản ghi
                ['phien_am', 'cap_do', 'hinh_anh_url', 'am_thanh_mau_url']  // cột cập nhật nếu trùng
            );
        }
    }
}

