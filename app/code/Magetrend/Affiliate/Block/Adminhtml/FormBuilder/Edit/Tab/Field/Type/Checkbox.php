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

namespace Magetrend\Affiliate\Block\Adminhtml\FormBuilder\Edit\Tab\Field\Type;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Type\AbstractType;

/**
 * Checkbox field block class
 */
class Checkbox extends AbstractType
{
    /**
     * Block template file
     * @var string
     */
    //@codingStandardsIgnoreLine
    protected $_template = 'formbuilder/edit/tab/field/type/checkbox.phtml';
}
