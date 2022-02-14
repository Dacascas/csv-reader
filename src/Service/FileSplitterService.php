<?php
declare(strict_types=1);

namespace Importer\Service;

class FileSplitterService
{
    /**
     * The file size that is optimal to process the parsing
     * The value itself could be optimized base on the testing
     */
    public const POSSIBLE_FILESIZE = 300000;

    /**
     * @var string
     */
    private $fileName;

    /**
     * Set the path to the file that will be use in the file split process
     *
     * @param string $filename
     * @return void
     */
    public function setFilename(string $filename): FileSplitterService
    {
        $this->fileName = $filename;

        return $this;
    }

    /**
     * Do a checking if the filesize is more that expected
     *
     * @return bool
     */
    public function isProcessPossible(): bool
    {
        return filesize($this->fileName) < self::POSSIBLE_FILESIZE;
    }

    /**
     * Generate unique input file hash
     *
     * @return string
     */
    public function getFileHash(): string
    {
        return md5(filesize($this->fileName) . $this->fileName);
    }
}
