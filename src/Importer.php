<?php

declare(strict_types=1);

namespace Importer;

use Importer\Service\{FileSplitterService, ParserService, StorageService};

/**
 * Class Importer
 */
class Importer
{
    /**
     * The batch items count could be adjusted base on the idea
     * that the length of the total INSERT statement with rows of data is less than net_buffer_length from MYSQL
     * This need to be tested and find an optimal number
     */
    const BATCH_ITEMS_COUNT = 100;

    /**
     * This data structure is introduced due to the reason to not do a complicated
     * object-oriented approach
     */
    const CSV_DEFAULT_COLLECTION = [
        'm' => [],
        'b' => [],
        't' => [],
    ];
    /**
     * @var StorageService
     */
    private $storageService;
    /**
     * @var FileSplitterService
     */
    private $fileSplitterService;
    /**
     * @var ParserService
     */
    private $parserService;

    /**
     * Importer constructor.
     */
    public function __construct(
        StorageService $storageService,
        FileSplitterService $fileSplitterService,
        ParserService $parserService
    ) {
        $this->storageService = $storageService;
        $this->fileSplitterService = $fileSplitterService;
        $this->parserService = $parserService;
    }

    /**
     * Imports a given report
     *
     * @param string $filename Full path to the report
     * @param string[] $mapping Report mapping
     *
     * @return Result Result of the import process
     */
    public function process(string $filename, array $mapping): Result
    {
        if ($this->fileSplitterService->setFilename($filename)->isProcessPossible()) {
            $importId = $this->fileSplitterService->getFileHash();

            if (!$this->storageService->createImportHistory($importId)) {
                return $this->storageService->getCount($importId);
            };

            $this->parserService->setImportId($importId);

            return $this->parseAndSave($filename, $mapping, $importId);
        }

        /**
         * Here is possible to implement the functionality that will do a correct split
         * the input file to the smaller base on the filesize for small files
         * and in separate call do a parsing for each small file
         *
         * $filePaths = $this->fileSplitterService->setFilename($filename)->splitAndOptimize();
         * foreach ($filePaths as $filePath) {
         *   $this->parseAndSave($filePath, $mapping);
         * }
         */

        print_r('The parsing is not possible the file size is more that expected - ' .
            $this->fileSplitterService::POSSIBLE_FILESIZE . "\n");

        return new Result(0, 0, 0);
    }

    /**
     * Process, parse and send to save data from csv file
     *
     * @param string $filename
     * @param array $mapping
     * @param string $importId
     * @return Result
     */
    public function parseAndSave(string $filename, array $mapping, string $importId): Result
    {
        $handle = fopen($filename, 'r');

        $lineNumber = 1;
        $position = [];
        $items = self::CSV_DEFAULT_COLLECTION;

        while (($rawString = fgets($handle)) !== false) {
            $row = str_getcsv($rawString);

            if ($lineNumber === 1) {
                $position = $this->parserService->findPositionByMapping($row, $mapping);
                $lineNumber++;
                continue;
            }

            if (empty($position)) {
                break;
            }

            $items['m'][] = $this->parserService->collectMerchant($position, $row);
            $items['b'][] = $this->parserService->collectBatch($position, $row);
            $items['t'][] = $this->parserService->collectTransaction($position, $row);

            if (count($items['t']) === self::BATCH_ITEMS_COUNT) {
                $this->storageService->batchInsert($items['m'], $items['b'], $items['t']);
                $items = self::CSV_DEFAULT_COLLECTION;
            }

            $lineNumber++;
        }

        if (count($items['t']) < self::BATCH_ITEMS_COUNT) {
            $this->storageService->batchInsert($items['m'], $items['b'], $items['t']);
            $items = self::CSV_DEFAULT_COLLECTION;
        }

        fclose($handle);

        return $this->storageService->getCount($importId);
    }
}
