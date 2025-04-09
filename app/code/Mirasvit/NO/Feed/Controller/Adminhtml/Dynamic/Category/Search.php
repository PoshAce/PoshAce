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

namespace Mirasvit\Feed\Controller\Adminhtml\Dynamic\Category;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Mirasvit\Feed\Controller\Adminhtml\Dynamic\Category;
use Mirasvit\Feed\Helper\CategoryMapping\Multiplicity\FileReaderMultiplicity;
use Mirasvit\Feed\Helper\CategoryMapping\ReaderMapper;

class Search extends Category
{
    public function execute(): Json
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $search = $this->getRequest()->getParam('query');

        $om = ObjectManager::getInstance();

        /** @var ReaderMapper $readerMapper */
        $readerMapper = $om->get('Mirasvit\Feed\Helper\CategoryMapping\ReaderMapper');

        /** @var FileReaderMultiplicity $fileReaderMultiplicity */
        $fileReaderMultiplicity = $om->get('Mirasvit\Feed\Helper\CategoryMapping\Multiplicity\FileReaderMultiplicity');
        $fileReaderMultiplicity->findAll();
        if ($fileReaderMultiplicity->count()) {
            $readerMapper->addMultiplicity($fileReaderMultiplicity);
        }

        $resultPage->setData($readerMapper->getData($search));

        return $resultPage;
    }
}
