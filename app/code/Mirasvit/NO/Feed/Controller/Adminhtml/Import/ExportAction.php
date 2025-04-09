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
use Mirasvit\Feed\Service\ExportService;
use Mirasvit\Feed\Helper\Data as DataHelper;
use Mirasvit\Feed\Model\Config;

class ExportAction extends Action
{
    private $dataHelper;

    private $entityFactory;

    private $resultForwardFactory;

    private $exportService;

    private $config;

    public function __construct(
        DataHelper     $dataHelper,
        EntityFactory  $entityFactory,
        Context        $context,
        ForwardFactory $resultForwardFactory,
        ExportService  $exportService,
        Config         $config
    ) {
        $this->dataHelper           = $dataHelper;
        $this->entityFactory        = $entityFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->exportService        = $exportService;
        $this->config               = $config;

        parent::__construct($context);
    }

    public function execute()
    {
        $exportData        = $this->getRequest()->getParams();
        $exportData        = $exportData['export'];
        $entityName        = $exportData['export_data'];
        $entityMessageName = ucfirst(str_replace('_', ' ', $entityName));

        if (isset($exportData[$entityName]) && $exportData[$entityName]) {
            foreach ($exportData[$entityName] as $entityId) {
                $model = $this->entityFactory->getEntityModelFactory($entityName)->load($entityId);
                $path  = $this->dataHelper->getEntityConfigPath($model, $entityName);

                try {
                    if ($this->exportService->export($model, $path)) {
                        $this->messageManager->addSuccessMessage(
                            __('%1 has been exported to %2', $entityMessageName, $this->config->printPath($path))
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
