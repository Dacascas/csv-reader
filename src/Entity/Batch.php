<?php
declare(strict_types=1);

namespace Importer\Entity;

class Batch extends AbstractEntity
{
    /**
     * @inheritdoc
     */
    protected $tableName = 'batch';

    /**
     * @inheritDoc
     */
    public function batchInsert(array $items): void
    {
        try {
            $this->execute($items);
        } catch (\Throwable $exception) {
            print_r("The Batch can't be inserted \n", true);
        }
    }

    /**
     * @inheritdoc
     */
    protected function getColumns(): array
    {
        return ['mid', 'batch_date', 'batch_ref_num', 'import_id'];
    }
}
