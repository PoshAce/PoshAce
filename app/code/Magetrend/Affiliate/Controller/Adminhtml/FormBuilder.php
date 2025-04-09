<?php
/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * @category MageTrend
 * @package  Magetend/Affiliate
 * @author   Edvinas St. <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.magetrend.com/magento-2-affiliate
 */

namespace  Magetrend\Affiliate\Controller\Adminhtml;

class FormBuilder extends \Magento\Backend\App\Action
{
    /**
     * @var \Magetrend\Affiliate\Model\FormBuilder\FormFactory
     */
    public $formFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * FormBuilder constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magetrend\Affiliate\Model\FormBuilder\FormFactory $formFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magetrend\Affiliate\Model\FormBuilder\FormFactory $formFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->formFactory = $formFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Form Builder/ Manage Forms'));

        $this->_view->renderLayout();
    }

    /**
     * Check if user has enough privileges
     * @return bool
     */
    //@codingStandardsIgnoreLine
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magetrend_Affiliate::formbuilder');
    }
}
