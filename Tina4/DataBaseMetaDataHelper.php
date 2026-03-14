<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

namespace Tina4;

/**
 * Provides shared implementations for DataBaseMetaData methods.
 * Classes using this trait must implement getTables(), getTableInformation(),
 * getPrimaryKeys(), and getForeignKeys() from the DataBaseMetaData interface.
 */
trait DataBaseMetaDataHelper
{
    /**
     * Gets the complete database metadata
     * @return array
     */
    final public function getDatabaseMetaData(): array
    {
        $database = [];
        $tables = $this->getTables();

        foreach ($tables as $record) {
            $tableInfo = $this->getTableInformation($record->tableName);

            $database[strtolower($record->tableName)] = $tableInfo;
        }

        return $database;
    }

    /**
     * Builds lookup arrays for primary and foreign keys of a table
     * @param string $tableName
     * @return array{primary: array<string, true>, foreign: array<string, true>}
     */
    final public function buildKeyLookups(string $tableName): array
    {
        $primaryKeys = $this->getPrimaryKeys($tableName);
        $primaryKeyLookup = [];
        foreach ($primaryKeys as $primaryKey) {
            $fieldName = $primaryKey->fieldName ?? $primaryKey->name ?? "";
            $primaryKeyLookup[$fieldName] = true;
        }

        $foreignKeys = $this->getForeignKeys($tableName);
        $foreignKeyLookup = [];
        foreach ($foreignKeys as $foreignKey) {
            $fieldName = $foreignKey->fieldName ?? "";
            $foreignKeyLookup[$fieldName] = true;
        }

        return ['primary' => $primaryKeyLookup, 'foreign' => $foreignKeyLookup];
    }
}
