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

use Exception;
use Mirasvit\Feed\Export\Liquid\Context;

/**
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 */
class DecisionBlock extends Block
{
    public $left;

    public $right;

    private function _stringValue($value): ?string
    {
        // objects should have a to_string a value to compare to
        if (is_object($value)) {
            if (method_exists($value, 'to_string')) {
                $value = $value->to_string();
            }
            if (method_exists($value, 'getSize')) {
                $value = $value->getSize();
            } else {
                throw new Exception("Cannot convert $value to string");// harry
            }

        }

        // arrays simply return true
        if (is_array($value)) {

            foreach ($value as $item) {
                if (is_object($item)) {
                    return 'true';
                }
            }

            return implode(',', $value);
        }

        return (string)$value;
    }

    protected function _equalVariables(string $left, string $right, Context &$context): bool
    {
        $left  = $this->_stringValue($context->get($left));
        $right = $this->_stringValue($context->get($right));

        return ($left == $right);

    }

    protected function _interpretCondition(?string $left, ?string $right, Context &$context, string $op = null): ?bool
    {
        if (is_null($op)) {
            $value = $this->_stringValue($context->get($left));

            return (bool)$value;
        }

        // values of 'empty' have a special meaning in array comparisons
        if ($right == 'empty' && is_array($context->get($left))) {
            $left  = count($context->get($left));
            $right = 0;

        } elseif ($left == 'empty' && is_array($context->get($right))) {
            $right = count($context->get($right));
            $left  = 0;
        } else {
            $left  = $context->get($left);
            $right = $context->get($right);

            $left  = $this->_stringValue($left);
            $right = $this->_stringValue($right);
        }

        // special rules for null values
        if (is_null($left) || is_null($right)) {
            // null == null returns true
            if ($op == '==' && is_null($left) && is_null($right)) {
                return true;
            }

            // null != anything other than null return true
            if ($op == '!=' && (!is_null($left) || !is_null($right))) {
                return true;
            }

            // everything else, return false;
            return false;
        }

        // regular rules
        switch ($op) {
            case '==':
                return ($left == $right);

            case '!=':
                return ($left != $right);

            case '>':
                return ($left > $right);

            case '<':
                return ($left < $right);

            case '>=':
                return ($left >= $right);

            case '<=':
                return ($left <= $right);

            case 'contains':
                /** @var mixed $left */
                return is_array($left) ? in_array($right, $left) : ($left == $right || is_numeric(strpos($left, $right)));

            case 'excludes':
                /** @var mixed $left */
                return !(is_array($left) ? in_array($right, $left) : ($left == $right || is_numeric(strpos($left, $right))));

            default:
                throw new Exception("Error in tag '" . $this->name() . "' - Unknown operator $op");
        }
    }
}
