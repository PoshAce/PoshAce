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


namespace Mirasvit\Feed\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\Feed\Model\FeedFactory;
use Magento\Backend\Model\View\Result\Page\Interceptor;
use Mirasvit\Feed\Model\Feed as ModelFeed;

abstract class Feed extends Action
{
    protected $feedFactory;

    protected $registry;

    protected $backendSession;

    protected $context;

    public function __construct(
        FeedFactory $feedFactory,
        Registry    $registry,
        Context     $context
    ) {
        $this->feedFactory    = $feedFactory;
        $this->registry       = $registry;
        $this->context        = $context;
        $this->backendSession = $context->getSession();

        parent::__construct($context);
    }


    protected function initPage(Interceptor $resultPage): Interceptor
    {
        $resultPage->setActiveMenu('Magento_Catalog::catalog');
        $resultPage->getConfig()->getTitle()->prepend(__('Advanced Product Feeds'));
        $resultPage->getConfig()->getTitle()->prepend(__('Feeds'));

        if (!class_exists('ReflectionClass')) {
            $this->context->getMessageManager()->addError(__(
                "Zend Reflection library wasn't installed." . "</br>" .
                "To display the feed edit pages correctly,
                install this library on the server using the following SSH command:
                composer require 'zf1/zend-reflection'"
            ));
        }

        return $resultPage;
    }

    protected function initModel(): ModelFeed
    {
        $model = $this->feedFactory->create();

        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        $this->registry->register('current_model', $model);

        return $model;
    }

    protected function _isAllowed(): bool
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Feed::feed_feed');
    }
}
