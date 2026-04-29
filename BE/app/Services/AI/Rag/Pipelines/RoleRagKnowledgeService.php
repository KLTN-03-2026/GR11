<?php

namespace App\Services\AI\Rag\Pipelines;

use App\Services\AI\Rag\Support\TextNormalizer;

class RoleRagKnowledgeService
{
    public function __construct(
        private readonly TextNormalizer $normalizer
    ) {
    }

    /**
     * @return array{message:string,action_url:?string,action_label:?string,topic:string}|null
     */
    public function tryAnswer(string $assistantRole, string $message): ?array
    {
        $normalized = $this->normalizer->normalize($message);
        if ($normalized === '') {
            return null;
        }

        $knowledge = $this->knowledgeByRole($assistantRole);
        foreach ($knowledge as $item) {
            $keywords = (array) ($item['keywords'] ?? []);
            if ($keywords === []) {
                continue;
            }

            $matched = 0;
            foreach ($keywords as $keyword) {
                if ($keyword !== '' && str_contains($normalized, (string) $keyword)) {
                    $matched++;
                }
            }

            // Tại sao: chỉ trả lời RAG khi độ khớp đủ rõ để tránh trả lời sai ngữ cảnh.
            if ($matched >= max(1, min(2, (int) floor(count($keywords) / 2)))) {
                return [
                    'message' => (string) ($item['answer'] ?? ''),
                    'action_url' => isset($item['action_url']) ? (string) $item['action_url'] : null,
                    'action_label' => isset($item['action_label']) ? (string) $item['action_label'] : null,
                    'topic' => (string) ($item['topic'] ?? 'general'),
                ];
            }
        }

        return null;
    }

    /**
     * @return list<array{topic:string,keywords:list<string>,answer:string,action_url?:string,action_label?:string}>
     */
    private function knowledgeByRole(string $assistantRole): array
    {
        return [
            [
                'topic' => 'client_progress',
                'keywords' => ['tien do', 'diem', 'lich su hoc'],
                'answer' => 'Con có thể xem tiến độ và điểm ở mục Tiến độ. Nếu muốn, con gửi cô mốc thời gian cụ thể để cô nhận xét chi tiết hơn nhé.',
                'action_url' => '/tien-do',
                'action_label' => 'tại đây',
            ],
            [
                'topic' => 'client_lesson_navigation',
                'keywords' => ['bai hoc', 'doi bai', 'hoc gi'],
                'answer' => 'Con vào mục Bài học để chọn bài phù hợp theo mức độ. Mình nên bắt đầu từ bài dễ rồi tăng dần để phát âm chắc hơn.',
                'action_url' => '/bai-hoc',
                'action_label' => 'tại đây',
            ],
            [
                'topic' => 'client_profile_support',
                'keywords' => ['ho so', 'avatar', 'doi ten'],
                'answer' => 'Nếu cần đổi thông tin cá nhân, con vào mục Hồ sơ để cập nhật tên hoặc ảnh đại diện rồi bấm lưu nhé.',
                'action_url' => '/profile',
                'action_label' => 'tại đây',
            ],
        ];
    }
}

