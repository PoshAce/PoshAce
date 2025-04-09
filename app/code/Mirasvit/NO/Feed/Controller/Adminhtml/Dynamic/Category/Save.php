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

use Exception;
use Mirasvit\Feed\Model\Dynamic\CategoryFactory as CategoryFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Mirasvit\Feed\Controller\Adminhtml\Dynamic\Category;
use Mirasvit\Feed\Helper\Data as Helper;

class Save extends Category
{
    protected $categoryFactory;

    protected $helper;

    public function __construct(
        CategoryFactory $categoryFactory,
        Registry        $registry,
        Context         $context,
        Helper          $helper,
        ForwardFactory  $resultForwardFactory
    ) {
        $this->helper = $helper;

        parent::__construct($categoryFactory, $registry, $context, $resultForwardFactory);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data           = $this->helper->removeJS($this->getRequest()->getParams());
        if ($data) {
            $data  = $this->prepareData($data);
            $model = $this->initModel();

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('Item was successfully saved'));

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('Unable to find item to save'));

            return $resultRedirect->setPath('*/*/');
        }
    }

    protected function prepareData(array $data): array
    {

        if (isset($data['mapping_id']) && $data['mapping_id'] == '') {
            unset($data['mapping_id']);
        }

        if (isset($data['mapping'])) {
            $data['mapping_serialized'] = json_encode($data['mapping']);
            unset($data['mapping']);
        }

        return $data;
    }
}
