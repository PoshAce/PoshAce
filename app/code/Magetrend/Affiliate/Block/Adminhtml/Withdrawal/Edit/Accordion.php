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

namespace Magetrend\Affiliate\Block\Adminhtml\Withdrawal\Edit;

/**
 * Admin page left menu
 */
class Accordion extends \Magento\Backend\Block\Widget\Accordion
{
    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        parent::_construct();
        $this->setId('withdrawal_accordion');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Withdrawal Data'));
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
        return parent::_beforeToHtml();
    }

    /**
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addGeneralTab()
    {
        $this->addItem(
            'general_section',
            [
                'label' => __('General Information'),
                'title' => __('General Information'),
                'open' => true,
                'content' => $this->getLayout()->createBlock(
                    'Magetrend\Affiliate\Block\Adminhtml\Withdrawal\Edit\Accordion\General'
                )->toHtml()
            ]
        );
    }
}
