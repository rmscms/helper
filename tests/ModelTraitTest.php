<?php

namespace RMS\Helper\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Database\Eloquent\Model;
use RMS\Helper\Eloquent\ModelTrait;
use Illuminate\Support\Carbon;

class ModelTraitTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [\RMS\Helper\HelperServiceProvider::class];
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

    public function testActiveScope()
    {
        $model = new class extends Model {
            use ModelTrait;
            protected $table = 'test_table';
        };

        $query = $model->active();
        $this->assertEquals('select * from "test_table" where "active" = ?', $query->toSql());
        $this->assertEquals([1], $query->getBindings());
    }

    public function testCountAndSumScope()
    {
        $model = new class extends Model {
            use ModelTrait;
            protected $table = 'test_table';
        };

        $query = $model->countAndSum('amount');
        $this->assertEquals('select count(*) as total_count, sum(amount) as total_sum from "test_table"', $query->toSql());
    }

    public function testTodayScope()
    {
        $model = new class extends Model {
            use ModelTrait;
            protected $table = 'test_table';
        };

        $query = $model->today();
        $this->assertStringContainsString('where "created_at" >= ?', $query->toSql());
        $this->assertEquals([Carbon::today()], $query->getBindings());
    }

    public function testYesterdayScope()
    {
        $model = new class extends Model {
            use ModelTrait;
            protected $table = 'test_table';
        };

        $query = $model->yesterday();
        $this->assertStringContainsString('where "created_at" >= ? and "created_at" < ?', $query->toSql());
        $this->assertEquals([Carbon::today()->subDay(), Carbon::today()], $query->getBindings());
    }

    public function testWhereLikeScope()
    {
        $model = new class extends Model {
            use ModelTrait;
            protected $table = 'test_table';
        };

        $query = $model->whereLike('name', 'test');
        $this->assertEquals('select * from "test_table" where "name" LIKE ?', $query->toSql());
        $this->assertEquals(['%test%'], $query->getBindings());
    }

    public function testOrderByLatestScope()
    {
        $model = new class extends Model {
            use ModelTrait;
            protected $table = 'test_table';
        };

        $query = $model->orderByLatest();
        $this->assertEquals('select * from "test_table" order by "created_at" desc', $query->toSql());
    }

    public function testWhereInDateRangeScope()
    {
        $model = new class extends Model {
            use ModelTrait;
            protected $table = 'test_table';
        };

        $start = '2025-07-01';
        $end = '2025-07-19';
        $query = $model->whereInDateRange($start, $end);
        $this->assertStringContainsString('where "created_at" between ? and ?', $query->toSql());
        $this->assertEquals([Carbon::parse($start), Carbon::parse($end)], $query->getBindings());
    }

    public function testWithTrashedScope()
    {
        $model = new class extends Model {
            use ModelTrait;
            use \Illuminate\Database\Eloquent\SoftDeletes;
            protected $table = 'test_table';
        };

        $query = $model->withTrashed();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $query);
    }

    public function testWhereStatusScope()
    {
        $model = new class extends Model {
            use ModelTrait;
            protected $table = 'test_table';
        };

        $query = $model->whereStatus('pending');
        $this->assertEquals('select * from "test_table" where "status" = ?', $query->toSql());
        $this->assertEquals(['pending'], $query->getBindings());
    }
}
