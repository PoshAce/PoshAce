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

namespace Mirasvit\Feed\Controller\Adminhtml\Feed;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Feed\Controller\Adminhtml\Feed;
use Mirasvit\Feed\Model\ResourceModel\Feed\CollectionFactory;
use Mirasvit\Feed\Model\FeedFactory;
use Mirasvit\Feed\Model\Feed as ModelFeed;
use Mirasvit\Feed\Model\TemplateFactory;
use Mirasvit\Feed\Helper\Data as Helper;

class Save extends Feed
{
    protected $feedFactory;

    protected $templateFactory;

    protected $helper;

    protected $feedCollectionFactory;

    public function __construct(
        CollectionFactory $feedCollectionFactory,
        FeedFactory       $feedFactory,
        TemplateFactory   $templateFactory,
        Registry          $registry,
        Helper            $helper,
        Context           $context
    ) {
        $this->feedCollectionFactory = $feedCollectionFactory;
        $this->templateFactory       = $templateFactory;
        $this->helper                = $helper;

        parent::__construct($feedFactory, $registry, $context);
    }

    public function execute()
    {
        $id             = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        $data           = $this->getRequest()->getPostValue();

        if ($data) {

            $model = $this->initModel();
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This feed no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }
            $data = $this->helper->removeJS($this->filterPostData($data));

            if (isset($data['template_id'])) {
                $template = $this->templateFactory->create()->load($data['template_id']);
                $model->loadFromTemplate($template);
            }

            $model->addData($data);

            if (isset($data['filename']) && $this->checkNameDuplicates($model)) {
                $this->messageManager->addError(__('A file with the same name and type already exists.'));

                return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
            }

            try {
                $model->save();

                $this->messageManager->addSuccessMessage(__('You saved the feed.'));
                $this->backendSession->setFormData(false);

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }

                return $this->context->getResultRedirectFactory()->create()->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        } else {
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addError('No data to save.');

            return $resultRedirect;
        }
    }

    /**
     * Filter post data
     *
     * @param array $data
     *
     * @return array
     */


    public function filterPostData($data): array
    {
        $feed = [];
        if (isset($data['settings'])) {
            return $data['settings'];
        }
        $type = $data['general']['type'];

        if ($type == 'txt') {
            $type = 'csv';
        }

        if ($type === 'csv') {
            $data = $this->helper->processDeleteFlags($data);
        }

        unset($data['form_key']);

        foreach ($data as $key => $value) {
            if ($key == $type) {
                $feed['format_serialized'] = SerializeService::encode($value);
                continue;
            }
            if(is_array($value)) {
                foreach ($value as $key => $item) {
                    $feed[$key] = $item;
                }
            }
        }

        if (isset($feed['filename'])) {
            $feed['filename'] = basename($feed['filename']);
        }

        return $feed;
    }

    protected function checkNameDuplicates(ModelFeed $model): bool
    {
        $collection = $this->feedCollectionFactory->create();

        foreach ($collection as $feed) {

            if ($feed->getFilename() == $model->getFilename() && $model->getId() != $feed->getId()) {
                return true;
            }

        }

        return false;
    }
}
