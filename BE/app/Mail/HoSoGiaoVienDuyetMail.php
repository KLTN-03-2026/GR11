<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HoSoGiaoVienDuyetMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $ho_ten;

    public function __construct(string $ho_ten)
    {
        $this->ho_ten = $ho_ten;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Hồ sơ đăng ký Giáo viên của bạn đã được duyệt - EchoKids',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.ho_so_giao_vien_duyet',
        );
    }
}
