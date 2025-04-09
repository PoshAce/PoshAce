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
class Regexp
{

    public $pattern;

    public $matches;


    public function __construct(string $pattern)
    {
        $this->pattern = (substr($pattern, 0, 1) != '/') ? '/' . $this->quote($pattern) . '/' : $pattern;
    }

    function quote(string $string): string
    {
        return preg_quote($string, '/');
    }

    function scan(string $string): array
    {
        $result = preg_match_all($this->pattern, $string, $matches);

        if (count($matches) == 1) {
            return $matches[0];
        }

        array_shift($matches);

        $result = [];

        foreach ($matches as $matchKey => $subMatches) {
            foreach ($subMatches as $subMatchKey => $subMatch) {
                $result[$subMatchKey][$matchKey] = $subMatch;
            }
        }

        return $result;
    }

    public function match(string $string): int
    {
        return preg_match($this->pattern, $string, $this->matches);
    }


    function match_all(string $string): int
    {
        return preg_match_all($this->pattern, $string, $this->matches);
    }

    function split(string $string, int $limit = -1): array
    {
        return preg_split($this->pattern, $string, $limit);
    }
}
