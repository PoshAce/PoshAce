<?php

namespace Magecomp\Mobilelogin\Block\Adminhtml\System\Config;

class Update extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_template = 'Magecomp_Mobilelogin::update.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }

    public function getAjaxUrl()
    {
     return $this->getUrl('mobilelogin/index/update').'website/'.$this->_request->getParam('website');
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            array(
                'id' => 'update',
                'label' => __('Update'),
                'class' => 'primary'
            )
        );

        return $button->toHtml();
    }
}
