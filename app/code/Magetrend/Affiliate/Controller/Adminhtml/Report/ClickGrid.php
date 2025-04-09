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

namespace Magetrend\Affiliate\Controller\Adminhtml\Report;

class ClickGrid extends \Magetrend\Affiliate\Controller\Adminhtml\Report
{
    /**
     * Coupon codes grid
     *
     * @return void
     */
    public function execute()
    {
        $accountId = $this->getRequest()->getParam('id');
        $this->accountManager->initAccount($accountId);
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
