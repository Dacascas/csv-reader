<?php

declare(strict_types=1);

namespace Importer\Entity;

class Transaction extends AbstractEntity
{
    /**
     * @inheritdoc
     */
    protected $tableName = 'transaction';

    /**
     * @inheritDoc
     */
    public function batchInsert(array $items): void
    {
        try {
            $this->execute($items);
        } catch (\Throwable $exception) {
            print_r("The Transaction can't be inserted\n", true);
        }
    }

    /**
     * @inheritdoc
     */
    protected function getColumns(): array
    {
        return [
            'mid',
            'batch_date',
            'batch_ref_num',
            'trans_date',
            'trans_type',
            'trans_card_type',
            'trans_card_num',
            'trans_amount',
            'import_id'
        ];
    }
}
