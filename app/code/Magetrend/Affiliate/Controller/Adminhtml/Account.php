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

use Magento\Backend\App\Action;

/**
 * Account controller class
 */
class Account extends Action
{
    /**
     * @var \Magetrend\Affiliate\Model\AccountManager
     */
    public $accountManager;

    /**
     * @var \Magetrend\Affiliate\Model\RegistrationManager
     */
    public $registrationManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * Account constructor.
     * @param Action\Context $context
     * @param \Magetrend\Affiliate\Model\AccountManager $accountManager
     * @param \Magetrend\Affiliate\Model\RegistrationManager $registrationManager
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magetrend\Affiliate\Model\AccountManager $accountManager,
        \Magetrend\Affiliate\Model\RegistrationManager $registrationManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->accountManager = $accountManager;
        $this->registrationManager = $registrationManager;
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * Controller init method
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Check if user has enough privileges
     * @return bool
     */
    //@codingStandardsIgnoreLine
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magetrend_Affiliate::account');
    }
}
