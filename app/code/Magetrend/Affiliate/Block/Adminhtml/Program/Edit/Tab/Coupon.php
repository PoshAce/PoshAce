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

namespace Magetrend\Affiliate\Block\Adminhtml\Program\Edit\Tab;

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Backend\Block\Widget\Tab\TabInterface;

class Coupon extends Generic implements TabInterface
{
    public $coupon;

    /**
     * @var \Magetrend\Affiliate\Model\Config\Source\Format
     */
    public $codeFormat;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    public $yesNo;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magetrend\Affiliate\Model\Config\Coupon $coupon,
        \Magetrend\Affiliate\Model\Config\Source\Format $format,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->coupon = $coupon;
        $this->yesNo = $yesno;
        $this->codeFormat = $format;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    //@codingStandardsIgnoreLine
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_model');
        $contentType = $this->_request->getParam('content_type');
        if (empty($contentType)) {
            $model = $this->_coreRegistry->registry('current_model');
            $contentType = $model->getContentType();
        }
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Coupons')]);

        if ($model->getId()) {
            $fieldset->addField(
                'entity_id',
                'hidden',
                [
                    'name' => 'entity_id',
                    'value' => $model->getId()
                ]
            );
        }

        $fieldset->addField(
            'coupon_is_active',
            'select',
            [
                'name' => 'coupon_is_active',
                'label' =>  __('Is Active'),
                'title' =>  __('Is Active'),
                'value' => 0,
                'options' => $this->yesNo->toArray(),
            ]
        );

        $fieldset->addField(
            'coupon',
            'multiselect',
            [
                'name' => 'coupon',
                'label' => __('Cart Price Rules'),
                'title' => __('Cart Price Rules'),
                'required' => false,
                'disabled' => false,
                'values' => $this->coupon->toOptionArray()
            ]
        );

        $fieldset->addField(
            'coupon_length',
            'text',
            [
                'name' => 'coupon_length',
                'label' => __('Code Length'),
                'title' => __('Code Length'),
                'required' => false,
                'disabled' => false,
                'value' => 8
            ]
        );

        $fieldset->addField(
            'coupon_format',
            'select',
            [
                'name' => 'coupon_format',
                'label' =>  __('Code Format'),
                'title' =>  __('Code Format'),
                'value' => 'alphanum',
                'options' => $this->codeFormat->toArray(),
            ]
        );

        $fieldset->addField(
            'coupon_prefix',
            'text',
            [
                'name' => 'coupon_prefix',
                'label' => __('Code Prefix'),
                'title' => __('Code Prefix'),
                'required' => false,
                'disabled' => false,
                'value' => 'NS-'
            ]
        );

        $fieldset->addField(
            'coupon_suffix',
            'text',
            [
                'name' => 'coupon_suffix',
                'label' => __('Code Suffix'),
                'title' => __('Code Suffix'),
                'required' => false,
                'disabled' => false,
            ]
        );

        $fieldset->addField(
            'coupon_dash',
            'text',
            [
                'name' => 'coupon_dash',
                'label' => __('Dash'),
                'title' => __('Dash'),
                'note' => __('Add Dash Every Time after X Symbols'),
                'required' => false,
                'disabled' => false,
                'value' => 4
            ]
        );

        if ($model->getId()) {
            $form->setValues($model->getData());
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('General Settings');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('General Settings');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
