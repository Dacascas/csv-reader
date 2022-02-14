<?php

namespace Importer\Entity;

use Importer\DB;

abstract class AbstractEntity
{
    /**
     * Common import id field for tables
     */
    public const IMPORT_ID_FIELD = 'import_id';

    /**
     * The DB connection
     *
     * @var DB
     */
    protected $connection;

    /**
     * The name of the table
     *
     * @var string
     */
    protected $tableName;

    /**
     * @param DB $connection
     */
    public function __construct(DB $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Execution of the batch insert for inherited class table
     *
     * @param array $rows
     * @return bool
     */
    protected function execute(array $rows): bool
    {
        if (empty($rows)) {
            return true;
        }

        $columnList = !empty($this->getColumns()) ? '(' . implode(', ', $this->getColumns()) . ')' : '';

        $rowPlaceholder = ' (' . implode(', ', array_fill(1, count($this->getColumns()), '?')) . ')';

        $onUpdate = array_map(function ($column) {return $column . ' = values(' . $column . ')';}, $this->getColumns());

        $query = sprintf(
            'INSERT INTO %s%s VALUES %s ON DUPLICATE KEY UPDATE %s',
            $this->tableName,
            $columnList,
            implode(', ', array_fill(1, count($rows), $rowPlaceholder)),
            implode(', ', $onUpdate)
        );

        return $this->connection->getConnection()->prepare($query)->execute($this->getData($rows));
    }

    /**
     * Get amount of items that was inserted
     *
     * @var string $importId
     * @return int
     */
    public function getCount(string $importId): int
    {
        return $this->connection->getConnection()->query(sprintf('SELECT count(*) FROM %s WHERE import_id = \'%s\'',
            $this->tableName, $importId))->fetchColumn();
    }

    /**
     * The abstract batch insert method to implement in the inherited classes
     *
     * @param array $items
     * @return void
     */
    abstract public function batchInsert(array $items): void;

    /**
     * Get columns that are related to the particular tables
     *
     * @return array
     */
    abstract protected function getColumns(): array;

    /**
     * @param array $rows
     * @return array
     */
    protected function getData(array $rows): array
    {
        $data = [];

        foreach ($rows as $rowData) {
            foreach ($rowData as $rowField) {
                $data[] = $rowField;
            }
        }
        return $data;
    }
}
