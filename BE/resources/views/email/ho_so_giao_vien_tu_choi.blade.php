<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông báo hồ sơ Giáo viên - EchoKids</title>
</head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:40px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
                <!-- Header -->
                <tr>
                    <td style="background:linear-gradient(135deg,#f97316 0%,#ef4444 100%);padding:36px 40px;text-align:center;">
                        <div style="font-size:36px;margin-bottom:10px;">📋</div>
                        <h1 style="margin:0;color:#ffffff;font-size:24px;font-weight:700;letter-spacing:0.5px;">Thông báo hồ sơ</h1>
                        <p style="margin:8px 0 0;color:rgba(255,255,255,0.85);font-size:15px;">Đăng ký Giáo viên tại EchoKids</p>
                    </td>
                </tr>
                <!-- Body -->
                <tr>
                    <td style="padding:36px 40px;">
                        <p style="margin:0 0 16px;font-size:16px;color:#334155;">Xin chào <strong>{{ $ho_ten }}</strong>,</p>
                        <p style="margin:0 0 20px;font-size:15px;color:#475569;line-height:1.7;">
                            Cảm ơn bạn đã quan tâm và gửi hồ sơ đăng ký trở thành <strong>Giáo viên</strong> trên nền tảng <strong>EchoKids</strong>.
                        </p>
                        <p style="margin:0 0 20px;font-size:15px;color:#475569;line-height:1.7;">
                            Sau khi xem xét, chúng tôi rất tiếc phải thông báo rằng hồ sơ của bạn <strong style="color:#ef4444;">chưa đáp ứng đủ yêu cầu</strong> lần này.
                        </p>
                        <!-- Reason box -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#fff7ed;border-radius:12px;border-left:4px solid #f97316;margin:0 0 24px;">
                            <tr>
                                <td style="padding:18px 20px;">
                                    <p style="margin:0 0 6px;font-size:14px;color:#9a3412;font-weight:700;">⚠️ Lý do từ chối:</p>
                                    <p style="margin:0;font-size:14px;color:#c2410c;line-height:1.6;">{{ $ghi_chu }}</p>
                                </td>
                            </tr>
                        </table>
                        <p style="margin:0 0 20px;font-size:15px;color:#475569;line-height:1.7;">
                            Bạn có thể bổ sung thêm thông tin và <strong>nộp lại hồ sơ</strong> để được xem xét vào lần tiếp theo. Chúng tôi luôn chào đón những giáo viên có tâm huyết!
                        </p>
                        <div style="text-align:center;margin:28px 0;">
                            <a href="http://localhost:5173/dang-ky-giao-vien"
                               style="display:inline-block;background:linear-gradient(135deg,#f97316,#ef4444);color:#fff;text-decoration:none;padding:14px 36px;border-radius:50px;font-size:15px;font-weight:700;letter-spacing:0.5px;box-shadow:0 4px 15px rgba(239,68,68,0.35);">
                                Nộp lại hồ sơ
                            </a>
                        </div>
                    </td>
                </tr>
                <!-- Footer -->
                <tr>
                    <td style="background:#f8fafc;padding:20px 40px;text-align:center;border-top:1px solid #e2e8f0;">
                        <p style="margin:0;font-size:13px;color:#94a3b8;">Trân trọng, <strong style="color:#f97316;">Đội ngũ EchoKids</strong></p>
                        <p style="margin:6px 0 0;font-size:12px;color:#cbd5e1;">Email này được gửi tự động, vui lòng không trả lời.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
