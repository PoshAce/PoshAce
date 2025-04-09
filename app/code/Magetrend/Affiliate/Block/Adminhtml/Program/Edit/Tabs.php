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

namespace Magetrend\Affiliate\Block\Adminhtml\Program\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @var \Magento\Framework\Registry
     */
    public $registry;

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
        $this->registry = $registry;
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
        $this->setId('program_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Affiliate Program'));
    }

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return $this
     */
    //@codingStandardsIgnoreLine
    protected function _beforeToHtml()
    {
        $contentType = $this->_request->getParam('content_type');
        if (empty($contentType)) {
            $model = $this->registry->registry('current_model');
            $contentType = $model->getType();
        }

        $this->addGeneralTab();
        if ($contentType == \Magetrend\Affiliate\Model\Program::TYPE_PAY_PER_SALE) {
            $this->addPayPerSaleCommissionTab();
            $this->addCouponTab();
        }

        if ($contentType == \Magetrend\Affiliate\Model\Program::TYPE_PAY_PER_CLICK) {
            $this->addPayPerClickCommissionTab();
        }

        return parent::_beforeToHtml();
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
                    'Magetrend\Affiliate\Block\Adminhtml\Program\Edit\Tab\General'
                )->toHtml()
            ]
        );
    }

    public function addPayPerSaleCommissionTab()
    {
        $this->addTab(
            'commission_section',
            [
                'label' => __('Commissions'),
                'title' => __('Commissions'),
                'active' => false,
                'content' => $this->getLayout()->createBlock(
                    'Magetrend\Affiliate\Block\Adminhtml\Program\Edit\Tab\PayPerSaleCommission'
                )->toHtml()
            ]
        );
    }

    public function addPayPerClickCommissionTab()
    {
        $this->addTab(
            'commission_section',
            [
                'label' => __('Commissions'),
                'title' => __('Commissions'),
                'active' => false,
                'content' => $this->getLayout()->createBlock(
                    'Magetrend\Affiliate\Block\Adminhtml\Program\Edit\Tab\PayPerClickCommission'
                )->toHtml()
            ]
        );
    }

    public function addCouponTab()
    {
        $this->addTab(
            'coupon_section',
            [
                'label' => __('Coupons'),
                'title' => __('Coupons'),
                'active' => false,
                'content' => $this->getLayout()->createBlock(
                    'Magetrend\Affiliate\Block\Adminhtml\Program\Edit\Tab\Coupon'
                )->toHtml()
            ]
        );
    }
}
