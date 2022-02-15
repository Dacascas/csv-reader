<?php

use Importer\Importer;
use Importer\Report;

include __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
include __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

$filename = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'report.csv';

$mapping  = [
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

$dbConnection = new \Importer\DB(
    $config['db']['host'],
    $config['db']['dbname'],
    $config['db']['user'],
    $config['db']['pass']
);
$storageService = new \Importer\Service\StorageService(
    new \Importer\Entity\Merchant($dbConnection),
    new \Importer\Entity\Batch($dbConnection),
    new \Importer\Entity\Transaction($dbConnection),
    new \Importer\Entity\ImportHistory($dbConnection),
);

$result = (new Importer(
    $storageService,
    new \Importer\Service\FileSplitterService(),
    new \Importer\Service\ParserService()
))->process($filename, $mapping);

echo sprintf(
    'Imported %d merchants, %d batches, and %d transactions' . PHP_EOL,
    $result->getMerchantCount(),
    $result->getBatchCount(),
    $result->getTransactionCount()
);
