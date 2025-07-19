<?php

namespace RMS\Helper;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class ExcelHelper
{
    /**
     * Export Eloquent query to Excel
     *
     * @param Builder $query
     * @param string $filename
     * @param array $headings
     * @param string $format
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public static function export(Builder $query, string $filename, array $headings = [], string $format = 'xlsx'): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $exportClass = new class($query, $headings) implements FromQuery, WithHeadings{
            protected Builder $query;
            protected array $headings;

            public function __construct(Builder $query, array $headings)
            {
                $this->query = $query;
                $this->headings = $headings;
            }

            public function query(): Builder
            {
                return $this->query;
            }

            public function headings(): array
            {
                return $this->headings;
            }
        };

        return Excel::download($exportClass, $filename . '.' . $format, \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * Import data from Excel to Eloquent model
     *
     * @param UploadedFile $file
     * @param string $modelClass
     * @param array $columns
     * @return void
     * @throws InvalidArgumentException
     */
    public static function import(UploadedFile $file, string $modelClass, array $columns): void
    {
        if (!class_exists($modelClass) || !is_subclass_of($modelClass, \Illuminate\Database\Eloquent\Model::class)) {
            throw new InvalidArgumentException('Invalid model class');
        }

        $importClass = new class($columns, $modelClass) implements ToModel{
            protected array $columns;
            protected string $modelClass;

            public function __construct(array $columns, string $modelClass)
            {
                $this->columns = $columns;
                $this->modelClass = $modelClass;
            }

            public function model(array $row)
            {
                $data = [];
                foreach ($this->columns as $index => $column) {
                    $data[$column] = $row[$index] ?? null;
                }
                return new ($this->modelClass)($data);
            }
        };

        Excel::import($importClass, $file);
    }
}
