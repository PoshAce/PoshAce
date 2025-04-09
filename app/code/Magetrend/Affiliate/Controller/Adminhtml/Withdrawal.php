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

class Withdrawal extends Action
{
    /**
     * @var \Magetrend\Affiliate\Model\WithdrawalManager
     */
    public $withdrawalManager;

    /**
     * Withdrawal constructor.
     * @param Action\Context $context
     * @param \Magetrend\Affiliate\Model\WithdrawalManager $withdrawalManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magetrend\Affiliate\Model\WithdrawalManager $withdrawalManager
    ) {
        $this->withdrawalManager = $withdrawalManager;
        parent::__construct($context);
    }

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
        return $this->_authorization->isAllowed('Magetrend_Affiliate::withdrawal');
    }
}
