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

namespace Mirasvit\Feed\Controller\Adminhtml\Report;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Backend\Model\View\Result\Page;
use Mirasvit\Report\Api\Repository\ReportRepositoryInterface;

class View extends Action
{
    protected $registry;

    protected $backendSession;

    protected $context;

    protected $reportRepository;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        Registry                  $registry,
        Context                   $context
    ) {
        $this->reportRepository = $reportRepository;
        $this->registry         = $registry;
        $this->context          = $context;
        $this->backendSession   = $context->getSession();

        parent::__construct($context);
    }


    protected function initPage(Page $resultPage): Page
    {
        $resultPage->setActiveMenu('Magento_Catalog::catalog');
        $resultPage->getConfig()->getTitle()->prepend(__('Advanced Product Feeds'));
        $resultPage->getConfig()->getTitle()->prepend(__('Reports'));

        return $resultPage;
    }

    public function execute()
    {
        $this->registry->register('current_report', $this->reportRepository->get('feed_overview'));

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->initPage($resultPage);

        return $resultPage;
    }


    protected function _isAllowed(): bool
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Feed::feed_report');
    }
}
