<?php

declare(strict_types=1);

namespace Importer\Entity;

use Importer\DB;

class ImportHistory
{
    /**
     * The DB connection
     *
     * @var DB
     */
    private $connection;

    /**
     * The name of the table
     *
     * @var string
     */
    private $tableName = 'import_history';
    /**
     * @var string
     */
    private $lastImportId;

    /**
     * @param DB $connection
     */
    public function __construct(DB $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Insert an import id to the DB
     *
     * @param string $importId
     * @return void
     */
    public function insert(string $importId)
    {
        $this->lastImportId = $importId;
        $this->connection->getConnection()->query(sprintf('INSERT INTO %s (`id`) VALUES (\'%s\')', $this->tableName,
        $importId));
    }
}
