<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuenMatKhauMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ho_ten;
    public $otp;

    // Truyền tên và mã OTP từ Controller vào đây
    public function __construct($ho_ten, $otp)
    {
        $this->ho_ten = $ho_ten;
        $this->otp = $otp;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mã xác nhận khôi phục mật khẩu - EchoKids', // Tiêu đề email
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.quen_mat_khau',
        );
    }
}
