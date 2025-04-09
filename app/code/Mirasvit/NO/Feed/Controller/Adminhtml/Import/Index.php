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

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page\Interceptor;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Page;

class Index extends Action
{
    protected $context;

    public function __construct(
        Context $context
    ) {
        $this->context = $context;

        parent::__construct($context);
    }


    public function execute()
    {
        /** @var Interceptor $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->initPage($resultPage);

        return $resultPage;
    }

    protected function initPage(Page $resultPage): Page
    {
        $resultPage->setActiveMenu('Mirasvit_Feed::feed');

        $resultPage->getConfig()->getTitle()->prepend(__('Import/Export Data'));

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Feed::feed_import');
    }
}
