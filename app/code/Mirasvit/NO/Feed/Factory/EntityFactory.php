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

namespace Mirasvit\Feed\Factory;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Mirasvit\Feed\Model\TemplateFactory;
use Mirasvit\Feed\Model\RuleFactory;
use Mirasvit\Feed\Model\Dynamic\AttributeFactory;
use Mirasvit\Feed\Model\Dynamic\CategoryFactory;
use Mirasvit\Feed\Model\Dynamic\VariableFactory;

class EntityFactory
{

    private $variable;

    private $category;

    private $attribute;

    private $rule;

    private $template;

    private $context;

    public function __construct(
        Context          $context,
        TemplateFactory  $template,
        RuleFactory      $rule,
        AttributeFactory $attribute,
        VariableFactory  $variable,
        CategoryFactory  $category
    ) {
        $this->context   = $context;
        $this->template  = $template;
        $this->rule      = $rule;
        $this->attribute = $attribute;
        $this->category  = $category;
        $this->variable  = $variable;
    }

    public function getEntityModelFactory(string $entityName)
    {
        switch ($entityName) {
            case 'template':
                $entityModel = $this->template->create();
                break;

            case 'rule':
                $entityModel = $this->rule->create();
                break;

            case 'dynamic_attribute':
                $entityModel = $this->attribute->create();
                break;

            case 'dynamic_category':
                $entityModel = $this->category->create();
                break;
            case 'dynamic_variable':
                $entityModel = $this->variable->create();
                break;
            default:
                $entityModel = '';
                break;
        }

        return $entityModel;
    }
}
