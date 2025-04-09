<?php
/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * PHP version 5.3 or later
 *
 * @category MageTrend
 * @package  Magetend/Email
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-exit-intent-popup-extension
 */

namespace Magetrend\Email\Block\Adminhtml\Config\System\Config;

use Magento\Framework\App\Cache\Type\Config as CacheTypeConfig;

/**
 * Render extension information html element in Stores Configuration
 *
 * @category MageTrend
 * @package  Magetend/Email
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-exit-intent-popup-extension
 */
class Info extends \Magento\Config\Block\System\Config\Form\Field
{
    const MODULE_NAMESPACE = 'Magetrend_Email';

    const CONFIG_NAMESPACE = 'mtemail';

    const XML_PATH_GENERAL = 'mtemail/general/is_active';

    /**
     * @var \Magento\Framework\Module\Dir\ReverseResolver
     */
    public $reverseResolver;

    /**
     * Info constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Module\PackageInfoFactory $packageInfoFactory
     * @param \Magento\Framework\Module\Dir\ReverseResolver $reverseResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Module\PackageInfoFactory $packageInfoFactory,
        \Magento\Framework\Module\Dir\ReverseResolver $reverseResolver,
        array $data = []
    ) {
        $this->packageInfoFactory = $packageInfoFactory;
        $this->reverseResolver = $reverseResolver;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $respomseHtml = '';
        $output = $this->_scopeConfig->getValue(
            self::CONFIG_NAMESPACE.'/output',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            0
        );

        if (empty($output)) {
            return '';
        }

        ksort($output);
        foreach ($output as $rows) {
            if (empty( $rows)) {
                continue;
            }

            //@codingStandardsIgnoreLine
            $rows = json_decode($rows, true);
            if (empty($rows)) {
                continue;
            }

            foreach ($rows as $row) {
                $html = '<td class="label">'.$row['label'].'</td><td style="padding-top: 22px" class="value" colspan="3">'.$row['value'].'</td>';
                $html = $this->_decorateRowHtml($element, $html);
                $respomseHtml.=$html;
            }
        }

        return $respomseHtml;
    }
}