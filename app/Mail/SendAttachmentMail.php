<?php

namespace App\Mail;

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendAttachmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $filePath;

    public function __construct($subject, $filePath)
    {
        $this->subject = $subject;
        $this->filePath = $filePath;
    }

    public function build()
    {
        return $this->subject($this->subject)
            ->view('emails.attachment')
            ->attach($this->filePath, [
                'as' => basename($this->filePath),
                'mime' => mime_content_type($this->filePath),
            ]);
    }
}
