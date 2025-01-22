<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProductsImportJob;
use PHPUnit\Framework\Attributes\Test;

class ProductsImportJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    #[Test]
    public function it_imports_products_from_csv_file()
    {
        $csvContent = <<<CSV
        article;brand;description;quantity;minimal_quantity;currency;price
        ART001;BrandA;DescriptionA;10;5;USD;100.50
        ART002;BrandB;DescriptionB;20;10;EUR;200.75
        CSV;

        $filePath = storage_path('app/products_test.csv');
        file_put_contents($filePath, $csvContent);


        $job = new ProductsImportJob($filePath);


        $job->handle();

        $this->assertDatabaseCount('products', 2);

        $this->assertDatabaseHas('products', [
            'article' => 'ART001',
            'brand' => 'BrandA',
            'description' => 'DescriptionA',
            'quantity' => 10,
            'minimal_quantity' => 5,
            'currency' => 'USD',
            'price' => 100.50,
            'akey' => 'ART001',
            'bkey' => 'BRANDA',
        ]);

        $this->assertDatabaseHas('products', [
            'article' => 'ART002',
            'brand' => 'BrandB',
            'description' => 'DescriptionB',
            'quantity' => 20,
            'minimal_quantity' => 10,
            'currency' => 'EUR',
            'price' => 200.75,
            'akey' => 'ART002',
            'bkey' => 'BRANDB',
        ]);
    }

    #[Test]
    public function it_handles_large_files_in_batches()
    {
        $csvContent = "article;brand;description;quantity;minimal_quantity;currency;price\n";
        for ($i = 1; $i <= 600; $i++) {
            $csvContent .= "ART{$i};Brand{$i};Description{$i};{$i};{$i};USD;{$i}.00\n";
        }

        $filePath = storage_path('app/products_large_test.csv');
        file_put_contents($filePath, $csvContent);

        $job = new ProductsImportJob($filePath);

        $job->handle();

        $this->assertDatabaseCount('products', 600);

        $this->assertDatabaseHas('products', [
            'article' => 'ART1',
            'brand' => 'Brand1',
            'description' => 'Description1',
            'quantity' => 1,
            'minimal_quantity' => 1,
            'currency' => 'USD',
            'price' => 1.00,
            'akey' => 'ART1',
            'bkey' => 'BRAND1',
        ]);

        $this->assertDatabaseHas('products', [
            'article' => 'ART600',
            'brand' => 'Brand600',
            'description' => 'Description600',
            'quantity' => 600,
            'minimal_quantity' => 600,
            'currency' => 'USD',
            'price' => 600.00,
            'akey' => 'ART600',
            'bkey' => 'BRAND600',
        ]);
    }
}
