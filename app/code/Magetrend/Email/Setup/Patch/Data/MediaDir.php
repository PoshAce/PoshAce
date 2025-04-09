<?php

namespace Magetrend\Email\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\App\Filesystem\DirectoryList;

class MediaDir implements DataPatchInterface
{
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $_io;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $_directoryList;


    /**
     * InstallSchema constructor.
     * @param File $io
     * @param DirectoryList $directoryList
     */
    public function __construct(
        File $io,
        DirectoryList $directoryList
    ) {
        $this->_io = $io;
        $this->_directoryList = $directoryList;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->_io->mkdir($this->_directoryList->getPath('media').'/email', 0775);
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
