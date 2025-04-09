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

namespace Mirasvit\Feed\Controller\Adminhtml\Dynamic;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\Feed\Model\Dynamic\VariableFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Mirasvit\Feed\Model\Dynamic\Variable as ModelVariable;
use Magento\Backend\Model\View\Result\Page\Interceptor;

abstract class Variable extends Action
{
    protected $variableFactory;

    protected $registry;

    protected $context;

    protected $resultForwardFactory;

    public function __construct(
        VariableFactory $variableFactory,
        Registry        $registry,
        Context         $context,
        ForwardFactory  $resultForwardFactory
    ) {
        $this->variableFactory      = $variableFactory;
        $this->registry             = $registry;
        $this->context              = $context;
        $this->resultForwardFactory = $resultForwardFactory;

        parent::__construct($context);
    }

    protected function initPage(Interceptor $resultPage): Interceptor
    {
        $resultPage->setActiveMenu('Magento_Catalog::catalog');
        $resultPage->getConfig()->getTitle()->prepend(__('Advanced Product Feeds'));
        $resultPage->getConfig()->getTitle()->prepend(__('Dynamic Variables'));

        return $resultPage;
    }

    protected function initModel(): ModelVariable
    {
        $model = $this->variableFactory->create();

        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        $this->registry->register('current_model', $model);

        return $model;
    }

    protected function _isAllowed(): bool
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Feed::feed_dynamic_variable');
    }
}
