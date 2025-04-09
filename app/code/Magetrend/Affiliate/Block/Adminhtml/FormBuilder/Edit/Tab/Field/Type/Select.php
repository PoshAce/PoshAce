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

namespace Magetrend\Affiliate\Block\Adminhtml\FormBuilder\Edit\Tab\Field\Type;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Type\AbstractType;

/**
 * Select field block class
 */
class Select extends AbstractType
{
    /**
     * Block template file
     * @var string
     */
    //@codingStandardsIgnoreLine
    protected $_template = 'formbuilder/edit/tab/field/type/select.phtml';

    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        parent::_construct();

        $this->setCanEditPrice(true);
        $this->setCanReadPrice(true);
    }

    /**
     * {@inheritdoc}
     */
    //@codingStandardsIgnoreLine
    protected function _prepareLayout()
    {
        $this->addChild(
            'add_select_row_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Add New Row'),
                'class' => 'add add-select-row',
                'id' => 'field_option_<%- data.option_id %>_add_select_row'
            ]
        );

        $this->addChild(
            'delete_select_row_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Delete Row'),
                'class' => 'delete delete-select-row icon-btn',
                'id' => 'field_option_<%- data.id %>_select_<%- data.select_id %>_delete'
            ]
        );

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_select_row_button');
    }

    /**
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_select_row_button');
    }

    /**
     * Return select input for price type
     *
     * @param string $extraParams
     * @return string
     */
    public function getPriceTypeSelectHtml($extraParams = '')
    {
        $this->getChildBlock(
            'option_price_type'
        )->setData(
            'id',
            'field_option_<%- data.id %>_select_<%- data.select_id %>_price_type'
        )->setName(
            'field[options][<%- data.id %>][values][<%- data.select_id %>][price_type]'
        )->setExtraParams($extraParams);

        return parent::getPriceTypeSelectHtml();
    }
}
