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

class PayPerSaleCommission extends Generic implements TabInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    public $yesNo;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        array $data = []
    ) {
        $this->yesNo = $yesno;
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
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Pay per Sale Program Commissions')]);
        $fieldset->addType(
            'matrix',
            '\Magetrend\Affiliate\Block\Adminhtml\Data\Form\Element\Matrix'
        );

        $fieldset->addField(
            'commission',
            'matrix',
            [
                'name' => 'commission',
                'label' => __('Commission Rate'),
                'title' => __('Commission Rate'),
                'required' => false,
                'disabled' => false,
                'element_block' => 'Magetrend\Affiliate\Block\Adminhtml\Data\Form\Element\Matrix\Commission'
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
