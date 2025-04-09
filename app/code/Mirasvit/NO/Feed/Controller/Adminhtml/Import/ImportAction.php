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


namespace Mirasvit\Feed\Controller\Adminhtml\Import;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Mirasvit\Feed\Factory\EntityFactory;
use Mirasvit\Feed\Service\ImportService;

class ImportAction extends Action
{
    private $entityFactory;

    private $resultForwardFactory;

    private $importService;

    public function __construct(
        Context        $context,
        EntityFactory  $entityFactory,
        ForwardFactory $resultForwardFactory,
        ImportService  $importService
    ) {
        $this->entityFactory        = $entityFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->importService        = $importService;

        parent::__construct($context);
    }

    public function execute()
    {
        $importData        = $this->getRequest()->getParams();
        $importData        = $importData['import'];
        $entityName        = $importData['import_data'];
        $entityMessageName = ucfirst(str_replace('_', ' ', $entityName));

        if (isset($importData[$entityName]) && $importData[$entityName]) {
            foreach ($importData[$entityName] as $path) {
                try {
                    $model = $this->entityFactory->getEntityModelFactory($entityName);

                    if ($this->importService->import($model, $path)) {
                        $this->messageManager->addSuccessMessage(
                            __('%1 "%2" has been imported.', $entityMessageName, $model->getName())
                        );
                    }
                } catch (Exception $e) {
                    $this->messageManager->addErrorMessage($path . ' ' . $e->getMessage());
                }
            }

            return $this->resultRedirectFactory->create()->setPath('*/*/');
        } else {
            $this->messageManager->addErrorMessage(__('%1 has not been selected', $entityMessageName));

            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
    }
}
