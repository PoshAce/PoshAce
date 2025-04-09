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

namespace Magetrend\Affiliate\Block\Adminhtml\Account\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * Tabs constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        parent::_construct();
        $this->setId('account_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Account Data'));
    }

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return $this
     */
    //@codingStandardsIgnoreLine
    protected function _beforeToHtml()
    {
        $this->addGeneralTab();
        $this->addProgramTab();
        $this->paymentTab();
        $this->addClickReportTab();
        $this->addOrderReportTab();
        $this->addBalanceReportTab();
        return parent::_beforeToHtml();
    }

    /**
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addInfoTab()
    {
        $this->addTab(
            'info_section',
            [
                'label' => __('Account Information'),
                'title' => __('Account Information'),
                'active' => true,
                'content' => $this->getLayout()->createBlock(
                    'Magetrend\Affiliate\Block\Adminhtml\Account\Edit\Tab\Info'
                )->toHtml()
            ]
        );
    }

    /**
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function paymentTab()
    {
        $this->addTab(
            'info_section',
            [
                'label' => __('Payment Settings'),
                'title' => __('Payment Settings'),
                'active' => false,
                'content' => $this->getLayout()->createBlock(
                    'Magetrend\Affiliate\Block\Adminhtml\Account\Edit\Tab\Payment'
                )->toHtml()
            ]
        );
    }

    /**
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addGeneralTab()
    {
        $this->addTab(
            'general_section',
            [
                'label' => __('General Settings'),
                'title' => __('General Settings'),
                'active' => true,
                'content' => $this->getLayout()->createBlock(
                    'Magetrend\Affiliate\Block\Adminhtml\Account\Edit\Tab\General'
                )->toHtml()
            ]
        );
    }

    /**
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addProgramTab()
    {
        $this->addTab(
            'program_section',
            [
                'label' => __('Affiliate Programs'),
                'title' => __('Affiliate Programs'),
                'active' => false,
                'content' => $this->getLayout()->createBlock(
                    'Magetrend\Affiliate\Block\Adminhtml\Account\Edit\Tab\Program'
                )->toHtml()
            ]
        );
    }

    /**
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addClickReportTab()
    {
        $this->addTab(
            'click_report_section',
            [
                'label' => __('Clicks Report'),
                'title' => __('Clicks Report'),
                'active' => false,
                'content' => $this->getLayout()->createBlock(
                    'Magetrend\Affiliate\Block\Adminhtml\Account\Edit\Tab\Report\Click'
                )->toHtml()
            ]
        );
    }

    /**
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addOrderReportTab()
    {
        $this->addTab(
            'order_report_section',
            [
                'label' => __('Orders Report'),
                'title' => __('Orders Report'),
                'active' => false,
                'content' => $this->getLayout()->createBlock(
                    'Magetrend\Affiliate\Block\Adminhtml\Account\Edit\Tab\Report\Order'
                )->toHtml()
            ]
        );
    }

    /**
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addBalanceReportTab()
    {
        $this->addTab(
            'balance_report_section',
            [
                'label' => __('Balance Report'),
                'title' => __('Balance Report'),
                'active' => false,
                'content' => $this->getLayout()->createBlock(
                    'Magetrend\Affiliate\Block\Adminhtml\Account\Edit\Tab\Report\Balance'
                )->toHtml()
            ]
        );
    }
}
