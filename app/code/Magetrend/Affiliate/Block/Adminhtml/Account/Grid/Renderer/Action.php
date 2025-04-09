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

namespace  Magetrend\Affiliate\Block\Adminhtml\Account\Grid\Renderer;

use Magetrend\Affiliate\Model\Account;

/**
 * Action column renderer
 *
 * @category MageTrend
 * @package  Magetend/PdfTemplates
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-pdf-invoice-pro
 */
class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
        $status = $row->getStatus();
        if (in_array($status, [Account::STATUS_NEW, Account::STATUS_CANCELED])) {
            $actions[] = [
                'url' => '#',
                'caption' => __('View Application'),
                'class' => 'open-modal-edit',
                'data-form' => $this->getUrl('*/*/view', ['id' => $row->getId()])
            ];
        }

        if (in_array($status, [Account::STATUS_ACTIVE, Account::STATUS_INACTIVE])) {
            $actions[] = [
                'url' => $this->getUrl('*/*/edit', ['id' => $row->getId()]),
                'caption' => __('Edit'),
            ];
        }

        if (in_array($status, [Account::STATUS_CANCELED])) {
            $actions[] = [
            'url' => $this->getUrl('*/*/delete', ['id' => $row->getId()]),
                'caption' => __('Delete'),
            ];
        }

        $html = '';
        if (!empty($actions)) {
            foreach ($actions as $action) {
                $class = '';
                $dataForm = '';
                if (isset($action['class'])) {
                    $class = ' class="'.$action['class'].'"';
                }

                if (isset($action['data-form'])) {
                    $dataForm = ' data-form="'.$action['data-form'].'"';
                }

                $html.= '<a'.$class.$dataForm.' href="'.$action['url'].'">'.$action['caption'].'</a><br/>';
            }
        }

        return $html;
    }
}
