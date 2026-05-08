<?php

namespace App\Support;

use Carbon\CarbonInterface;

final class ChatDisplayTime
{
    /**
     * Giờ hiển thị trong chat: múi Việt Nam, 24h (vd: 18:52).
     */
    public static function format(?CarbonInterface $at): string
    {
        if ($at === null) {
            return '';
        }

        return $at->copy()->timezone('Asia/Ho_Chi_Minh')->format('H:i');
    }
}
