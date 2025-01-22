<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\SendAttachmentMail;
use Illuminate\Support\Facades\Mail;

class SendEmail extends Command
{
    protected $signature = 'email:send-message';
    protected $description = 'Отправка письма с вложенным файлом.';

    public function handle()
    {
        $filePath = storage_path('app/test_products_100k.zip');

        Mail::to('test@example.com')->send(new SendAttachmentMail(
            'Письмо с вложением',
            $filePath
        ));
    }
}
