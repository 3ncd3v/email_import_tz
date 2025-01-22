<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Product;
use Spatie\SimpleExcel\SimpleExcelReader;

class ProductsImportJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;
    protected string $filePath;

    protected const BATCH_SIZE = 500;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function handle()
    {
        $rows = SimpleExcelReader::create($this->filePath)
            ->useDelimiter(';')
            ->getRows();

        $this->insertRowsInBatches($rows);
    }

    protected function insertRowsInBatches(iterable $rows): void
    {
        $batch = [];

        foreach ($rows as $row) {
            $batch[] = $this->prepareRowData($row);

            if (count($batch) >= self::BATCH_SIZE) {
                $this->insertBatch($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            $this->insertBatch($batch);
        }
    }

    protected function prepareRowData(array $row): array
    {
        return [
            'article' => $row['article'],
            'brand' => $row['brand'],
            'description' => $row['description'],
            'quantity' => $row['quantity'],
            'minimal_quantity' => $row['minimal_quantity'],
            'currency' => $row['currency'],
            'price' => $row['price'],
            'akey' => preg_replace('/[^A-Za-z0-9]/', '', strtoupper($row['article'])),
            'bkey' => preg_replace('/[^A-Za-z0-9]/', '', strtoupper($row['brand'])),
            'created_at' => now(),
        ];
    }

    protected function insertBatch(array $batch): void
    {
        Product::insert($batch);
    }
}
