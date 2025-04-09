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
abstract class Tag
{
    protected $markup;

    protected $attributes;


    public function __construct(string $markup, array &$tokens)
    {
        $this->markup = $markup;

        $this->parse($tokens);
    }


    public function parse(array &$tokens)
    {
    }

    public function extractAttributes(string $markup)
    {
        $this->attributes = [];

        $attribute_regexp = new Regexp(LIQUID_TAG_ATTRIBUTES);

        $matches = $attribute_regexp->scan($markup);

        foreach ($matches as $match) {
            $this->attributes[$match[0]] = $match[1];
        }
    }

    public function name(): string
    {
        return strtolower(get_class($this));
    }

    public function execute(Context &$context)
    {
        return '';
    }
}
