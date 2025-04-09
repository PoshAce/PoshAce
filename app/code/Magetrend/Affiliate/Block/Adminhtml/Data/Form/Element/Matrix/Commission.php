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

namespace Magetrend\Affiliate\Block\Adminhtml\Data\Form\Element\Matrix;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Commission matrix class
 */
class Commission extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    public $type;

    /**
     * Get activation options.
     *
     * @return \Magently\Tutorial\Block\Adminhtml\Form\Field\Activation
     */
    public function getCommissionTypeRenderer()
    {
        if (!$this->type) {
            $this->type = $this->getLayout()->createBlock(
                '\Magetrend\Affiliate\Block\Adminhtml\Data\Form\Element\Matrix\Commission\Type',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->type;
    }

    /**
     * Prepare to render.
     *
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _prepareToRender()
    {
        $this->addColumn('tier', ['label' => __('Tier (N-th of Order)')]);
        $this->addColumn(
            'type',
            [
                'label' => __('Commission Type'),
                'renderer' => $this->getCommissionTypeRenderer()
            ]
        );

        $this->addColumn('rate', ['label' => __('Commission Rate')]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare existing row data object.
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $options = [];
        $commissionTypeAttribute = $row->getData('type');

        $key = 'option_' . $this->getCommissionTypeRenderer()->calcOptionHash($commissionTypeAttribute);
        $options[$key] = 'selected="selected"';

        $row->setData('option_extra_attrs', $options);
    }
}
