<?php
namespace Magecomp\Webpimageconvert\Block\Systemconfig;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Button extends Field
{
    protected $_template = 'Magecomp_Webpimageconvert::button.phtml';
    public function __construct(
                    Context $context,
                    array $data = []
                )
    {
        parent::__construct($context, $data);
    }

    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }
    
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData(['id' => 'btn_id', 'label' => __('Delete Webp Images'),]);
        return $button->toHtml();
    }
    public function getAjaxUrl()
    {
        return $this->getUrl('webpconvert/webpimage/delete');
    }
}
