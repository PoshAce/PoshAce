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

namespace Mirasvit\Feed\Ui\Dynamic\Category\Form\Block;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form as WidgetForm;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset as FieldsetRenderer;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mirasvit\Feed\Block\Adminhtml\Dynamic\Category\Edit\Renderer\Mapping;

class Form extends WidgetForm
{
    protected $_nameInLayout = "Mirasvit_Feed::dynamic_category";

    protected $formFactory;

    protected $registry;

    protected $mappingElement;

    public function __construct(
        FieldsetRenderer $fieldsetRenderer,
        Mapping          $mappingElement,
        FormFactory      $formFactory,
        Registry         $registry,
        Context          $context
    ) {
        $this->fieldsetRenderer = $fieldsetRenderer;
        $this->mappingElement   = $mappingElement;
        $this->formFactory      = $formFactory;
        $this->registry         = $registry;

        parent::__construct($context);
    }

    protected function _prepareForm()
    {
        $form = $this->formFactory->create();

        $form->addElement($this->mappingElement);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
