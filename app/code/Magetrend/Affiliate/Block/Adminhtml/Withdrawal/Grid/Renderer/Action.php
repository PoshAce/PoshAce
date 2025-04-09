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

namespace  Magetrend\Affiliate\Block\Adminhtml\Withdrawal\Grid\Renderer;

/**
 * Action column renderer
 *
 * @category MageTrend
 * @package  Magetend/PdfTemplates
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-pdf-invoice-pro
 */
class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action
{
    /**
     * Render grid column
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $actions = [];

        if ($row->getStatus() == \Magetrend\Affiliate\Model\Withdrawal::STATUS_NEW) {
            $actions[] = [
                'url' => '#',
                'caption' => __('Manage'),
                'class' => 'open-modal-edit',
                'data-form' => $this->getUrl('*/*/edit', ['id' => $row->getId()])
            ];
        }

        if (empty($actions) || !is_array($actions)) {
            return '&nbsp;';
        }

        $out = '';
        foreach ($actions as $action) {
            if (is_array($action)) {
                $out.= $this->_toLinkHtml($action, $row);
            }
        }

        return $out;
    }
}
