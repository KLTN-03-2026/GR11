<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ sơ Giáo viên được duyệt - EchoKids</title>
</head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:40px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
                <!-- Header -->
                <tr>
                    <td style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:36px 40px;text-align:center;">
                        <div style="font-size:36px;margin-bottom:10px;">🎉</div>
                        <h1 style="margin:0;color:#ffffff;font-size:24px;font-weight:700;letter-spacing:0.5px;">Chúc mừng!</h1>
                        <p style="margin:8px 0 0;color:rgba(255,255,255,0.85);font-size:15px;">Hồ sơ Giáo viên của bạn đã được duyệt</p>
                    </td>
                </tr>
                <!-- Body -->
                <tr>
                    <td style="padding:36px 40px;">
                        <p style="margin:0 0 16px;font-size:16px;color:#334155;">Xin chào <strong>{{ $ho_ten }}</strong>,</p>
                        <p style="margin:0 0 20px;font-size:15px;color:#475569;line-height:1.7;">
                            Chúng tôi rất vui khi thông báo rằng hồ sơ đăng ký trở thành <strong>Giáo viên</strong> trên nền tảng <strong>EchoKids</strong> của bạn đã được <strong style="color:#22c55e;">xét duyệt thành công</strong>.
                        </p>
                        <!-- Notification box -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-radius:12px;border-left:4px solid #22c55e;margin:0 0 24px;">
                            <tr>
                                <td style="padding:18px 20px;">
                                    <p style="margin:0;font-size:14px;color:#166534;font-weight:600;">✅ Tài khoản của bạn đã được nâng cấp lên vai trò Giáo viên</p>
                                    <p style="margin:8px 0 0;font-size:13px;color:#15803d;line-height:1.6;">Bạn có thể đăng nhập và bắt đầu tạo bài học, quản lý học viên ngay bây giờ.</p>
                                </td>
                            </tr>
                        </table>
                        <p style="margin:0 0 24px;font-size:15px;color:#475569;line-height:1.7;">
                            Hãy đăng nhập vào hệ thống để khám phá các tính năng dành cho giáo viên và bắt đầu hành trình giảng dạy của bạn!
                        </p>
                        <div style="text-align:center;margin:28px 0;">
                            <a href="http://localhost:5173/dang-nhap"
                               style="display:inline-block;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;text-decoration:none;padding:14px 36px;border-radius:50px;font-size:15px;font-weight:700;letter-spacing:0.5px;box-shadow:0 4px 15px rgba(118,75,162,0.35);">
                                Đăng nhập ngay
                            </a>
                        </div>
                    </td>
                </tr>
                <!-- Footer -->
                <tr>
                    <td style="background:#f8fafc;padding:20px 40px;text-align:center;border-top:1px solid #e2e8f0;">
                        <p style="margin:0;font-size:13px;color:#94a3b8;">Trân trọng, <strong style="color:#667eea;">Đội ngũ EchoKids</strong></p>
                        <p style="margin:6px 0 0;font-size:12px;color:#cbd5e1;">Email này được gửi tự động, vui lòng không trả lời.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
