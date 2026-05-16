<?php

namespace App\Services\AI\Rag\Tools;

use App\Models\BaiHoc;
use App\Models\DanhMucBaiHoc;
use App\Models\NguoiDung;
use App\Services\AI\Rag\Analytics\LearningAnalyticsRepository;

class StudentProgressCatalogTools
{
    public function __construct(
        private readonly LearningAnalyticsRepository $analytics,
    ) {}

    /**
     * @return list<array{name:string,description:string,args:array<string,string>}>
     */
    public function definitions(): array
    {
        return [
            [
                'name' => 'student_get_lesson_categories',
                'description' => 'Đếm và liệt kê danh mục bài học (chủ đề) đang hiển thị trên hệ thống, kèm số bài học trong từng danh mục. Dùng khi hỏi có bao nhiêu danh mục/chủ đề bài học.',
                'args' => [],
            ],
            [
                'name' => 'student_get_learning_inventory',
                'description' => 'Đếm số bài học học viên đã hoàn thành và đang học dở, kèm danh sách gần đây. Dùng khi hỏi "đã học bao nhiêu bài", "hoàn thành mấy bài", tiến độ bài học của bản thân.',
                'args' => [],
            ],
            [
                'name' => 'student_get_next_lesson_recommendation',
                'description' => 'Recommend the next lesson the student should study (from learning path or lesson order)',
                'args' => [
                    'bai_hoc_id' => 'integer, optional anchor lesson id',
                ],
            ],
            [
                'name' => 'student_get_vocabulary_mastery',
                'description' => 'Vocabulary mastered vs learning and practice counts',
                'args' => [
                    'days' => 'integer, period in days (default 7)',
                ],
            ],
            [
                'name' => 'student_get_teacher_suggestions',
                'description' => 'Gợi ý luyện tập giáo viên đã gửi cho học viên (bảng goi_y_luyen_tap: bài học, lời nhắn, ưu tiên). Dùng khi hỏi "gợi ý luyện tập", "giáo viên gợi ý gì". KHÔNG dùng student_get_suggested_lessons_by_level cho câu này.',
                'args' => [
                    'limit' => 'integer, max items (default 10)',
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $args
     * @return array<string, mixed>
     */
    public function execute(NguoiDung $student, string $toolName, array $args): array
    {
        return match ($toolName) {
            'student_get_lesson_categories' => [
                'ok' => true,
                'data' => $this->lessonCategories(),
            ],
            'student_get_learning_inventory' => [
                'ok' => true,
                'data' => $this->analytics->learningInventory($student),
            ],
            'student_get_next_lesson_recommendation' => $this->nextLesson($student, $args),
            'student_get_vocabulary_mastery' => [
                'ok' => true,
                'data' => $this->analytics->vocabularyMastery(
                    $student,
                    max(1, (int) ($args['days'] ?? 7))
                ),
            ],
            'student_get_teacher_suggestions' => [
                'ok' => true,
                'data' => $this->analytics->teacherSuggestions(
                    $student,
                    min(20, max(1, (int) ($args['limit'] ?? 10)))
                ),
            ],
            default => [
                'ok' => false,
                'message' => 'Công cụ không được hỗ trợ.',
            ],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function lessonCategories(): array
    {
        $categories = DanhMucBaiHoc::query()
            ->where('trang_thai', DanhMucBaiHoc::TRANG_THAI_HIEN_THI)
            ->orderBy('thu_tu')
            ->orderBy('id')
            ->withCount([
                'baiHocs as lesson_count' => static function ($query): void {
                    $query->where('trang_thai', BaiHoc::TRANG_THAI_HOAT_DONG);
                },
            ])
            ->get(['id', 'ten_danh_muc', 'mo_ta']);

        $items = $categories->map(static fn (DanhMucBaiHoc $row): array => [
            'id' => (int) $row->id,
            'ten_danh_muc' => (string) ($row->ten_danh_muc ?? ''),
            'lesson_count' => (int) ($row->lesson_count ?? 0),
        ])->values()->all();

        return [
            'category_count' => count($items),
            'total_active_lessons' => (int) array_sum(array_column($items, 'lesson_count')),
            'categories' => $items,
        ];
    }

    /**
     * @param array<string, mixed> $args
     * @return array<string, mixed>
     */
    private function nextLesson(NguoiDung $student, array $args): array
    {
        $baiHocId = isset($args['bai_hoc_id']) ? (int) $args['bai_hoc_id'] : null;
        if ($baiHocId === 0) {
            $baiHocId = null;
        }

        $next = $this->analytics->nextLessonRecommendation($student, $baiHocId);
        if ($next === null) {
            return [
                'ok' => true,
                'data' => ['found' => false, 'message' => 'Chưa có bài gợi ý phù hợp'],
            ];
        }

        return [
            'ok' => true,
            'data' => array_merge(['found' => true], $next),
        ];
    }
}
