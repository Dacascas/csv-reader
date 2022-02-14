<?php

namespace Importer;

/**
 * Class Result
 */
class Result
{
    /** @var int Number of imported merchants */
    private $merchants = 0;

    /** @var int Number of imported batches */
    private $batches = 0;

    /** @var int Number of imported transactions */
    private $transactions = 0;

    /**
     * @param int $merchants
     * @param int $batches
     * @param int $transactions
     */
    public function __construct(int $merchants, int $batches, int $transactions)
    {
        $this->merchants = $merchants;
        $this->transactions = $transactions;
        $this->batches = $batches;
    }

    /**
     * Gets a number of imported merchants
     *
     * @return int Number of imported merchants
     */
    public function getMerchantCount(): int
    {
        return $this->merchants;
    }

    /**
     * Gets a number of imported batches
     *
     * @return int Number of imported batches
     */
    public function getBatchCount(): int
    {
        return $this->batches;
    }

    /**
     * Gets a number of imported transactions
     *
     * @return int Number of imported transactions
     */
    public function getTransactionCount(): int
    {
        return $this->transactions;
    }
}
