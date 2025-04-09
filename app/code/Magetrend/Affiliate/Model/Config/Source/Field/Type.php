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

namespace Magetrend\Affiliate\Model\Config\Source\Field;

class Type implements \Magento\Framework\Option\ArrayInterface
{

    public function getAll()
    {
        return [
            [
                'label' => 'Text',
                'renderer' => '\Magetrend\Affiliate\Block\Adminhtml\FormBuilder\Edit\Tab\Field\Type\Text',
                'name' => 'text',
                'types' => [
                    ['value' => 'field',  'label' => __('Text Field')],
                    ['value' => 'area',    'label' => __('Textarea')],
                ]
            ],
            [
                'name' => 'select',
                'label' => 'Select',
                'renderer' => '\Magetrend\Affiliate\Block\Adminhtml\FormBuilder\Edit\Tab\Field\Type\Select',
                'types' => [
                    ['value' => 'drop_down',    'label' => __('Drop Down')],
                ]
            ],
            [
                'name' => 'checkbox',
                'label' => 'Checkbox',
                'renderer' => '\Magetrend\Affiliate\Block\Adminhtml\FormBuilder\Edit\Tab\Field\Type\Checkbox',
                'types' => [
                    ['value' => 'checkbox',    'label' => __('Checkbox')],
                ]
            ],
            [
                'name' => 'hidden',
                'label' => 'Hidden',
                'renderer' => '\Magetrend\Affiliate\Block\Adminhtml\FormBuilder\Edit\Tab\Field\Type\Hidden',
                'types' => [
                    ['value' => 'hidden',    'label' => __('Hidden')],
                ]
            ],
        ];
    }
    public function toOptionArray()
    {
        $groups = [['value' => '', 'label' => __('-- Please select --')]];

        foreach ($this->getAll() as $option) {
            $types = [];
            foreach ($option['types'] as $type) {
                $types[] = ['label' => __($type['label']), 'value' => $type['value']];
            }
            if ($types) {
                $groups[] = ['label' => __($option['label']), 'value' => $types];
            }
        }

        return $groups;
    }
}
