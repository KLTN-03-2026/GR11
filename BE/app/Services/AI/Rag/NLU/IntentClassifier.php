<?php

namespace App\Services\AI\Rag\NLU;

use App\Services\AI\Rag\Support\TextNormalizer;

class IntentClassifier
{
    public function __construct(
        private readonly TextNormalizer $normalizer
    ) {
    }

    public function detect(string $message, string $assistantRole = 'client'): string
    {
        $text = $this->normalizer->normalize($message);

        if ($text === '') {
            return 'unknown';
        }

        $intents = [
            'greeting' => ['chao', 'hello', 'hi', 'xin chao'],
            'system_support' => [
                'doi avatar',
                'hinh dai dien',
                'doi ten',
                'doi ho ten',
                'cap nhat ten',
                'sua ten',
                'doi thong tin',
                'chinh sua ho so',
                'mic',
                'micro',
                'man hinh den',
                'den thui',
                'loa',
                'am thanh',
                'khong nghe',
                'khong vao duoc',
                'loi mang',
                'quen mat khau',
                'dang xuat',
            ],
            'ask_score' => [
                'diem',
                'ket qua',
                'cham diem',
                'bao nhieu diem',
                'score',
                'diem cao nhat',
                'cao nhat',
                'diem toi da',
            ],
            'ask_mistake' => ['sai o dau', 'loi', 'am dau', 'van', 'thanh dieu', 'sua loi'],
            'ask_lesson' => ['bai hoc', 'goi y bai', 'hoc gi', 'de xuat bai', 'lo trinh'],
            'ask_pronunciation' => ['doc sao cho dung', 'doc mau', 'phat am', 'apple doc', 'doc chu'],
            'ask_lesson_switch' => ['doi bai', 'bai khac', 'de hon', 'bai nay kho'],
            'ask_parent_report' => ['be nha toi', 'phu huynh', 'luyen duoc bao nhieu cau', 'bao nhieu cau roi'],
            'motivation_low' => ['buon ngu', 'khong thich hoc', 'muon nghi', 'nghi choi game'],
            'practice_chat' => ['hoi thoai', 'tro chuyen', 'giao tiep', 'phan xa', 'tap noi'],
            'ask_progress' => [
                'tien do',
                'cai thien',
                'hoc den dau',
                'da hoc',
                'lich su hoc tap',
                'lich su',
                'xem tien do',
                'da hoc bai nao',
                'hoc bai nao',
                'thoi gian luyen tap',
                'tong bao nhieu gio',
                'bao nhieu gio',
                'tong so gio',
            ],
        ];

        foreach ($intents as $intent => $keywords) {
            if ($this->matchAnyKeyword($text, $keywords)) {
                return $intent;
            }
        }

        return 'unknown';
    }

    /**
     * @param list<string> $keywords
     */
    private function matchAnyKeyword(string $text, array $keywords): bool
    {
        $parts = array_values(array_filter(explode(' ', $text), static fn(string $part): bool => $part !== ''));

        foreach ($keywords as $keyword) {
            if ($this->containsKeyword($text, $keyword)) {
                return true;
            }

            foreach ($parts as $part) {
                // Fuzzy match only for sufficiently long single-word keywords.
                // This avoids false positives like "cho" being classified as "chao".
                if (str_contains($keyword, ' ')) {
                    continue;
                }
                if (mb_strlen($keyword, 'UTF-8') < 4 || mb_strlen($part, 'UTF-8') < 4) {
                    continue;
                }
                if (abs(mb_strlen($part, 'UTF-8') - mb_strlen($keyword, 'UTF-8')) > 1) {
                    continue;
                }
                if (levenshtein($part, $keyword) <= 1) {
                    return true;
                }
            }
        }

        return false;
    }

    private function containsKeyword(string $text, string $keyword): bool
    {
        if ($keyword === '') {
            return false;
        }

        // Short keyword (e.g. "hi") must match as a full token.
        if (mb_strlen($keyword, 'UTF-8') <= 2) {
            $pattern = '/(?:^|\s)' . preg_quote($keyword, '/') . '(?:\s|$)/u';
            return preg_match($pattern, $text) === 1;
        }

        return str_contains($text, $keyword);
    }
}
