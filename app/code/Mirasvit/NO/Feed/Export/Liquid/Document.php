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
class Document extends Block
{
    public function __construct(array $tokens)
    {
        $this->parse($tokens);

        return $this;
    }

    public function checkIncludes(): bool
    {
        $return = false;

        foreach ($this->nodeList as $token) {
            if (is_object($token)) {
                if (get_class($token) == 'LiquidTagInclude' || get_class($token) == 'LiquidTagExtends') {
                    /** @var mixed $token */
                    if ($token->checkIncludes() == true) {
                        $return = true;
                    }
                }
            }
        }

        return $return;
    }

    public function blockDelimiter(): string
    {
        return '';
    }

    public function assertMissingDelimitation()
    {
    }
}
