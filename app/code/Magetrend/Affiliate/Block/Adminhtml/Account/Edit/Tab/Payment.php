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

namespace Magetrend\Affiliate\Block\Adminhtml\Account\Edit\Tab;

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Account edit payment tab block class
 */
class Payment extends Generic implements TabInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    public $yesNo;

    /**
     * @var \Magetrend\Affiliate\Model\Config\AffiliateProgram
     */
    public $affiliateProgram;

    /**
     * Account edit payment tab block class
     */
    public $accountStatus;

    /**
     * Payment constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Config\Model\Config\Source\Yesno $yesno
     * @param \Magetrend\Affiliate\Model\Config\AffiliateProgram $affiliateProgram
     * @param \Magetrend\Affiliate\Model\Config\AccountStatus $accountStatus
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        \Magetrend\Affiliate\Model\Config\AffiliateProgram $affiliateProgram,
        \Magetrend\Affiliate\Model\Config\AccountStatus $accountStatus,
        array $data = []
    ) {
        $this->yesNo = $yesno;
        $this->affiliateProgram = $affiliateProgram;
        $this->accountStatus = $accountStatus;
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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Payment Settings')]);

        $fieldset->addField(
            'paypal_account_email',
            'text',
            [
                'name' => 'account[paypal_account_email]',
                'label' => __('PayPal Account Email'),
                'title' => __('PayPal Account Email'),
                'required' => false,
                'disabled' => false
            ]
        );

        if ($model->getId()) {
            $data = $model->getData();
            $form->setValues($data);
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
        return __('Payment Settings');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Payment Settings');
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
