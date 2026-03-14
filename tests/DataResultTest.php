<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

use PHPUnit\Framework\TestCase;

class DataResultTest extends TestCase
{
    /**
     * Test that the DataResult constructor correctly sets all properties
     */
    public function testConstruction(): void
    {
        $error = new \Tina4\DataError("", "");
        $fields = [
            (object)["name" => "id", "type" => "integer"],
            (object)["name" => "username", "type" => "varchar"],
        ];

        $record1 = new \Tina4\DataRecord(["id" => 1, "username" => "alice"]);
        $record2 = new \Tina4\DataRecord(["id" => 2, "username" => "bob"]);
        $records = [$record1, $record2];

        $result = new \Tina4\DataResult($records, $fields, 10, 0, $error);

        // Check properties
        $this->assertEquals(10, $result->noOfRecords);
        $this->assertEquals(10, $result->getNoOfRecords());
        $this->assertEquals(0, $result->offSet);
        $this->assertEquals(2, $result->recordCount);
        $this->assertEquals(10, $result->recordsFiltered);
        $this->assertSame($records, $result->records);
        $this->assertSame($fields, $result->fields);
        $this->assertSame($fields, $result->fields());
        $this->assertSame($error, $result->error);

        // Test with null error
        $resultNoError = new \Tina4\DataResult([], [], 0);
        $this->assertEquals(0, $resultNoError->noOfRecords);
        $this->assertEquals(0, $resultNoError->recordCount);
        $this->assertNull($resultNoError->error);
        $this->assertNull($resultNoError->getError());

        // Test record() accessor
        $this->assertNotNull($result->record(0));
        $this->assertNotNull($result->record(1));

        // Test with empty records
        $emptyResult = new \Tina4\DataResult(null, [], 0, 0, $error);
        $this->assertEquals(0, $emptyResult->recordCount);
        $this->assertNull($emptyResult->record(0));
    }

    /**
     * Test that data access methods (asArray, asObject) work correctly
     */
    public function testAsArrayOrObject(): void
    {
        $error = new \Tina4\DataError("", "");
        $fields = [
            (object)["name" => "id", "type" => "integer"],
            (object)["name" => "user_name", "type" => "varchar"],
        ];

        $record1 = new \Tina4\DataRecord(["id" => 1, "user_name" => "alice"]);
        $record2 = new \Tina4\DataRecord(["id" => 2, "user_name" => "bob"]);
        $records = [$record1, $record2];

        $result = new \Tina4\DataResult($records, $fields, 2, 0, $error);

        // asObject returns array of objects via records()
        $objects = $result->asObject();
        $this->assertIsArray($objects);
        $this->assertCount(2, $objects);
        // Each object should have the original and camelCase field names
        $this->assertEquals(1, $objects[0]->id);

        // asArray returns array of associative arrays
        $arrays = $result->asArray();
        $this->assertIsArray($arrays);
        $this->assertCount(2, $arrays);
        $this->assertIsArray($arrays[0]);
        $this->assertEquals(1, $arrays[0]["id"]);

        // asOriginal returns records with original field names
        $originals = $result->asOriginal();
        $this->assertIsArray($originals);
        $this->assertCount(2, $originals);
        $this->assertEquals("alice", $originals[0]->user_name);

        // Empty result
        $emptyResult = new \Tina4\DataResult([], [], 0, 0, $error);
        $this->assertEmpty($emptyResult->asArray());
        $this->assertEmpty($emptyResult->asObject());

        // jsonSerialize returns object with expected keys
        $json = $result->jsonSerialize();
        $this->assertObjectHasProperty("recordsTotal", $json);
        $this->assertObjectHasProperty("recordsFiltered", $json);
        $this->assertObjectHasProperty("recordCount", $json);
        $this->assertObjectHasProperty("data", $json);
        $this->assertEquals(2, $json->recordsTotal);
        $this->assertCount(2, $json->data);
    }
}
