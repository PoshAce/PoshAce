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

namespace Magetrend\Affiliate\Block\Report;

class Order extends \Magento\Framework\View\Element\Template
{
    public $accountHelper;

    public $collectionFactory;

    public $collection = null;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magetrend\Affiliate\Helper\Account $accountHelper,
        \Magetrend\Affiliate\Model\ResourceModel\Record\Order\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->accountHelper = $accountHelper;
        parent::__construct($context, $data);
    }

    public function getCollection()
    {
        if ($this->collection == null) {
            $this->collection = $this->collectionFactory->create()
                ->addFieldToFilter('referral_id', $this->accountHelper->getCurrentAccount()->getId())
                ->addFieldToFilter('is_visible', 1)
                ->setPageSize($this->getPageLimit())
                ->setCurPage($this->getCurPage())
                ->setOrder('entity_id', 'DESC');
        }

        return $this->collection;
    }

    public function getPageLimit()
    {
        $limit = $this->getRequest()->getParam('limit');
        return $limit>0?$limit:10;
    }

    public function getCurPage()
    {
        $page = $this->getRequest()->getParam('p');
        return $page>0?$page:1;
    }

    /**
     * {@inheritdoc}
     */
    //@codingStandardsIgnoreLine
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getCollection()->getSize() > \Magetrend\Affiliate\Helper\Data::MIN_PAGE_LIMIT) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'sales.order.history.pager'
            )->setCollection(
                $this->getCollection()
            );
            $this->setChild('pager', $pager);
            $this->getCollection()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
