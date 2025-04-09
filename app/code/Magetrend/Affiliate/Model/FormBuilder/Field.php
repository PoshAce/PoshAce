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

namespace Magetrend\Affiliate\Model\FormBuilder;

use \Magento\Framework\Model\AbstractModel;

class Field extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init('Magetrend\Affiliate\Model\ResourceModel\FormBuilder\Field');
    }

    public function isValid()
    {
        $fieldName = $this->getName();
        if (empty($fieldName) || !ctype_alpha($fieldName)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Bad field name. It must be not empty and only alpha')
            );
        }

        $fieldType = $this->getType();
        if (empty($fieldType)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Bad field type'));
        }
    }
}
