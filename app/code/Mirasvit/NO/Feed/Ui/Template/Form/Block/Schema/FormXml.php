<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-feed
 * @version   1.4.6
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\Feed\Ui\Template\Form\Block\Schema;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Framework\Registry;
use Mirasvit\Feed\Helper\Output as OutputHelper;
use Mirasvit\Feed\Model\Template;

class  FormXml extends Form
{
    protected $_nameInLayout = 'Mirasvit_Feed::template_content_xml';

    protected $registry;

    protected $elementXml;

    public function __construct(
        Registry $registry,
        Context  $context
    ) {
        $this->registry = $registry;

        $this->_template = 'Mirasvit_Feed::template/edit/tab/schema/xml.phtml';
        parent::__construct($context);
    }

    public function getModel(): ?Template
    {
        return $this->registry->registry('current_model');
    }

    public function getJsConfig(): array
    {
        return [
            "*" => [
                'Magento_Ui/js/core/app' => [
                    'components' => [
                        'schema_xml' => [
                            'component' => 'Mirasvit_Feed/js/edit/tab/schema/xml',
                            'config'    => [
                                'liquidTemplate' => $this->getModel()->getLiquidTemplate(),
                                'form' => 'mst_feed_template_form'
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
