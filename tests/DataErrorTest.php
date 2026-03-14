<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

use PHPUnit\Framework\TestCase;

class DataErrorTest extends TestCase
{
    /**
     * Test that DataError stores error code and message correctly
     */
    public function testConstruction(): void
    {
        $error = new \Tina4\DataError("ERR001", "Something went wrong");

        $this->assertEquals("ERR001", $error->getErrorCode());
        $this->assertEquals("Something went wrong", $error->getErrorMessage());

        // getErrorText returns "code message"
        $this->assertEquals("ERR001 Something went wrong", $error->getErrorText());

        // getError returns associative array
        $arr = $error->getError();
        $this->assertArrayHasKey("errorCode", $arr);
        $this->assertArrayHasKey("errorMessage", $arr);
        $this->assertEquals("ERR001", $arr["errorCode"]);
        $this->assertEquals("Something went wrong", $arr["errorMessage"]);

        // __toString returns JSON
        $json = (string) $error;
        $decoded = json_decode($json, true);
        $this->assertEquals("ERR001", $decoded["errorCode"]);
        $this->assertEquals("Something went wrong", $decoded["errorMessage"]);
    }

    /**
     * Test that an empty DataError represents success (no error)
     */
    public function testEmptyError(): void
    {
        // Default constructor with no arguments
        $error = new \Tina4\DataError();
        $this->assertEquals("", $error->getErrorCode());
        $this->assertEquals("", $error->getErrorMessage());
        $this->assertEquals(" ", $error->getErrorText()); // "code message" with empty strings = " "

        // Explicit empty strings
        $error2 = new \Tina4\DataError("", "");
        $this->assertEquals("", $error2->getErrorCode());
        $this->assertEquals("", $error2->getErrorMessage());

        $arr = $error2->getError();
        $this->assertEquals("", $arr["errorCode"]);
        $this->assertEquals("", $arr["errorMessage"]);
    }
}
