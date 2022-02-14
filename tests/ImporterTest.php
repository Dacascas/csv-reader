<?php

namespace Tests;

use Importer\Importer;
use Importer\Report;
use Importer\Result;
use Importer\Service\FileSplitterService;
use Importer\Service\ParserService;
use Importer\Service\StorageService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ImporterTest
 * @package Tests
 */
class ImporterTest extends TestCase
{
    /**
     * @var StorageService|MockObject
     */
    private $storageServiceMock;
    /**
     * @var FileSplitterService|MockObject
     */
    private $fileSplitterServiceMock;
    /**
     * @var ParserService|MockObject
     */
    private $parserServiceMock;

    /**
     * Tests Importer::process
     */
    public function testProcess()
    {
        /** @var Importer $importer */
        $importer = $this->createImporter();

        $this->fileSplitterServiceMock->expects($this->once())
            ->method('setFilename')
            ->with($this->getFile())
            ->willReturn($this->fileSplitterServiceMock);
        $this->fileSplitterServiceMock->expects($this->once())
            ->method('isProcessPossible')
            ->willReturn(true);
        $this->fileSplitterServiceMock->expects($this->once())
            ->method('getFileHash')
            ->willReturn('some-hash-string');

        $this->parserServiceMock->expects($this->once())
            ->method('findPositionByMapping')
            ->willReturn([[1],[2],[3]]);
        $this->parserServiceMock->expects($this->any())
            ->method('collectMerchant');
        $this->parserServiceMock->expects($this->any())
            ->method('collectBatch');
        $this->parserServiceMock->expects($this->any())
            ->method('collectTransaction');
        $this->parserServiceMock->expects($this->once())
            ->method('setImportId')
            ->with('some-hash-string');

        $this->storageServiceMock->expects($this->once())
            ->method('createImportHistory')
            ->with('some-hash-string')
            ->willReturn(true);
        $this->storageServiceMock->expects($this->once())
            ->method('batchInsert');
        $this->storageServiceMock->expects($this->once())
            ->method('getCount')
            ->with('some-hash-string')
            ->willReturn(new Result(2, 3, 5));

        $result   = $importer->process($this->getFile(), $this->getMapping());

        // 2 merchants
        $this->assertEquals(2, $result->getMerchantCount());

        // with 3 batches
        $this->assertEquals(3, $result->getBatchCount());

        // with 5 transactions
        $this->assertEquals(5, $result->getTransactionCount());
    }

    /**
     * Creates an importer instance for testing purposes
     */
    private function createImporter()
    {
        $this->storageServiceMock = $this->createMock(StorageService::class);
        $this->fileSplitterServiceMock = $this->createMock(FileSplitterService::class);
        $this->parserServiceMock = $this->createMock(ParserService::class);
        return new Importer($this->storageServiceMock, $this->fileSplitterServiceMock, $this->parserServiceMock);
    }

    /**
     * Gets a sample report
     *
     * @return string Full path to a sample report
     */
    private function getFile(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . 'small.csv';
    }

    /**
     * Gets a sample mapping
     *
     * @return string[] Sample mapping
     */
    private function getMapping(): array
    {
        return [
            Report::TRANSACTION_DATE        => 'Transaction Date',
            Report::TRANSACTION_TYPE        => 'Transaction Type',
            Report::TRANSACTION_CARD_TYPE   => 'Transaction Card Type',
            Report::TRANSACTION_CARD_NUMBER => 'Transaction Card Number',
            Report::TRANSACTION_AMOUNT      => 'Transaction Amount',
            Report::BATCH_DATE              => 'Batch Date',
            Report::BATCH_REF_NUM           => 'Batch Reference Number',
            Report::MERCHANT_ID             => 'Merchant ID',
            Report::MERCHANT_NAME           => 'Merchant Name',
        ];
    }
}
