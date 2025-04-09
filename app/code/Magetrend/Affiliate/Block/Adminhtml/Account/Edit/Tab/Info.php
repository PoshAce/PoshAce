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
 * Account edit info tab block class
 */
class Info extends Generic implements TabInterface
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
     * @var \Magetrend\Affiliate\Model\Config\AccountStatus
     */
    public $accountStatus;

    /**
     * @var \Magetrend\Affiliate\Helper\Data
     */
    public $moduleHelper;

    /**
     * Info constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Config\Model\Config\Source\Yesno $yesno
     * @param \Magetrend\Affiliate\Model\Config\AffiliateProgram $affiliateProgram
     * @param \Magetrend\Affiliate\Model\Config\AccountStatus $accountStatus
     * @param \Magetrend\Affiliate\Helper\Data $moduleHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        \Magetrend\Affiliate\Model\Config\AffiliateProgram $affiliateProgram,
        \Magetrend\Affiliate\Model\Config\AccountStatus $accountStatus,
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        array $data = []
    ) {
        $this->yesNo = $yesno;
        $this->affiliateProgram = $affiliateProgram;
        $this->accountStatus = $accountStatus;
        $this->moduleHelper = $moduleHelper;
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
        /**
         * @var \Magetrend\Affiliate\Model\Account $model
         */
        $model = $this->_coreRegistry->registry('current_model');
        $contentType = $this->_request->getParam('content_type');
        if (empty($contentType)) {
            $model = $this->_coreRegistry->registry('current_model');
            $contentType = $model->getContentType();
        }
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Account Information')]);

        if ($model->getId()) {
            $fieldset->addField(
                'account[entity_id]',
                'hidden',
                [
                    'name' => 'entity_id',
                    'value' => $model->getId()
                ]
            );
        }

        $fieldset->addField(
            'referral_code',
            'label',
            [
                'name' => 'referral_code',
                'label' => __('Referral Code'),
                'title' => __('Referral Code'),
                'required' => false,
                'disabled' => false
            ]
        );

        $fieldset->addField(
            'ip',
            'label',
            [
                'name' => 'ip',
                'label' => __('IP Address'),
                'title' => __('IP Address'),
                'required' => false,
                'disabled' => false
            ]
        );

        $fieldset->addField(
            'balance',
            'label',
            [
                'name' => 'balance',
                'label' => __('Total Balance'),
                'title' => __('Total Balance'),
                'required' => false,
                'disabled' => false
            ]
        );

        $fieldset->addField(
            'available_balance',
            'label',
            [
                'name' => 'available_balance',
                'label' => __('Available Balance'),
                'title' => __('Available Balance'),
                'required' => false,
                'disabled' => false
            ]
        );

        $fieldset->addField(
            'reserved_balance',
            'label',
            [
                'name' => 'reserved_balance',
                'label' => __('Reserved Balance'),
                'title' => __('Reserved Balance'),
                'required' => false,
                'disabled' => false
            ]
        );

        $fieldset->addField(
            'payout_amount',
            'label',
            [
                'name' => 'payout_amount',
                'label' => __('Lifetime Payout Amount'),
                'title' => __('Lifetime Payout Amount'),
                'required' => false,
                'disabled' => false
            ]
        );

        if ($model->getId()) {
            $data = $model->getData();
            if (!isset($data['referral_code']) || empty($data['referral_code'])) {
                $data['referral_code'] = '-- --';
            }

            $data['balance'] = $model->getFormattedBalance();
            $data['reserved_balance'] = $model->getFormattedReservedBalance();
            $data['available_balance'] = $model->getFormattedAvailableBalance();

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

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    //@codingStandardsIgnoreLine
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
