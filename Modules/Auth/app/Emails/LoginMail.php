<?php

namespace Modules\Auth\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginMail extends Mailable
{
    use Queueable, SerializesModels;

    private $username;

    private $code;

    public function __construct($username, $code)
    {
        $this->username = $username;
        $this->code = $code;
    }

    // public function build(): self
    // {
    //     return $this->view('view.name');
    // }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Email Verification',
        );
    }

    public function build(): self
    {
        return $this->view('mail.TwofactorEmail') // نام ویوی ایمیل را در اینجا وارد کنید
            ->with([
                'username' => $this->username,
                'code' => $this->code,
            ]);
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.TwofactorEmail',
            with: [
                'username' => $this->username,
                'code' => $this->code,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
