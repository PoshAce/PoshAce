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

namespace Magetrend\Affiliate\Block\Adminhtml\FormBuilder\Edit\Tab\Field;

use Magento\Backend\Block\Widget;
use Magento\Catalog\Model\Product;

/**
 * Field block class
 */
class Field extends Widget
{
    /**
     * @var Product
     */
    public $productInstance;

    /**
     * @var \Magento\Framework\Object[]
     */
    public $values;

    /**
     * @var int
     */
    public $itemCount = 1;

    /**
     * Block template file
     * @var string
     */
    //@codingStandardsIgnoreLine
    protected $_template = 'formbuilder/edit/tab/field/field.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * @var Field
     */
    public $fieldCollectionFactory;

    /**
     * @var Field Option
     */
    public $fieldOptionCollectionFactory;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    public $configYesNo;

    /**
     * @var \Magetrend\Affiliate\Model\Config\Source\Field\Type
     */
    public $optionType;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Config\Model\Config\Source\Yesno $configYesNo,
        \Magetrend\Affiliate\Model\Config\Source\Field\Type $optionType,
        \Magetrend\Affiliate\Model\ResourceModel\FormBuilder\Field\CollectionFactory $fieldCollectionFactory,
        \Magetrend\Affiliate\Model\ResourceModel\FormBuilder\Option\CollectionFactory $fieldOptionCollectionFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->optionType = $optionType;
        $this->configYesNo = $configYesNo;
        $this->coreRegistry = $registry;
        $this->fieldCollectionFactory = $fieldCollectionFactory;
        $this->fieldOptionCollectionFactory = $fieldOptionCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        parent::_construct();

        $this->setCanReadPrice(true);
        $this->setCanEditPrice(true);
    }

    /**
     * @return int
     */
    public function getItemCount()
    {
        return $this->itemCount;
    }

    /**
     * @param int $itemCount
     * @return $this
     */
    public function setItemCount($itemCount)
    {
        $this->itemCount = max($this->itemCount, $itemCount);
        return $this;
    }

    /**
     * Get Product
     *
     * @return Product
     */
    public function getForm()
    {
        return $this->coreRegistry->registry('current_model');
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->productInstance = $product;
        return $this;
    }

    /**
     * Retrieve options field name prefix
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'field[options]';
    }

    /**
     * Retrieve options field id prefix
     *
     * @return string
     */
    public function getFieldId()
    {
        return 'field_option';
    }

    /**
     * Check block is readonly
     *
     * @return bool
     */
    public function isReadonly()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    //@codingStandardsIgnoreLine
    protected function _prepareLayout()
    {
        foreach ($this->optionType->getAll() as $option) {
            $this->addChild($option['name'] . '_option_type', $option['renderer']);
        }

        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getAddButtonId()
    {
        $buttonId = $this->getLayout()->getBlock('admin.product.options')->getChildBlock('add_button')->getId();
        return $buttonId;
    }

    /**
     * @return mixed
     */
    public function getTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            [
                'id' => $this->getFieldId() . '_<%- data.id %>_type',
                'class' => 'select select-product-option-type required-option-select',
            ]
        )->setName(
            $this->getFieldName() . '[<%- data.id %>][type]'
        )->setOptions(
            $this->optionType->toOptionArray()
        );

        return $select->getHtml();
    }

    /**
     * @return mixed
     */
    public function getRequireSelectHtml()
    {
        $options = $this->configYesNo->toOptionArray();
        krsort($options);

        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            ['id' => $this->getFieldId() . '_<%- data.id %>_is_require', 'class' => 'select']
        )->setName(
            $this->getFieldName() . '[<%- data.id %>][is_require]'
        )->setOptions($options);

        return $select->getHtml();
    }

    public function getAfterSelectHtml()
    {
        $options = $this->configYesNo->toOptionArray();
        krsort($options);
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            ['id' => $this->getFieldId() . '_<%- data.id %>_after_email_field', 'class' => 'select']
        )->setName(
            $this->getFieldName() . '[<%- data.id %>][after_email_field]'
        )->setOptions($options);

        return $select->getHtml();
    }

    /**
     * Retrieve html templates for different types of product custom options
     *
     * @return string
     */
    public function getTemplatesHtml()
    {
        $templates = $this->getChildHtml(
            'text_option_type'
        ) . "\n" . $this->getChildHtml(
            'select_option_type'
        ) . "\n"  . $this->getChildHtml(
            'hidden_option_type'
        ) . "\n"  . $this->getChildHtml(
            'checkbox_option_type'
        ) . "\n" ;

        return $templates;
    }

    /**
     * @return \Magento\Framework\Object[]
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getOptionValues()
    {
        if (!$this->values) {
            $this->values = [];
            $fieldData = $this->getForm()->getFields();
            if (count($fieldData) > 0) {
                foreach ($fieldData as $key => $field) {
                    $this->values[] = new \Magento\Framework\DataObject($field);
                    $this->setItemCount($field['id']);
                }
            }
        }

        return $this->values;
    }

    /**
     * Retrieve html of scope checkbox
     *
     * @param string $id
     * @param string $name
     * @param boolean $checked
     * @param string $select_id
     * @param array $containers
     * @return string
     */
    public function getCheckboxScopeHtml($id, $name, $checked = true, $select_id = '-1', array $containers = [])
    {
        $checkedHtml = '';
        if ($checked) {
            $checkedHtml = ' checked="checked"';
        }
        $selectNameHtml = '';
        $selectIdHtml = '';
        if ($select_id != '-1') {
            $selectNameHtml = '[values][' . $select_id . ']';
            $selectIdHtml = 'select_' . $select_id . '_';
        }
        $containers[] = '$(this).up(1)';
        $containers = implode(',', $containers);
        $localId = $this->getFieldId() . '_' . $id . '_' . $selectIdHtml . $name . '_use_default';
        $localName = "options_use_default[" . $id . "]" . $selectNameHtml . "[" . $name . "]";
        $useDefault =
            '<div class="field-service">'
            . '<input type="checkbox" class="use-default-control"'
            . ' name="' . $localName . '"' . 'id="' . $localId . '"'
            . ' value=""'
            . $checkedHtml
            . ' onchange="toggleSeveralValueElements(this, [' . $containers . ']);" '
            . ' />'
            . '<label for="' . $localId . '" class="use-default">'
            . '<span class="use-default-label">' . __('Use Default') . '</span></label></div>';

        return $useDefault;
    }

    /**
     * @param float $value
     * @param string $type
     * @return string
     */
    public function getPriceValue($value, $type)
    {
        if ($type == 'percent') {
            return number_format($value, 2, null, '');
        } elseif ($type == 'fixed') {
            return number_format($value, 2, null, '');
        }
    }

    /**
     * Return product grid url for custom options import formbuilder
     *
     * @return string
     */
    public function getProductGridUrl()
    {
        return $this->getUrl('catalog/*/optionsImportGrid');
    }

    /**
     * Return custom options getter URL for ajax queries
     *
     * @return string
     */
    public function getCustomOptionsUrl()
    {
        return $this->getUrl('catalog/*/customOptions');
    }
}
