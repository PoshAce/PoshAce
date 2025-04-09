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

namespace Mirasvit\Feed\Block\Adminhtml\Dynamic\Attribute\Edit\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Registry;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Mirasvit\Feed\Model\Dynamic\Attribute;

class Conditions extends AbstractElement
{
    protected $layout;

    protected $registry;

    protected $fieldset;

    public function __construct(
        Fieldset          $fieldset,
        Registry          $registry,
        LayoutInterface   $layout,
        Factory           $factory,
        CollectionFactory $collectionFactory,
        Escaper           $escaper
    ) {
        $this->fieldset = $fieldset;
        $this->registry = $registry;
        $this->layout   = $layout;

        parent::__construct($factory, $collectionFactory, $escaper);
    }

    public function toHtml(): string
    {
        return $this->layout
            ->createBlock('Magento\Backend\Block\Template')
            ->setData('js_config', $this->getJsConfig())
            ->setData('parent', $this)
            ->setData('code', $this->getAttribute()->getCode())
            ->setTemplate('Mirasvit_Feed::dynamic/attribute/edit/form.phtml')
            ->toHtml();
    }

    public function getJsConfig(): array
    {
        return [
            "*" => [
                'Magento_Ui/js/core/app' => [
                    'components' => [
                        'dynamic_attribute' => [
                            'component' => 'Mirasvit_Feed/js/dynamic/attribute',
                            'config'    => [
                                'conditions' => $this->getAttribute()->getConditions(),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }


    public function getAttribute(): Attribute
    {
        return $this->registry->registry('current_model');
    }
}
