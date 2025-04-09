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

namespace Magetrend\Affiliate\Block\Adminhtml\Account\View\Accordion;

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Account view page general tab block class
 */
class General extends Generic
{
    /**
     * @var \Magetrend\Affiliate\Model\Config\WithdrawalStatus
     */
    public $status;

    /**
     * @var \Magetrend\Affiliate\Model\AccountFactory
     */
    public $accountFactory;

    /**
     * @var \Magetrend\Affiliate\Helper\Data
     */
    public $moduleHelper;

    /**
     * @var \Magetrend\Affiliate\Model\FormBuilder\FormFactory
     */
    public $formBuilderForm;

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
        \Magetrend\Affiliate\Model\FormBuilder\FormFactory $formBuilderForm,
        array $data = []
    ) {
        $this->status = $status;
        $this->accountFactory = $accountFactory;
        $this->moduleHelper = $moduleHelper;
        $this->formBuilderForm = $formBuilderForm;
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

        $fieldset = $form->addFieldset('base_fieldset', []);

        $registrationFormId = $this->moduleHelper->getRegistrationFormId($model->getStoreId());
        $registrationForm = $this->formBuilderForm->create();
        $registrationForm->load($registrationFormId);

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

        if ($registrationForm->getId()) {
            $fields = $registrationForm->getFields();
            foreach ($fields as $field) {
                $fieldset->addField(
                    $field['name'],
                    'label',
                    [
                        'name' => $field['name'],
                        'label' => __($field['label']),
                        'title' => __($field['label']),
                    ]
                );
            }
        }

        if ($model->getId()) {
            $additionalData = [];
            $registrationFormData = $model->getRegistrationFormData();
            if ($registrationFormData->getSize() > 0) {
                foreach ($registrationFormData as $row) {
                    $additionalData[$row->getFieldKey()] = $row->getFieldValue();
                }
            }

            if ($registrationForm->getId()) {
                $fields = $registrationForm->getFields();
                foreach ($fields as $field) {
                    if ($field['type'] != 'checkbox') {
                        continue;
                    }
                    if (isset($additionalData[$field['name']])) {
                        $additionalData[$field['name']] = (string)__('Checked');
                    } else {
                        $additionalData[$field['name']] = (string)__('Not Checked');
                    }
                }
            }

            $form->setValues(array_merge($model->getData(), $additionalData));

            $fieldset->addType(
                'buttons',
                '\Magetrend\Affiliate\Block\Adminhtml\Account\View\Renderer\Buttons'
            );

            $fieldset->addField(
                'actions',
                'buttons',
                [
                    'name'  => 'actions',
                    'label' => '',
                    'title' => '',
                    'buttons' => [
                        'button_1' => [
                            'label' => __('Confirm & Create Account'),
                            'url' => $this->_urlBuilder->getUrl('*/*/confirm', ['id' => $model->getId()]),
                            'class' => 'action-primary',
                        ],

                        'button_2' => [
                            'label' => __('Cancel Application'),
                            'url' => $this->_urlBuilder->getUrl('*/*/cancel', ['id' => $model->getId()]),
                            'class' => 'action-secondary',
                        ]
                    ]
                ]
            );
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
