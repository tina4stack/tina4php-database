<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

namespace Tina4;

interface DataBaseMetaData
{
    /**
     * Create the database metadata
     * @param Database $connection
     */
    public function __construct(Database $connection);

    /**
     * Gets all the tables in the database
     * @return array
     */
    public function getTables() : array;

    /**
     * Gets all the primary keys for the table
     * @param string $tableName
     * @return array
     */
    public function getPrimaryKeys(string $tableName): array;

    /**
     * Gets all the foreign keys for the table
     * @param string $tableName
     * @return array
     */
    public function getForeignKeys(string $tableName): array;

    /**
     * Gets the table information
     * @param string $tableName
     * @return array
     */
    public function getTableInformation(string $tableName): array;

    /**
     * Gets the complete database metadata
     * @return array
     */
    public function getDatabaseMetaData(): array;
}
