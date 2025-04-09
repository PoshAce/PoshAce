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

namespace Magetrend\Affiliate\Block\Adminhtml\Data\Form\Element;

use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;

/**
 * Matrix form element class
 */
class Matrix extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    public $layout;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    public $json;

    /**
     * Matrix constructor.
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\Json\Helper\Data $json
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Json\Helper\Data $json,
        array $data = []
    ) {
        $this->layout = $layout;
        $this->json = $json;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * Returns element html
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';
        $htmlId = $this->getHtmlId();

        $beforeElementHtml = $this->getBeforeElementHtml();
        if ($beforeElementHtml) {
            $html .= '<label class="addbefore" for="' . $htmlId . '">' . $beforeElementHtml . '</label>';
        }

        $value = $this->getValue();
        if (empty($value)) {
            $value = [];
        }

        if (!is_array($value)) {
            $value = $this->json->jsonDecode($value);
        }

        if (isset($value['__empty'])) {
            unset($value['__empty']);
        }
        $this->setValue($value);

        $html .= $this->layout->createBlock($this->getElementBlock())
            ->setElement($this)
            ->toHtml();

        $afterElementJs = $this->getAfterElementJs();
        if ($afterElementJs) {
            $html .= $afterElementJs;
        }

        $afterElementHtml = $this->getAfterElementHtml();
        if ($afterElementHtml) {
            $html .= '<label class="addafter" for="' . $htmlId . '">' . $afterElementHtml . '</label>';
        }

        return $html;
    }
}
