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

namespace Magetrend\Affiliate\Block\Adminhtml;

use Magento\Core\Model\ObjectManager;

class Program extends \Magento\Backend\Block\Widget\Grid\Container
{
    public $programType;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magetrend\Affiliate\Model\Config\ProgramType $programType,
        array $data = []
    ) {
        $this->programType = $programType;
        parent::__construct($context, $data);
    }

    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_controller = 'popup_index';
        $this->_headerText = __('Affiliate Programs');
        parent::_construct();
    }

    /**
     * {@inheritdoc}
     */
    //@codingStandardsIgnoreLine
    protected function _prepareLayout()
    {
        $this->removeButton('add');
        $this->addButton('add', [
            'id' => 'add_new_blog_post',
            'label' => '&nbsp;'.__('Add New Program').'&nbsp;&nbsp;&nbsp;&nbsp;',
            'class' => 'add',
            'button_class' => '',
            'class_name' => 'Magento\Backend\Block\Widget\Button\SplitButton',
            'options' => $this->getAddButtonOptions(),
        ]);
        return parent::_prepareLayout();
    }

    public function getAddButtonOptions()
    {
        $splitButtonOptions = [];
        $options = $this->programType->toArray();
        foreach ($options as $key => $value) {
            $splitButtonOptions[] = [
                'label' => __($value),
                'onclick' => "setLocation('" . $this->getCreateActionUrl($key) . "')"
            ];
        }

        return $splitButtonOptions;
    }

    public function getCreateActionUrl($key)
    {
        return $this->getUrl(
            '*/*/new',
            ['content_type' => $key]
        );
    }
}
