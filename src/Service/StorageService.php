<?php

declare(strict_types=1);

namespace Importer\Service;

use Importer\Entity\{Batch, ImportHistory, Merchant, Transaction};
use Importer\Result;

class StorageService
{
    /**
     * @var Merchant
     */
    private $merchant;

    /**
     * @var Batch
     */
    private $batch;

    /**
     * @var Transaction
     */
    private $transaction;
    /**
     * @var ImportHistory
     */
    private $importHistory;

    /**
     * @param Merchant $merchant
     * @param Batch $batch
     * @param Transaction $transaction
     * @param ImportHistory $importHistory
     */
    public function __construct(
        Merchant $merchant,
        Batch $batch,
        Transaction $transaction,
        ImportHistory $importHistory
    ) {
        $this->merchant = $merchant;
        $this->batch = $batch;
        $this->transaction = $transaction;
        $this->importHistory = $importHistory;
    }

    /**
     * Process of insert to DB for each of entities
     *
     * @param array $merchant
     * @param array $batch
     * @param array $transaction
     * @return void
     */
    public function batchInsert(array $merchant, array $batch, array $transaction): void
    {
        $this->merchant->batchInsert($merchant);
        $this->batch->batchInsert($batch);
        $this->transaction->batchInsert($transaction);
    }

    /**
     * Get an object Result with necessary numbers
     *
     * @return Result
     * @var string $importId
     */
    public function getCount(string $importId): Result
    {
        return new Result(
            $this->batch->getCount($importId),
            $this->merchant->getCount($importId),
            $this->transaction->getCount($importId)
        );
    }

    /**
     * Create an item in the import history table
     *
     * @param string $importId
     * @return bool
     */
    public function createImportHistory(string $importId): bool
    {
        try {
            $this->importHistory->insert($importId);
        } catch (\PDOException $PDOException) {
            print_r("Import already exist with id - $importId\n");

            return false;
        }

        return true;
    }
}
