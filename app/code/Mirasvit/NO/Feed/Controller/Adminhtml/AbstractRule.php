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
use Mirasvit\Feed\Api\Data\RuleInterface;
use Mirasvit\Feed\Controller\Registry;
use Mirasvit\Feed\Repository\RuleRepository;
use Magento\Framework\View\Result\Layout;

abstract class AbstractRule extends Action
{
    protected $ruleRepository;

    protected $context;

    private   $registry;

    public function __construct(
        RuleRepository $ruleRepository,
        Registry       $registry,
        Context        $context
    ) {
        $this->ruleRepository = $ruleRepository;
        $this->registry       = $registry;
        $this->context        = $context;

        parent::__construct($context);
    }


    public function initModel(): ?RuleInterface
    {
        $model = $this->ruleRepository->create();

        if ($this->getRequest()->getParam(RuleInterface::ID)) {
            $model = $this->ruleRepository->get((int)$this->getRequest()->getParam(RuleInterface::ID));
        }

        if ($model) {
            $this->registry->setRule($model);
        }

        return $model;
    }

    protected function initPage(Layout $page): Layout
    {
        $page->setActiveMenu('Magento_Catalog::catalog');
        $page->getConfig()->getTitle()->prepend(__('Advanced Product Feeds'));
        $page->getConfig()->getTitle()->prepend(__('Filters'));

        return $page;
    }

    protected function _isAllowed(): bool
    {
        return $this->context->getAuthorization()
            ->isAllowed('Mirasvit_Feed::feed');
    }
}
