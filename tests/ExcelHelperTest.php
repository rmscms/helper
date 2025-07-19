<?php

namespace RMS\Helper\Tests;

use Orchestra\Testbench\TestCase;
use RMS\Helper\ExcelHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use RMS\Helper\HelperServiceProvider;

class ExcelHelperTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [HelperServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'test');
        $app['config']->set('database.connections.test', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    public function testExport()
    {
        $model = new class extends Model {
            protected $table = 'test_table';
        };

        Excel::fake();
        ExcelHelper::export($model->newQuery(), 'test_export', ['id', 'name']);
        Excel::assertDownloaded('test_export.xlsx');
    }

    public function testImport()
    {
        $modelClass = get_class(new class extends Model {
            protected $table = 'test_table';
            protected $fillable = ['name', 'value'];
        });

        Excel::fake();
        $file = UploadedFile::fake()->create('test.xlsx');
        ExcelHelper::import($file, $modelClass, ['name', 'value']);
        Excel::assertImported($file->getClientOriginalName());
    }

    public function testImportInvalidModel()
    {
        $this->expectException(\InvalidArgumentException::class);
        $file = UploadedFile::fake()->create('test.xlsx');
        ExcelHelper::import($file, 'InvalidClass', ['name']);
    }
}
