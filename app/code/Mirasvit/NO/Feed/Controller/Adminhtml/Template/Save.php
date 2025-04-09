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

namespace Mirasvit\Feed\Controller\Adminhtml\Template;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Registry;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Feed\Model\TemplateFactory;
use Mirasvit\Feed\Controller\Adminhtml\Template;
use Mirasvit\Feed\Helper\Data as Helper;

class Save extends Template
{
    protected $templateFactory;

    protected $helper;


    public function __construct(
        TemplateFactory $templateFactory,
        Registry        $registry,
        Context         $context,
        Helper          $helper,
        ForwardFactory  $resultForwardFactory
    ) {
        $this->helper = $helper;

        parent::__construct($templateFactory, $registry, $context, $resultForwardFactory);
    }


    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data           = $this->helper->removeJS($this->getRequest()->getParams());

        if ($data) {
            $data  = $this->prepareData($data);
            $model = $this->initModel();

            $model->addData($data);

            try {
                $model->save();

                $this->messageManager->addSuccess(__('You saved the template.'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
            }
        } else {
            $this->messageManager->addError(__('No data to save.'));

            return $resultRedirect->setPath('*/*/');
        }
    }

    private function prepareData(array $originalData): array
    {
        $data = [];

        $data = $originalData['general'];
        $type = $originalData['general']['type'];

        if ($type == 'txt') {
            $type = 'csv';
        }

        if ($type === 'csv') {
            $originalData = $this->helper->processDeleteFlags($originalData);
        }

        if (isset($originalData[$type])) {
            $data['format_serialized'] = SerializeService::encode($originalData[$type]);
        }

        return $data;
    }
}
