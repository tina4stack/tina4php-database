<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

namespace Tina4;

use DateTimeInterface;

trait DataUtility
{

    /**
     * Validates and converts an ISO8601 date string to YYYY-MM-DD HH:MM:SS format
     * @param string $isoDate The ISO8601 date string (e.g., '2025-04-08T13:49:40')
     * @return string|null The formatted date (e.g., '2025-04-08 13:49:40') or null if invalid
     */
    public function isoToNormalDate(string $isoDate, string $dateFormat = "Y-m-d H:i:s"): ?string
    {
        // Remove milliseconds if present
        $isoDate = preg_replace('/\.\d{3}/', '', $isoDate);

        // Attempt to parse ISO8601 date
        $date = \DateTime::createFromFormat(\DateTimeInterface::ATOM, $isoDate);

        // Check if parsing was successful
        if ($date === false) {
            return null;
        }

        // Reformat to specified format
        return $date->format($dateFormat);
    }

    /**
     * Validates and converts a YYYY-MM-DD HH:MM:SS date string to ISO8601 format
     * @param string $normalDate The date string (e.g., '2025-04-08 13:49:40')
     * @return string|null The ISO8601 date (e.g., '2025-04-08T13:49:40+00:00') or null if invalid
     */
    public function normalToIsoDate(string $normalDate, $dateFormat="Y-m-d H:i:s"): ?string
    {
        // Attempt to parse YYYY-MM-DD HH:MM:SS date
        $date = \DateTime::createFromFormat($dateFormat, $normalDate);

        // Check if parsing was successful
        if ($date === false) {
            return null;
        }

        // Reformat to ISO8601 (ATOM format)
        return $date->format(\DateTimeInterface::ATOM);
    }

    /**
     * Checks if the string is a valid date in ISO8601 format
     * @param string $dateString
     * @return bool
     */
    public function isIsoDate(string $dateString): bool
    {
        // Handle milliseconds by checking for their presence
        $format = \DateTimeInterface::ATOM; // Y-m-d\TH:i:sP
        if (preg_match('/\.\d{3}/', $dateString)) {
            // If milliseconds are present, adjust the format
            $format = 'Y-m-d\TH:i:s.vP';
        }

        // Attempt to parse the date string
        $date = \DateTime::createFromFormat($format, $dateString);

        // Check if parsing was successful
        if ($date === false) {

            return false;
        }

        // Reformat parsed date and compare to handle variations (e.g., 'Z' vs '+00:00')
        $formatted = $date->format($format);
        $normalizedInput = str_replace('Z', '+00:00', $dateString);

        // Check if the input matches the parsed and reformatted date
        if ($formatted !== $normalizedInput) {

            return false;
        }

        return true;
    }

    /**
     * Makes sure the field is a date field and formats the data accordingly
     * @param string|null $dateString
     * @param string $databaseFormat
     * @return bool
     */
    public function isDate(?string $dateString, string $databaseFormat): bool
    {
        if ($dateString === null) {
            return false;
        }

        if (is_array($dateString) || is_object($dateString)) {
            return false;
        }
        
        if (substr($dateString, -1, 1) === "Z") {
            $dateParts = explode("T", $dateString);
        } else {
            $dateParts = explode(" ", $dateString);
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}(?:\.\d+)?[+-]\d{2}(?::?\d{2})?$/', $dateString)) {
            return true;
        }
        
        $d = \DateTime::createFromFormat($databaseFormat, str_replace(chr(0), '', $dateParts[0]));

        return $d && $d->format($databaseFormat) === $dateParts[0];
    }

    /**
     * Returns a formatted date in the specified output format
     * @param string|null $dateString Date input
     * @param string $databaseFormat Format in date format of PHP
     * @param string $outputFormat Output of the date in the specified format
     * @return string The resulting formatted date
     */
    public function formatDate(?string $dateString, string $databaseFormat, string $outputFormat): ?string
    {
        //Hacky fix for weird dates?
        $dateString = str_replace(".000000", "", $dateString);

        if (!empty($dateString)) {
            if ($dateString[strlen($dateString) - 1] === "Z") {
                $delimiter = "T";
                $dateParts = explode($delimiter, $dateString);
                $d = \DateTime::createFromFormat($databaseFormat, str_replace(chr(0), '', $dateParts[0]));
                if ($d) {
                    return $d->format($outputFormat) . $delimiter . $dateParts[1];
                }
                return null;
            }

            if (preg_match('/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})(?:\.\d+)?([+-]\d{2}(?::?\d{2})?)$/', $dateString, $matches)) {
                $basePart = $matches[1];
                $offset   = str_replace(':', '', $matches[2]);
                if (strlen($offset) === 3) {
                    $offset .= '00';
                }
                $d = \DateTime::createFromFormat('Y-m-d H:i:sO', $basePart . $offset);
                if ($d) {
                    return $d->format($outputFormat);
                }
                return null;
            }
            
            if (strpos($dateString, ":") !== false) {
                $databaseFormat .= " H:i:s";
                if (strpos($outputFormat, "T")) {
                    $outputFormat .= "H:i:s";
                } else {
                    $outputFormat .= " H:i:s";
                }
            }
            $d = \DateTime::createFromFormat($databaseFormat, $dateString);
            if ($d) {
                return $d->format($outputFormat);
            }

            return null;
        } else {
            return null;
        }
    }

    /**
     * This tests a string result from the DB to see if it is binary or not so it gets base64 encoded on the result
     * Delegates to the canonical implementation in \Tina4\Utilities (tina4php-core) when available
     * @param string|null $string $string Data to be checked to see if it is binary data like images
     * @return bool True if the string is binary
     * @see \Tina4\Utility::isBinary()
     * @tests tina4
     *
     *   assert(null) === false,"Check if binary returns false"
     */
    public function isBinary(?string $string): bool
    {
        if (class_exists('\Tina4\Utilities')) {
            return (new \Tina4\Utilities())->isBinary($string);
        }

        // Fallback for standalone usage without tina4php-core
        if ($string === null || is_numeric($string) || empty($string)) {
            return false;
        }
        if (is_string($string) && strlen($string) > 50 && @is_array(@getimagesizefromstring($string))) {
            return true;
        }
        $isBinary = false;
        $string = str_ireplace("\t", "", $string);
        $string = str_ireplace("\n", "", $string);
        $string = str_ireplace("\r", "", $string);

        if (is_string($string) && ctype_print($string) === false && strspn($string, '01') === strlen($string)) {
            $isBinary = true;
        }

        return $isBinary;
    }
}
