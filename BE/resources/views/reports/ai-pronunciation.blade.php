<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        h2 { font-size: 14px; margin-top: 20px; margin-bottom: 8px; }
        .muted { color: #64748b; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; }
        th { background: #f8fafc; }
        .chart-wrap { margin-top: 12px; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p class="muted">{{ $period }}</p>

    @php
        $dist = $payload['error_distribution'] ?? [];
        $amDau = (int) ($dist['am_dau']['count'] ?? 0);
        $van = (int) ($dist['van']['count'] ?? 0);
        $thanhDieu = (int) ($dist['thanh_dieu']['count'] ?? 0);
        $maxBar = max(1, $amDau, $van, $thanhDieu);
        $barH = 120;
        $scale = $barH / $maxBar;
    @endphp

    <h2>Phân bố lỗi phát âm</h2>
    <table>
        <thead>
            <tr>
                <th>Loại lỗi</th>
                <th>Số lần</th>
                <th>Tỷ lệ</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Âm đầu</td>
                <td>{{ $amDau }}</td>
                <td>{{ $dist['am_dau']['percent'] ?? 0 }}%</td>
            </tr>
            <tr>
                <td>Vần</td>
                <td>{{ $van }}</td>
                <td>{{ $dist['van']['percent'] ?? 0 }}%</td>
            </tr>
            <tr>
                <td>Thanh điệu</td>
                <td>{{ $thanhDieu }}</td>
                <td>{{ $dist['thanh_dieu']['percent'] ?? 0 }}%</td>
            </tr>
        </tbody>
    </table>

    <div class="chart-wrap">
        <svg width="360" height="160" xmlns="http://www.w3.org/2000/svg">
            <rect x="40" y="{{ $barH - (int) ($amDau * $scale) + 20 }}" width="60" height="{{ (int) ($amDau * $scale) }}" fill="#3b82f6"/>
            <text x="55" y="{{ $barH + 35 }}" font-size="10">Âm đầu</text>
            <rect x="140" y="{{ $barH - (int) ($van * $scale) + 20 }}" width="60" height="{{ (int) ($van * $scale) }}" fill="#22c55e"/>
            <text x="160" y="{{ $barH + 35 }}" font-size="10">Vần</text>
            <rect x="240" y="{{ $barH - (int) ($thanhDieu * $scale) + 20 }}" width="60" height="{{ (int) ($thanhDieu * $scale) }}" fill="#f59e0b"/>
            <text x="248" y="{{ $barH + 35 }}" font-size="10">Thanh điệu</text>
        </svg>
    </div>

    @if (!empty($payload['top_words']))
        <h2>Top từ mắc lỗi nhiều nhất</h2>
        <table>
            <thead>
                <tr>
                    <th>Từ</th>
                    <th>Loại lỗi</th>
                    <th>Số lần</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payload['top_words'] as $row)
                    <tr>
                        <td>{{ $row['word'] ?? '' }}</td>
                        <td>{{ $row['error_type'] ?? '' }}</td>
                        <td>{{ $row['count'] ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if (!empty($payload['weekly_trend']))
        <h2>Xu hướng theo tuần</h2>
        <table>
            <thead>
                <tr>
                    <th>Tuần bắt đầu</th>
                    <th>Âm đầu</th>
                    <th>Vần</th>
                    <th>Thanh điệu</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payload['weekly_trend'] as $week)
                    <tr>
                        <td>{{ $week['week'] ?? '' }}</td>
                        <td>{{ $week['am_dau'] ?? 0 }}</td>
                        <td>{{ $week['van'] ?? 0 }}</td>
                        <td>{{ $week['thanh_dieu'] ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
