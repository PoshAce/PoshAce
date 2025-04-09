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

namespace Magetrend\Affiliate\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    private $programFactory;

    private $ruleFactory;

    private $formFactory;

    private $fieldFactory;

    private $optionFactory;

    private $pageFactory;

    private $date;

    private $storeManager;

    private $moduleHelper;

    private $scopeConfig;

    private $configWriter;

    private $program1;

    private $program2;

    private $tcPage = null;

    private $formPage = null;

    private $cartPriceRule = null;

    private $registrationForm;

    private $state;

    /**
     * InstallData constructor.
     * @param \Magento\Framework\App\State $state
     * @param EavSetupFactory $eavSetupFactory
     * @param \Magetrend\Affiliate\Model\ProgramFactory $programFactory
     * @param \Magetrend\Affiliate\Model\FormBuilder\FormFactory $formFactory
     * @param \Magetrend\Affiliate\Model\FormBuilder\FieldFactory $fieldFactory
     * @param \Magetrend\Affiliate\Model\FormBuilder\OptionFactory $optionFactory
     * @param \Magento\SalesRule\Model\RuleFactory $ruleFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        EavSetupFactory $eavSetupFactory,
        \Magetrend\Affiliate\Model\ProgramFactory $programFactory,
        \Magetrend\Affiliate\Model\FormBuilder\FormFactory $formFactory,
        \Magetrend\Affiliate\Model\FormBuilder\FieldFactory $fieldFactory,
        \Magetrend\Affiliate\Model\FormBuilder\OptionFactory $optionFactory,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Cms\Model\PageFactory $pageFactory
    ) {
        $this->state = $state;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->programFactory = $programFactory;
        $this->ruleFactory = $ruleFactory;
        $this->date = $date;
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->formFactory = $formFactory;
        $this->fieldFactory = $fieldFactory;
        $this->optionFactory = $optionFactory;
        $this->pageFactory = $pageFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->state->setAreaCode('adminhtml');
        $this->createAffiliatePrograms();
        $this->createRegistrationForm();
        $this->createTCPage();
        $this->createRegistrationPage();
        $this->updateConfig();
    }

    public function updateConfig()
    {
        $this->configWriter->save(
            \Magetrend\Affiliate\Helper\Data::XML_REGISTRATION_DEFAULT_AFFILIATE_PROGRAM,
            $this->program1->getId().','.$this->program2->getId(),
            'default',
            0
        );

        $this->configWriter->save(
            \Magetrend\Affiliate\Helper\Data::XML_PATH_AFFILIATE_REGISTRATION_FORM,
            $this->registrationForm->getId(),
            'default',
            0
        );
    }

    public function createRegistrationForm()
    {
        $this->registrationForm = $this->formFactory->create();
        $this->registrationForm->setData([
                'name' => 'Default Registration Form'
            ])
            ->save();

        $formId = $this->registrationForm->getId();
        $this->fieldFactory->create()
            ->setData([
                'form_id' => $formId,
                'type' => 'field',
                'name' => 'fullname',
                'label' => 'Full Name',
                'is_required' => 1,
                'position' => 1,
                'after_email_field' => 0,
                'error_message' => '[]',
            ])->save();

        $this->fieldFactory->create()
            ->setData([
                'form_id' => $formId,
                'type' => 'field',
                'name' => 'website',
                'label' => 'Website',
                'is_required' => 0,
                'position' => 2,
                'after_email_field' => 1,
                'error_message' => '[]',
            ])->save();

        $this->fieldFactory->create()
            ->setData([
                'form_id' => $formId,
                'type' => 'area',
                'name' => 'message',
                'label' => 'Message',
                'is_required' => 0,
                'position' => 3,
                'after_email_field' => 1,
                'error_message' => '[]',
            ])->save();

        $field4 = $this->fieldFactory->create()
            ->setData([
                'form_id' => $formId,
                'type' => 'drop_down',
                'name' => 'type',
                'label' => 'Type',
                'is_required' => 0,
                'position' => 4,
                'after_email_field' => 1,
                'error_message' => '[]',
            ])->save();

        $this->fieldFactory->create()
            ->setData([
                'form_id' => $formId,
                'type' => 'field',
                'name' => 'email',
                'label' => 'Email',
                'is_required' => 1,
                'position' => 1,
                'after_email_field' => 0,
                'error_message' => '[]',
            ])->save();

        $msg = '{"is_required":"Please indicate that you have read and agree to the Affiliate Terms and Conditions"}';
        $frontendLabel = 'I agree to the '
            .'<a target="_blank" href="/affiliate-terms-and-conditions">Affiliate Terms & Conditions</a>';
        $this->fieldFactory->create()
            ->setData([
                'form_id' => $formId,
                'type' => 'checkbox',
                'name' => 'agree',
                'label' => 'I Agree',
                'frontend_label' => $frontendLabel,
                'is_required' => 1,
                'position' => 5,
                'after_email_field' => 1,
                'error_message' => $msg,
            ])->save();

        $this->optionFactory->create()
            ->setData([
                'field_id' => $field4->getId(),
                'value' => 'Blogger',
                'label' =>'Blogger',
                'position' => 0,
            ])->save();

        $this->optionFactory->create()
            ->setData([
                'field_id' => $field4->getId(),
                'value' => 'Marketing Agency',
                'label' =>'Marketing Agency',
                'position' => 1,
            ])->save();

        $this->optionFactory->create()
            ->setData([
                'field_id' => $field4->getId(),
                'value' => 'Forum Master',
                'label' =>'Forum Master',
                'position' => 2,
            ])->save();

        $this->optionFactory->create()
            ->setData([
                'field_id' => $field4->getId(),
                'value' => 'Ecommerce Enthusiast',
                'label' =>'Ecommerce Enthusiast',
                'position' => 3,
            ])->save();
    }

    public function createAffiliatePrograms()
    {
        $this->program1 = $this->programFactory->create()
            ->setData([
                'is_active' => 1,
                'name' => 'Default Pay Per Sale Program',
                'description' => 'Pay per Sale (20%)',
                'type' => 'pay_per_sale',
                'currency' => $this->getCurrency(),
                'commission' =>
                    '{"_1537776370674_674":{"tier":"1","type":"percentage_from_total","rate":"20"},"__empty":""}',
                'coupon' => '["'.$this->getCartPriceRule()->getId().'"]',
                'coupon_is_active' => 1,
                'coupon_length' => '8',
                'coupon_format' => 'alphanum',
                'coupon_prefix' => '',
                'coupon_suffix' => '',
                'coupon_dash' => 4,
                'is_deleted' => 0,
            ])
            ->save();

        $this->program2 = $this->programFactory->create()
            ->setData([
                'is_active' => 1,
                'name' => 'Default Program for Clicks',
                'description' => '0.05 for click',
                'type' => 'pay_per_click',
                'currency' => $this->getCurrency(),
                'fixed_commission' => '0.05',
                'commission' => null,
                'coupon' => null,
                'coupon_is_active' => 0,
                'coupon_length' => '8',
                'coupon_format' => 'alphanum',
                'coupon_prefix' => '',
                'coupon_suffix' => '',
                'coupon_dash' => '',
                'is_deleted' => 0,
            ])
            ->save();
    }

    public function getCurrency()
    {
        return $this->scopeConfig->getValue(
            \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            null
        );
    }

    public function getCartPriceRule()
    {
        if ($this->cartPriceRule === null) {
            $this->cartPriceRule = $this->ruleFactory->create()
                ->setData([
                    'name' => 'Affiliate Program 10%',
                    'description' => '10% off from order total',
                    'from_date' => $this->date->gmtDate('Y-m-d'),
                    'to_date' => $this->date->gmtDate('Y-m-d', strtotime('+1 year')),
                    'is_advanced' => 1,
                    'simple_action' => 'by_percent',
                    'discount_amount' => '10',
                    'times_used' => '1',
                    'coupon_type' => '2',
                    'use_auto_generation' => '1',
                    'uses_per_customer' => '1',
                    'uses_per_coupon' => '100000',
                    'simple_free_shipping' => '0',
                ])->save();
        }

        return $this->cartPriceRule;
    }

    public function createTCPage()
    {
        if ($this->tcPage === null) {
            $this->tcPage = $this->pageFactory->create();
            $this->tcPage->load('affiliate-terms-and-conditions', 'identifier');

            if (!$this->tcPage->getId()) {
                $this->tcPage->setTitle('AFFILIATE TERMS AND CONDITIONS')
                    ->setIdentifier('affiliate-terms-and-conditions')
                    ->setIsActive(true)
                    ->setPageLayout('1column')
                    ->setStores([0])
                    ->setContent('<p>AFFILIATE TERMS AND CONDITIONS</p>')
                    ->save();
            }
        }
        return $this->tcPage;
    }

    public function createRegistrationPage()
    {
        $affilatePage = $this->pageFactory->create();
        $affilatePage->load('affiliates', 'identifier');
        if (!$affilatePage->getId()) {
            $this->tcPage->setTitle('Join to our affiliate program today!')
                ->setIdentifier('affiliates')
                ->setIsActive(true)
                ->setContentHeading('Affiliate Program')
                ->setPageLayout('1column')
                ->setStores([0])
                ->setContent('<p>{{block class="Magetrend\Affiliate\Block\Registration\Form" }}</p>')
                ->save();
        }
    }
}
