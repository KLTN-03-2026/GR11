<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HoSoGiaoVienTuChoiMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $ho_ten;
    public string $ghi_chu;

    public function __construct(string $ho_ten, string $ghi_chu)
    {
        $this->ho_ten  = $ho_ten;
        $this->ghi_chu = $ghi_chu;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông báo về hồ sơ đăng ký Giáo viên - EchoKids',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.ho_so_giao_vien_tu_choi',
        );
    }
}
