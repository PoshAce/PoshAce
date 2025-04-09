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

namespace Magetrend\Affiliate\Block\Adminhtml\Withdrawal\Edit\Accordion;

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Backend\Block\Widget\Tab\TabInterface;

class General extends Generic
{
    public $status;

    public $accountFactory;

    public $moduleHelper;

    /**
     * General constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magetrend\Affiliate\Model\Config\WithdrawalStatus $status
     * @param \Magetrend\Affiliate\Model\AccountFactory $accountFactory
     * @param \Magetrend\Affiliate\Helper\Data $moduleHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magetrend\Affiliate\Model\Config\WithdrawalStatus $status,
        \Magetrend\Affiliate\Model\AccountFactory $accountFactory,
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        array $data = []
    ) {
        $this->status = $status;
        $this->accountFactory = $accountFactory;
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
        $model = $this->_coreRegistry->registry('current_model');
        $contentType = $this->_request->getParam('content_type');
        if (empty($contentType)) {
            $model = $this->_coreRegistry->registry('current_model');
            $contentType = $model->getContentType();
        }
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('withdrawal_');

        $fieldset = $form->addFieldset('base_fieldset', []);

        if ($model->getId()) {
            $fieldset->addField(
                'entity_id',
                'hidden',
                [
                    'name' => 'entity_id',
                    'value' => ''
                ]
            );
        }

        $fieldset->addField('action', 'hidden', ['name' => 'action', 'value' => '0']);

        $fieldset->addField(
            'fullname',
            'label',
            [
                'name' => 'fullname',
                'label' => __('Full Name'),
                'title' => __('Full Name'),
            ]
        );

        $fieldset->addField(
            'balance',
            'label',
            [
                'name' => 'balance',
                'label' => __('Account Balance'),
                'title' => __('Account Balance'),
            ]
        );

        $fieldset->addField(
            'paypal_account_email',
            'label',
            [
                'name' => 'paypal_account_email',
                'label' => __('Paypal Account Email'),
                'title' => __('Paypal Account Email'),
            ]
        );

        $fieldset->addField(
            'amount_request',
            'label',
            [
                'name' => 'amount_request',
                'label' => __('Requested Amount'),
                'title' => __('Requested Amount'),
            ]
        );

        $fieldset->addField(
            'amount_paid',
            'text',
            [
                'name' => 'amount_paid',
                'label' => __('Payout Amount'),
                'title' => __('Payout Amount'),
                'required' => false,
                'disabled' => false
            ]
        );

        $fieldset->addField(
            'comment',
            'textarea',
            [
                'name' => 'comment',
                'label' => __('Comments for Customer'),
                'title' => __('Comments for Customer'),
                'required' => false,
                'disabled' => false
            ]
        );

        /**
        $fieldset->addField(
            'submit',
            'button',
            [
                'label' => '',
                'title' => 'Update',
                'name' => 'update',
                'value' => __('Payout'),
                'class' => 'action-primary submit-form'
            ]
        );**/

        $fieldset->addType(
            'buttons',
            '\Magetrend\Affiliate\Block\Adminhtml\Account\View\Renderer\Buttons'
        );

        $fieldset->addField(
            'submit',
            'buttons',
            [
                'name'  => 'actions',
                'label' => '',
                'title' => '',
                'buttons' => [
                    'button_1' => [
                        'label' => __('Payout'),
                        'class' => 'action-primary submit-form-button',
                        'name' => 'payout',
                    ],

                    'button_2' => [
                        'label' => __('Cancel'),
                        'name' => 'cancel',
                        'class' => 'action-secondary submit-form-button',
                    ]
                ]
            ]
        );

        if ($model->getId()) {
            $affiliate = $this->accountFactory->create()->load($model->getReferralId());
            $customer = $affiliate->getCustomer();
            $amount = $this->moduleHelper->formatPrice($model->getAmountRequest(), $model->getCurrency());
            $additionalData = [
                'fullname' => $customer->getFirstname().' '.$customer->getLastname(),
                'paypal_account_email' => $affiliate->getPaypalAccountEmail(),
                'amount_paid' => number_format($model->getAmountRequest(), 2, '.', ''),
                'amount_request' => $amount,
                'submit' => __('Payout'),
                'balance' => __(
                    'Total: %1  Available: %2  Reserved: %3',
                    $this->moduleHelper->formatPrice($affiliate->getBalance(), $affiliate->getCurrency()),
                    $this->moduleHelper->formatPrice($affiliate->getAvailableBalance(), $affiliate->getCurrency()),
                    $this->moduleHelper->formatPrice($affiliate->getReservedBalance(), $affiliate->getCurrency())
                )
            ];
            $form->setValues(array_merge($model->getData(), $additionalData));
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
