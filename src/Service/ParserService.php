<?php
declare(strict_types=1);

namespace Importer\Service;

use Importer\Entity\AbstractEntity;
use Importer\Report;

class ParserService
{
    /**
     * @var string
     */
    private $importId;

    /**
     * @param string $importId
     * @return ParserService
     */
    public function setImportId(string $importId): ParserService
    {
        $this->importId = $importId;

        return $this;
    }

    /**
     * Find a position of the necessary CSV title base on the mapping relation
     *
     * @param array $row
     * @param array $mapping
     * @return array
     */
    public function findPositionByMapping(array $row, array $mapping): array
    {
        $mappingValue = array_flip($mapping);
        $position = [];

        foreach ($row as $key => $title) {
            if (isset($mappingValue[$title])) {
                $position[$mappingValue[$title]] = $key;
            }
        }

        return $position;
    }

    /**
     * Collect merchant data based on the position in csv file
     *
     * @param array $position
     * @param array $row
     * @return array
     */
    public function collectMerchant(array $position, array $row): array
    {
        return [
            Report::MERCHANT_ID => $row[$position[Report::MERCHANT_ID]] ?? 0,
            Report::MERCHANT_NAME => $row[$position[Report::MERCHANT_NAME]] ?? '',
            AbstractEntity::IMPORT_ID_FIELD => $this->importId
        ];
    }

    /**
     * Collect batch data based on the position in csv file
     *
     * @param array $position
     * @param array $row
     * @return array
     */
    public function collectBatch(array $position, array $row): array
    {
        return [
            Report::MERCHANT_ID => $row[$position[Report::MERCHANT_ID]] ?? 0,
            Report::BATCH_DATE => $row[$position[Report::BATCH_DATE]] ?? '',
            Report::BATCH_REF_NUM => $row[$position[Report::BATCH_REF_NUM]] ?? '',
            AbstractEntity::IMPORT_ID_FIELD => $this->importId
        ];
    }

    /**
     * Collect transaction data based on the position in csv file
     *
     * @param array $position
     * @param array $row
     * @return array
     */
    public function collectTransaction(array $position, array $row): array
    {
        return [
            Report::MERCHANT_ID => $row[$position[Report::MERCHANT_ID]] ?? 0,
            Report::BATCH_DATE => $row[$position[Report::BATCH_DATE]] ?? '',
            Report::BATCH_REF_NUM => $row[$position[Report::BATCH_REF_NUM]] ?? '',
            Report::TRANSACTION_DATE => $row[$position[Report::TRANSACTION_DATE]] ?? 0,
            Report::TRANSACTION_TYPE => $row[$position[Report::TRANSACTION_TYPE]] ?? '',
            Report::TRANSACTION_CARD_TYPE => $row[$position[Report::TRANSACTION_CARD_TYPE]] ?? '',
            Report::TRANSACTION_CARD_NUMBER => $row[$position[Report::TRANSACTION_CARD_NUMBER]] ?? '',
            Report::TRANSACTION_AMOUNT => $row[$position[Report::TRANSACTION_AMOUNT]] ?? '',
            AbstractEntity::IMPORT_ID_FIELD => $this->importId
        ];
    }
}
