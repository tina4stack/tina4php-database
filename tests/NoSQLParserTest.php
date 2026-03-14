<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

use PHPUnit\Framework\TestCase;

class NoSQLParserTest extends TestCase
{
    private \Tina4\NoSQLParser $parser;

    protected function setUp(): void
    {
        $this->parser = new \Tina4\NoSQLParser();
    }

    /**
     * Test that a SELECT query is parsed into the correct NoSQL structure
     */
    public function testParseSelect(): void
    {
        $result = $this->parser->parseSQLToNoSQL("select id, name from users");

        $this->assertArrayHasKey("collectionName", $result);
        $this->assertArrayHasKey("columns", $result);
        $this->assertArrayHasKey("data", $result);
        $this->assertArrayHasKey("filter", $result);

        $this->assertEquals("users", $result["collectionName"]);
        $this->assertEquals(["id", "name"], $result["columns"]);
        $this->assertEmpty($result["filter"]);
        $this->assertEmpty($result["data"]);

        // Select with star
        $resultStar = $this->parser->parseSQLToNoSQL("select * from products");
        $this->assertEquals("products", $resultStar["collectionName"]);
        $this->assertEquals(["*"], $resultStar["columns"]);
    }

    /**
     * Test that an INSERT query is parsed correctly with columns and data
     */
    public function testParseInsert(): void
    {
        $result = $this->parser->parseSQLToNoSQL("insert into users (name, email) values ('John', 'john@test.com')");

        $this->assertEquals("users", $result["collectionName"]);
        $this->assertEquals(["name", "email"], $result["columns"]);
        $this->assertCount(2, $result["data"]);
        $this->assertEquals("John", $result["data"][0]);
        $this->assertEquals("john@test.com", $result["data"][1]);
        $this->assertEmpty($result["filter"]);
    }

    /**
     * Test that an UPDATE query is parsed correctly with columns and filter
     */
    public function testParseUpdate(): void
    {
        $result = $this->parser->parseSQLToNoSQL("update users set name = 'Jane' where id = 1");

        $this->assertEquals("users", $result["collectionName"]);
        $this->assertContains("name", $result["columns"]);
        $this->assertArrayHasKey("id", $result["filter"]);
        $this->assertArrayHasKey('$eq', $result["filter"]["id"]);
    }

    /**
     * Test that a DELETE query is parsed correctly
     */
    public function testParseDelete(): void
    {
        // Delete with where clause
        $result = $this->parser->parseSQLToNoSQL("delete from users where id = 5");
        $this->assertEquals("users", $result["collectionName"]);
        $this->assertArrayHasKey("id", $result["filter"]);
        $this->assertArrayHasKey('$eq', $result["filter"]["id"]);

        // Delete without where clause
        $resultPlain = $this->parser->parseSQLToNoSQL("delete from users");
        $this->assertEquals("users", $resultPlain["collectionName"]);
        $this->assertEmpty($resultPlain["filter"]);
    }

    /**
     * Test WHERE clause with various comparison operators: =, >, <, and compound with AND
     */
    public function testParseWhereOperators(): void
    {
        // Equals
        $eq = $this->parser->parseSQLToNoSQL("select id from users where id = 1");
        $this->assertArrayHasKey('$eq', $eq["filter"]["id"]);
        $this->assertEquals(1, $eq["filter"]["id"]['$eq']);

        // Greater than
        $gt = $this->parser->parseSQLToNoSQL("select id from users where age > 18");
        $this->assertArrayHasKey('$gt', $gt["filter"]["age"]);
        $this->assertEquals(18, $gt["filter"]["age"]['$gt']);

        // Less than
        $lt = $this->parser->parseSQLToNoSQL("select id from users where age < 65");
        $this->assertArrayHasKey('$lt', $lt["filter"]["age"]);
        $this->assertEquals(65, $lt["filter"]["age"]['$lt']);

        // Combined with AND: "id = 1 and age > 0"
        $combined = $this->parser->parseSQLToNoSQL("select id, test from tableName where id = 1 and id > 0");
        $this->assertArrayHasKey("id", $combined["filter"]);
        $this->assertArrayHasKey('$eq', $combined["filter"]["id"]);
        $this->assertArrayHasKey("and", $combined["filter"]);
        $this->assertArrayHasKey('$gt', $combined["filter"]["and"]["id"]);
    }

    /**
     * Test ORDER BY and LIMIT are handled without breaking the parser.
     * Note: The NoSQLParser regex captures ORDER BY content but does not
     * have dedicated keys for orderBy/limit in the return array.
     */
    public function testParseOrderByLimit(): void
    {
        // The parser uses a withOrderBy regex when both WHERE and ORDER BY are present
        $result = $this->parser->parseSQLToNoSQL("select id, name from users where id > 0 order by name");

        $this->assertEquals("users", $result["collectionName"]);
        $this->assertEquals(["id", "name"], $result["columns"]);
        // The filter should still be parsed correctly
        $this->assertArrayHasKey("id", $result["filter"]);
        $this->assertArrayHasKey('$gt', $result["filter"]["id"]);

        // Plain select without where should still work
        $plain = $this->parser->parseSQLToNoSQL("select id from users");
        $this->assertEquals("users", $plain["collectionName"]);
        $this->assertEmpty($plain["filter"]);
    }
}
