<?php

namespace BenBjurstrom\Otpz\Mail;

use BenBjurstrom\Otpz\Models\Otp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpzMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(protected Otp $otp, protected string $code)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name'). ' Verification Code',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $email = $this->otp->user->email;

        // Format the code with hyphens for readability
        $middleHyphen = config('otpz.middle_hyphen', true);
        if ($middleHyphen){
            $codeLength = config('otpz.code_length', 10);
            $formattedCode = substr_replace($this->code, '-', $codeLength / 2, 0);
        } else {
            $formattedCode = $this->code;
        }

        $template = config('otpz.template', 'otpz::mail.otpz');

        return new Content(
            markdown: $template,
            with: [
                'email' => $email,
                'code' => $formattedCode,
            ],
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
