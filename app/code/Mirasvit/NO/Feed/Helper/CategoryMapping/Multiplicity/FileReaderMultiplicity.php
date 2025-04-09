<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-feed
 * @version   1.4.6
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\Feed\Helper\CategoryMapping\Multiplicity;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem;
use Magento\Framework\Module\Dir\Reader;
use Mirasvit\Feed\Helper\CategoryMapping\FileInterface;

class FileReaderMultiplicity extends ReaderMultiplicity
{
    public function findAll()
    {
        $mappingPaths = $this->getMappingPaths();

        foreach ($mappingPaths as $mappingPath) {
            foreach (glob($mappingPath . "/*.txt") as $filename) {
                /** @var FileInterface $fileReader */
                $fileReader = $this->getReader();
                $this->addItem($fileReader->setFile($filename));
            }
        }

        return $this;
    }

    protected function getReader(): FileInterface
    {
        $om = ObjectManager::getInstance();

        return $om->create('Mirasvit\Feed\Helper\CategoryMapping\FileReader');
    }

    protected function getMappingPaths(): array
    {
        $paths = [];

        $om = ObjectManager::getInstance();

        /** @var Reader $directoryReader */
        $directoryReader = $om->get('Magento\Framework\Module\Dir\Reader');
        $paths[]         = realpath($directoryReader->getModuleDir('etc', 'Mirasvit_Feed') . '/../Setup/data/mapping/');

        /** @var Filesystem $filesystem */
        $filesystem = $om->get('Magento\Framework\Filesystem');

        $paths[] = $filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . 'feed/mapping/';

        return $paths;
    }
}
