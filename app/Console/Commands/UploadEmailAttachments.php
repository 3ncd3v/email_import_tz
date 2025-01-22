<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailService;
use App\Services\ZipService;
use App\Services\ImportProductsService;
use Symfony\Component\Process\Process;

class UploadEmailAttachments extends Command
{
    protected $signature = 'email:upload-attachments';
    protected $description = 'Поиск писем с вложенными файлами для импорта в БД.';

    private $uploadPath;
    private $extractPath;

    public function __construct(
        private EmailService $emailService,
        private ZipService $zipService,
        private ImportProductsService $importService
    ) {
        parent::__construct();

        $this->uploadPath = storage_path(config('mailhog.upload_path'));
        $this->extractPath = storage_path(config('mailhog.extract_path'));
    }

    public function handle()
    {
        $messages = $this->emailService->getMessages();

        if ($messages['messages_count'] > 0) {
            foreach ($messages['messages'] as $message) {
                $messageId = $message['ID'];
                $messageDetails = $this->emailService->getMessageDetails($messageId);

                if (!empty($messageDetails['Attachments'])) {
                    $this->extractAttachmentsToDb($messageId, $messageDetails['Attachments']);
                }
            }
        } else {
            $this->info('Письма отсутствуют.');
        }
    }

    protected function extractAttachmentsToDb(string $messageId, array $attachments): void
    {
        foreach ($attachments as $attachment) {
            $fileName = generateUniqueFileName($attachment['FileName']);
            $filePath = $this->uploadPath . $fileName;

            $this->emailService->downloadAttachment($messageId, $attachment['PartID'], $filePath);

            if (pathinfo($fileName, PATHINFO_EXTENSION) === 'zip') {
                $this->info("Распаковка архива: $filePath");
                $extractedFile = $this->zipService->extract($filePath, $this->extractPath);

                if ($extractedFile) {
                    $this->importDataFromFile($extractedFile);
                } else {
                    $this->error('Возникла ошибка при распаковке архива.');
                }
            }
        }
    }

    protected function importDataFromFile(string $filePath): void
    {
        try {
            $message = $this->importService->importData($filePath);
            $this->info("Импорт данных из: $filePath");
            $this->info($message);

            $this->runWorker();
            $this->info("Файл успешно импортирован: $filePath");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    protected function runWorker(): void
    {
        $process = new Process(['php', 'artisan', 'queue:work', '--stop-when-empty']);
        $process->setTimeout(null);
        $process->start();
        $process->wait();
    }
}
