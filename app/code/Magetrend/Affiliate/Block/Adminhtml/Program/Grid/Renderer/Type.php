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

namespace  Magetrend\Affiliate\Block\Adminhtml\Program\Grid\Renderer;

/**
 * Action column renderer
 *
 * @category MageTrend
 * @package  Magetend/PdfTemplates
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-pdf-invoice-pro
 */
class Type extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    public $programType;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magetrend\Affiliate\Model\Config\ProgramType $programType,
        array $data = []
    ) {
        $this->programType = $programType;
        parent::__construct($context, $data);
    }

    /**
     * Render grid column
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $index = $this->getColumn()->getIndex();
        $value = $row->getData($index);
        $options = $this->programType->toArray();
        if (isset($options[$value])) {
            return $options[$value];
        }
        return parent::render($row);
    }
}
