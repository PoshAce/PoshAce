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

namespace Magetrend\Affiliate\Block\Adminhtml\Program;

class Grid extends \Magento\Backend\Block\Widget\Grid
{

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magetrend\Affiliate\Model\PopupFactory $queueFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magetrend\Affiliate\Model\ProgramFactory $queueFactory,
        array $data = []
    ) {
        $this->_queueFactory = $queueFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    public function getGridUrl()
    {
        return $this->getUrl('affiliate/*/grid', ['_current' => true]);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
            'affiliate/*/edit',
            ['popup_id' => $row->getId()]
        );
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
        $collecion = $this->getCollection();
        $this->setCollection($collecion);
        return $this;
    }
}
