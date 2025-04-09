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

namespace Magetrend\Affiliate\Block\Registration;

use Magento\Framework\View\Element\Template;

class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * Block template file
     * @var string
     */
    //@codingStandardsIgnoreLine
    protected $_template = 'Magetrend_Affiliate::registration/form.phtml';

    public $formFactory;

    public $moduleHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magetrend\Affiliate\Model\FormBuilder\FormFactory $formFactory,
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        array $data = []
    ) {
        $this->formFactory = $formFactory;
        $this->moduleHelper = $moduleHelper;
        parent::__construct($context, $data);
    }

    public function getFieldHtml($fieldData, $addtionalData = [])
    {
        if (!isset($fieldData['store_id'])) {
            $fieldData['store_id'] = $this->_storeManager->getStore()->getId();
        }

        $type = 'field';
        if (isset($fieldData['type'])) {
            $type = $fieldData['type'];
        }

        $field = $this->_layout->createBlock(\Magento\Framework\View\Element\Template::class)
            ->setTemplate('Magetrend_Affiliate::registration/field/'.$type.'.phtml')
            ->setData(array_merge($fieldData, $addtionalData));

        return $field->toHtml();
    }

    public function getFields()
    {
        $formId = $this->moduleHelper->getRegistrationFormId($this->_storeManager->getStore()->getId());
        $form = $this->formFactory->create()
            ->load($formId);

        return $form->getFields();
    }

    public function getPostActionUrl()
    {
        return $this->getUrl('affiliate/registration/post');
    }

    public function getBackUrl()
    {
        return $this->_urlBuilder->getCurrentUrl();
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
}
