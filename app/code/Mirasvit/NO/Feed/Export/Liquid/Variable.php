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

use Mirasvit\Feed\Export\Liquid\Context;

/**
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 */
class Variable
{

    private $filters;

    private $name;

    private $markup;

    private $length = 1;

    private $index  = 0;

    public function __construct(string $markup)
    {
        $this->markup = $markup;

        $quotedFragmentRegexp  = new Regexp('/\s*(' . LIQUID_QUOTED_FRAGMENT . ')/');
        $filterSeperatorRegexp = new Regexp('/' . LIQUID_FILTER_SEPARATOR . '\s*(.*)/');
        $filterSplitRegexp     = new Regexp('/ ' . LIQUID_FILTER_SEPARATOR . '/');

        $filterNameRegexp     = new Regexp('/\s*(\w+)/');
        $filterArgumentRegexp = new Regexp('/(?:' . LIQUID_FILTER_ARGUMENT_SEPARATOR . '|' . LIQUID_ARGUMENT_SEPARATOR . ')\s*(' . LIQUID_QUOTED_FRAGMENT . ')/');

        $quotedFragmentRegexp->match($markup);

        $this->name = (isset($quotedFragmentRegexp->matches[1])) ? $quotedFragmentRegexp->matches[1] : null;


        if ($filterSeperatorRegexp->match($markup)) {
            $filters = $filterSplitRegexp->split($filterSeperatorRegexp->matches[1]);

            foreach ($filters as $filter) {
                $filterNameRegexp->match($filter);

                if (isset($filterNameRegexp->matches[1])) {
                    $filterName = $filterNameRegexp->matches[1];

                    $filterArgumentRegexp->match_all($filter);
                    $matches = $this->array_flatten($filterArgumentRegexp->matches[1]);

                    $this->filters[] = [
                        $filterName, $matches,
                    ];
                }
            }

        } else {
            $this->filters = [];
        }
    }

    public function array_flatten(array $array): array
    {
        $return = [];
        foreach ($array as $element) {
            if (is_array($element)) {
                $return = array_merge($return, self::array_flatten($element));
            } else {
                $return[] = $element;
            }
        }

        return $return;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return false|string
     */
    function execute(Context $context)
    {
        if ($this->index == 0) {
            $output = $context->get($this->name);

            foreach ($this->filters as $filter) {
                [$filtername, $filterArgKeys] = $filter;

                $filterArgValues = [];

                foreach ($filterArgKeys as $argKey) {
                    $filterArgValues[] = $context->get($argKey);
                }

                $output = $context->invoke($filtername, $output, $filterArgValues);
            }

            $this->index = 1;

            if (is_array($output)) {
                $output = $context->resolver->toString($output, $this->name);
            } elseif (is_object($output)) {
                $output = $context->resolver->resolve($output, null);
            } elseif ($output && !is_scalar($output)) {
                $output = json_encode($output);
            }

            return $output;
        }
    }

    public function toArray(): array
    {
        return [
            'name'   => $this->name,
            'length' => $this->length,
            'index'  => $this->index,
        ];
    }

    public function fromArray(array $array)
    {
        $this->index  = $array['index'];
        $this->length = $array['length'];
    }

    public function reset()
    {
        $this->index = 0;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getLength(): int
    {
        return $this->length;
    }
}
