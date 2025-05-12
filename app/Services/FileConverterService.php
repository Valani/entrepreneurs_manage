<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class FileConverterService
{
    /**
     * Convert Excel file to CSV
     *
     * @param UploadedFile $file
     * @return string Path to the generated CSV file
     */
    public function convertToCSV(UploadedFile $file): string
    {
        // Load the Excel file
        $spreadsheet = IOFactory::load($file->getPathname());
        
        // Get the first worksheet
        $worksheet = $spreadsheet->getActiveSheet();
        
        // Create a temporary file for CSV
        $tempFile = tempnam(sys_get_temp_dir(), 'csv_');
        
        // Create CSV writer
        $writer = IOFactory::createWriter($spreadsheet, 'Csv');
        $writer->setDelimiter(',');
        $writer->setEnclosure('"');
        $writer->setLineEnding("\r\n");
        $writer->save($tempFile);
        
        return $tempFile;
    }

    /**
     * Clean up temporary files
     *
     * @param string $filePath
     * @return void
     */
    public function cleanup(string $filePath): void
    {
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}