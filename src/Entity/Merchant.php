<?php

declare(strict_types=1);

namespace Importer\Entity;

class Merchant extends AbstractEntity
{
    /**
     * @inheritdoc
     */
    protected $tableName = 'merchant';

    /**
     * @inheritDoc
     */
    public function batchInsert(array $items): void
    {
        try {
            $this->execute($items);
        } catch (\Throwable $exception) {
            print_r("The merchant can't be inserted\n", true);
        }
    }

    /**
     * @inheritdoc
     */
    protected function getColumns(): array
    {
        return ['mid', 'dba', 'import_id'];
    }
}
