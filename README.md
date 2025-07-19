# RMS Helper Package for Laravel

A powerful utility package for Laravel applications, providing helper functions for Persian date handling, web services (REST and SOAP), Eloquent model scopes, and Excel import/export.

## Installation

1. Install via Composer:
   ```bash
   composer require rmscms/helper
   ```

2. Add the service provider to `config/app.php` (optional, auto-discovered in Laravel):
   ```php
   'providers' => [
       RMS\Helper\HelperServiceProvider::class,
   ],
   ```

3. Publish the configuration file:
   ```bash
   php artisan vendor:publish --provider="RMS\Helper\HelperServiceProvider" --tag="config"
   ```

## Configuration

The package includes a configuration file at `config/helpers.php`:
```php
return [
    'currency' => 'تومان',
];
```

## Features

### 1. Date Helper Functions
Handle Persian (Jalali) and Gregorian dates with ease.

- **`persian_date($date, $format = 'Y/m/d H:i:s')`**: Format a date as Persian.
  ```php
  use Carbon\Carbon;
  echo \RMS\Helper\persian_date(Carbon::create(2025, 7, 20)); // 1404/04/29
  ```

- **`gregorian_date($date, $separator = '/')`**: Convert Persian date to Gregorian.
  ```php
  echo \RMS\Helper\gregorian_date('1404/04/29'); // 2025/07/20
  ```

- **`persian_to_timestamp($date, $separator = '/')`**: Convert Persian date to Unix timestamp.
  ```php
  echo \RMS\Helper\persian_to_timestamp('1404/04/29'); // 1752969600
  ```

- **`is_valid_persian_date($date, $separator = '/')`**: Validate Persian date.
  ```php
  var_dump(\RMS\Helper\is_valid_persian_date('1404/04/29')); // true
  ```

- **`persian_date_diff($startDate, $endDate, $separator = '/')`**: Calculate days between two Persian dates.
  ```php
  echo \RMS\Helper\persian_date_diff('1404/04/29', '1404/04/30'); // 1
  ```

- **`persian_now($format = 'Y/m/d H:i:s')`**: Get current Persian date.
  ```php
  echo \RMS\Helper\persian_now(); // e.g., 1404/04/29 14:00:00
  ```

### 2. Number and Currency Helpers
Format numbers and convert Persian/Arabic digits.

- **`displayAmount($amount, $sign = null)`**: Format amount with currency.
  ```php
  echo \RMS\Helper\displayAmount(1000); // 1,000 تومان
  echo \RMS\Helper\displayAmount(1000, 'ریال'); // 1,000 ریال
  ```

- **`changeNumberToEn($string)`**: Convert Persian/Arabic numbers to English.
  ```php
  echo \RMS\Helper\changeNumberToEn('۱۲۳۴۵۶'); // 123456
  ```

### 3. Eloquent Model Scopes
Enhance Eloquent models with useful scopes via `ModelTrait`.

```php
use Illuminate\Database\Eloquent\Model;
use RMS\Helper\Eloquent\ModelTrait;

class TestModel extends Model
{
    use ModelTrait;
    protected $table = 'test_table';
}
```

- **`active()`**: Filter active records.
  ```php
  TestModel::active()->get();
  ```

- **`countAndSum($column = 'amount')`**: Get count and sum of a column.
  ```php
  TestModel::countAndSum()->first(); // ['total_count' => X, 'total_sum' => Y]
  ```

- **`today()`**: Filter records created today.
  ```php
  TestModel::today()->get();
  ```

- **`yesterday()`**: Filter records created yesterday.
  ```php
  TestModel::yesterday()->get();
  ```

- **`whereLike($column, $value)`**: Search column with LIKE.
  ```php
  TestModel::whereLike('name', 'test')->get();
  ```

- **`orderByLatest()`**: Order by latest `created_at`.
  ```php
  TestModel::orderByLatest()->get();
  ```

- **`whereInDateRange($start, $end, $column = 'created_at')`**: Filter records in date range.
  ```php
  TestModel::whereInDateRange('2025-07-01', '2025-07-20')->get();
  ```

- **`withTrashed()`**: Include soft-deleted records.
  ```php
  TestModel::withTrashed()->get();
  ```

- **`whereStatus($status, $column = 'status')`**: Filter by status.
  ```php
  TestModel::whereStatus('pending')->get();
  ```

### 4. Web Services
Handle REST and SOAP web services.

#### REST Web Service
```php
use RMS\Helper\WebServices\Rest;

class SampleRestService extends Rest
{
    protected function url(string $uri = ''): string { return 'https://api.example.com/' . $uri; }
    protected function requestMethod(): string { return 'GET'; }
}

$service = new SampleRestService();
$result = $service->withParameters(['key' => 'value'])->send();
```

#### SOAP Web Service
```php
use RMS\Helper\WebServices\Soap;

class SampleSoapService extends Soap
{
    protected function url(): string { return 'https://www.w3schools.com/xml/tempconvert.asmx?WSDL'; }
}

$service = new SampleSoapService();
$result = $service->call('CelsiusToFahrenheit', ['Celsius' => '25']);
```

### 5. Excel Import/Export
Simplify Excel operations with `ExcelHelper`.

```php
use RMS\Helper\Excel\ExcelHelper;
use App\Models\TestModel;

// Export
ExcelHelper::export(TestModel::query(), 'test_export', ['id', 'name']);

// Import
ExcelHelper::import(request()->file('file'), TestModel::class, ['name', 'value']);
```

## Testing
Run tests to ensure functionality:

```bash
cd packages/rms/helper
php vendor/bin/phpunit
```

## Requirements
- PHP ^8.1
- Laravel ^12.0
- `guzzlehttp/guzzle` ^7.0
- `morilog/jalali` ^3.0
- `maatwebsite/excel` ^3.1

## License
MIT License. See [LICENSE](LICENSE) for details.