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

namespace Magetrend\Affiliate\Block\Adminhtml\Report\Order;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * @var \Magetrend\Affiliate\Model\ResourceModel\Record\Click\CollectionFactory
     */
    public $collectionFactory;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magetrend\Affiliate\Model\ResourceModel\Record\Order\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        parent::_construct();
        $this->setId('couponCodesGrid');
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection for grid
     *
     * @return $this
     */
    //@codingStandardsIgnoreLine
    protected function _prepareCollection()
    {
        $account = $this->coreRegistry->registry('current_model');
        $collection = $this->collectionFactory->create();
        if ($account->getId()) {
            $collection->addFieldToFilter('referral_id', $account->getId());
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Define grid columns
     *
     * @return $this
     */
    //@codingStandardsIgnoreLine
    protected function _prepareColumns()
    {

        $this->addColumn(
            'order_increment_id',
            [
                'header' => __('# ID'),
                'index' => 'order_increment_id',
                'renderer' => 'Magetrend\Affiliate\Block\Adminhtml\Account\Grid\Column\Renderer\HashBefore',
            ]
        );

        $this->addColumn(
            'order_status',
            [
                'header' => __('Status'),
                'index' => 'order_status',
                'renderer' => 'Magetrend\Affiliate\Block\Adminhtml\Report\Order\Grid\Column\Renderer\Status',
            ]
        );

        $this->addColumn(
            'commissions',
            [
                'header' => __('Commissions'),
                'index' => 'commissions',
                'renderer' => 'Magetrend\Affiliate\Block\Adminhtml\Account\Grid\Column\Renderer\Currency',
            ]
        );

        $this->addColumn(
            'invoiced_amount',
            [
                'header' => __('Invoiced'),
                'index' => 'invoiced_amount',
                'renderer' => 'Magetrend\Affiliate\Block\Adminhtml\Account\Grid\Column\Renderer\Currency',
            ]
        );

        $this->addColumn(
            'refunded_amount',
            [
                'header' => __('Refunded'),
                'index' => 'refunded_amount',
                'renderer' => 'Magetrend\Affiliate\Block\Adminhtml\Account\Grid\Column\Renderer\Currency',
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Date'),
                'index' => 'created_at',
                'type' => 'datetime',
                'align' => 'center',
                'width' => '160'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        $account = $this->coreRegistry->registry('current_model');
        $params = ['_current' => true];
        if ($account->getId()) {
            $params = ['id' => $account->getId()];
        }
        return $this->getUrl('affiliate/report/orderGrid', $params);
    }
}
