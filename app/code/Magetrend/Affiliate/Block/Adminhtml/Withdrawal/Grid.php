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

namespace Magetrend\Affiliate\Block\Adminhtml\Withdrawal;

class Grid extends \Magento\Backend\Block\Widget\Grid
{

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magetrend\Affiliate\Model\WithdrawalFactory $queueFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magetrend\Affiliate\Model\WithdrawalFactory $queueFactory,
        array $data = []
    ) {
        $this->_queueFactory = $queueFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    public function getGridUrl()
    {
        return $this->getUrl('affiliate/*/grid', ['_current' => true]);
    }

    /**
     * Prepare collection
     *
     * @return $this
     */
    //@codingStandardsIgnoreLine
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $collecion = $this->getCollection()
            ->joinCustomer([
                'customer_email' => 'email',
                'customer_firstname' => 'firstname',
                'customer_lastname' => 'lastname',
            ], ['paypal_account_email']);

        $this->setCollection($collecion);
        return $this;
    }
}
