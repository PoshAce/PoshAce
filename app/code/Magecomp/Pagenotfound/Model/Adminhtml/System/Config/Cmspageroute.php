<?php

namespace Magecomp\Pagenotfound\Model\Adminhtml\System\Config;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
class Cmspageroute implements \Magento\Framework\Option\ArrayInterface
{
    protected $_pageCollection;

    protected $_options;

    public function __construct( CollectionFactory $collectionFactory )
    {
        $this->_pageCollection = $collectionFactory;
    }

    public function toOptionArray()
    {
        $res = array();
        $collection = $this->_pageCollection->create();
        $collection->addFieldToFilter('is_active' , \Magento\Cms\Model\Page::STATUS_ENABLED);
        foreach($collection as $page){
            $data['value'] = $page->getData('identifier');
            $data['label'] = $page->getData('title');
            $res[] = $data;
        }
        return $res;
    }
}
