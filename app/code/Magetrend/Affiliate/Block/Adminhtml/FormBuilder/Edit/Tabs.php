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

namespace Magetrend\Affiliate\Block\Adminhtml\FormBuilder\Edit;

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
        $this->setId('formbuilder_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Form Information'));
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
        $this->addFieldTab();
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
                    'Magetrend\Affiliate\Block\Adminhtml\FormBuilder\Edit\Tab\General'
                )->toHtml()
            ]
        );
    }

    /**
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addFieldTab()
    {

        $this->addTab(
            'field_section',
            [
                'label' => __('Form Fields'),
                'title' => __('Form Fields'),
                'content' => $this->getLayout()->createBlock(
                    'Magetrend\Affiliate\Block\Adminhtml\FormBuilder\Edit\Tab\Field'
                )->toHtml()
            ]
        );
    }
}
