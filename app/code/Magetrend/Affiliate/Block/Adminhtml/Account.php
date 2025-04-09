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

namespace Magetrend\Affiliate\Block\Adminhtml;

use Magento\Core\Model\ObjectManager;

class Account extends \Magento\Backend\Block\Widget\Grid\Container
{
    public $accountType;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magetrend\Affiliate\Model\Config\ProgramType $accountType,
        array $data = []
    ) {
        $this->accountType = $accountType;
        parent::__construct($context, $data);
    }

    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_controller = 'popup_index';
        $this->_headerText = __('Affiliate Accounts');
        parent::_construct();
    }

    /**
     * {@inheritdoc}
     */
    //@codingStandardsIgnoreLine
    protected function _prepareLayout()
    {
        $this->removeButton('add');
        return parent::_prepareLayout();
    }
}
