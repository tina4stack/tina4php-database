# tina4php-database

Core database abstraction module for the Tina4 PHP framework, providing the `DataBase` interface, `DataBaseCore` trait, `DataResult`, `DataRecord`, `DataError`, and the `NoSQLParser` for MongoDB support.

## Installation

```bash
composer require tina4stack/tina4php-database
```

## Requirements

- PHP >= 8.1
- ext-json
- tina4stack/tina4php-debug ^2.0

## Usage

### Implementing a Custom Database Driver

```php
use Tina4\DataBase;
use Tina4\DataBaseCore;

class DataMyDb implements DataBase
{
    use DataBaseCore;

    public function open() { /* open connection */ }
    public function close() { /* close connection */ }
    public function exec() { /* execute statement */ }
    // ... implement remaining interface methods
}
```

### Running a Query

```php
$db = new DataMyDb("localhost/3306:mydb", "user", "password");
$result = $db->fetch("SELECT * FROM users WHERE active = 1");

foreach ($result->records() as $record) {
    echo $record->name;
}
```

### Using the NoSQL Parser

```php
use Tina4\NoSQLParser;

$parser = new NoSQLParser();
$parsed = $parser->parseSQLToNoSQL("select id, name from users where id = 1");
// Returns: ["collectionName" => "users", "columns" => ["id", "name"], "filter" => ["id" => ['$eq' => '1']]]
```

## Testing

```bash
composer test
```

## License

MIT - see [LICENSE](LICENSE)

---

## Our Sponsors

**Sponsored with 🩵 by Code Infinity**

[<img src="https://codeinfinity.co.za/wp-content/uploads/2025/09/c8e-logo-github.png" alt="Code Infinity" width="100">](https://codeinfinity.co.za/about-open-source-policy?utm_source=github&utm_medium=website&utm_campaign=opensource_campaign&utm_id=opensource)

*Supporting open source communities <span style="color: #1DC7DE;">•</span> Innovate <span style="color: #1DC7DE;">•</span> Code <span style="color: #1DC7DE;">•</span> Empower*
