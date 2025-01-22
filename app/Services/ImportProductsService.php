<?php

namespace App\Services;

use App\Jobs\ProductsImportJob;

class ImportProductsService
{
    public function importData(string $filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        if (in_array($extension, ['xlsx', 'xls', 'csv'])) {
            dispatch(new ProductsImportJob($filePath));
            return 'Импорт начат. Данные будут обработаны в фоновом режиме.';
        } else {
            throw new \Exception("Неправильный тип данных: {$extension}");
        }
    }
}
