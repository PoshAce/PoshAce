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

namespace Mirasvit\Feed\Export\Liquid;
/**
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 */
class Filterbank
{

    public $filters;

    public $methodMap;

    public $context;

    public function __construct(&$context)
    {
        $this->context = $context;
    }

    function addFilter($filter): bool
    {
        // if the passed filter was an object, store the object for future reference.
        if (is_object($filter)) {
            $methods = array_flip(get_class_methods($filter));

            foreach ($methods as $method => $null) {
                $this->methodMap[$method] = $filter;
            }

            return true;
        }

        return false;
    }

    function invoke(string $name, $value, array $args)
    {
        if (!is_array($args)) {
            $args = [];
        }

        array_unshift($args, $value);
        // consult the mapping
        if (isset($this->methodMap[$name])) {

            $class = $this->methodMap[$name];

            // if we have a registered object for the class, use that instead
            if (isset($this->filters[$class])) {
                $class = &$this->filters[$class];
            }

            return call_user_func_array([$class, $name], $args);
        }

        return $value;
    }
}
