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

namespace Mirasvit\Feed\Export\Filter;

use Exception;
use ReflectionClass;
use Zend_Reflection_Docblock;
use Zend_Reflection_Parameter;

class Pool
{
    protected $scopes;

    public function __construct(
        array $scopes
    ) {
        $this->scopes = $scopes;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function getFilters(): array
    {
        $filters = [];
        foreach ($this->scopes as $scope) {
            $class = new ReflectionClass($scope);

            //            /** @var \Zend_Reflection_Method $method */
            foreach ($class->getMethods() as $method) {
                try {
                    $doc = $method->getDocComment();
                    $doc = new Zend_Reflection_Docblock($doc);

                    if (!$doc->getShortDescription()) {
                        continue;
                    }
                } catch (Exception $e) {
                    continue;
                }


                $filter = [
                    'label' => __($doc->getShortDescription())->__toString(),
                    'value' => $method->getName(),
                    'args'  => [],
                ];

                /** @var Zend_Reflection_Parameter $param */
                foreach ($method->getParameters() as $param) {
                    if ($param->getName() == 'input') {
                        continue;
                    }

                    $default = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : '';

                    $filter['args'][] = [
                        'value'   => $param->getName(),
                        'label'   => ucfirst($param->getName()),
                        'default' => $default,
                    ];
                }

                $filters[] = $filter;
            }
        }

        return $filters;
    }
}
