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

namespace Mirasvit\Feed\Export\Liquid\Tag;

use Exception;
use Mirasvit\Feed\Export\Liquid\DecisionBlock;
use Mirasvit\Feed\Export\Liquid\Regexp;
use Mirasvit\Feed\Export\Liquid\Context;

/**
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 */
class TagIf extends DecisionBlock
{
    private $_nodelistHolders = [];

    private $_blocks          = [];

    public function __construct(string $markup, array &$tokens)
    {
        $this->nodeList = &$this->_nodelistHolders[count($this->_blocks)];

        array_push($this->_blocks, [
            'if', $markup, &$this->nodeList,
        ]);

        parent::__construct($markup, $tokens);
    }

    public function unknownTag(string $tag, array $params, array &$tokens)
    {
        if ($tag == 'else' || $tag == 'elsif') {
            /* Update reference to nodelistHolder for this block */
            $this->nodeList                                    = &$this->_nodelistHolders[count($this->_blocks) + 1];
            $this->_nodelistHolders[count($this->_blocks) + 1] = [];

            array_push($this->_blocks, [
                $tag, $params, &$this->nodeList,
            ]);

        } else {
            parent::unknownTag($tag, $params, $tokens);
        }
    }

    public function execute(Context &$context): string
    {
        $context->push();

        $logicalRegex     = new Regexp('/\s+(and|or)\s+/');
        $conditionalRegex = new Regexp('/(' . LIQUID_QUOTED_FRAGMENT . ')\s*([=!<>a-z_]+)?\s*(' . LIQUID_QUOTED_FRAGMENT . ')?/');

        $result = '';

        foreach ($this->_blocks as $i => $block) {
            if ($block[0] == 'else') {
                $result = $this->executeAll($block[2], $context);

                break;
            }

            if ($block[0] == 'if' || $block[0] == 'elsif') {
                /* Extract logical operators */
                $logicalRegex->match($block[1]);

                $logicalOperators = $logicalRegex->matches;
                array_shift($logicalOperators);

                /* Extract individual conditions */
                $temp = $logicalRegex->split($block[1]);

                $conditions = [];

                foreach ($temp as $condition) {
                    if ($conditionalRegex->match($condition)) {
                        $left     = (isset($conditionalRegex->matches[1])) ? $conditionalRegex->matches[1] : null;
                        $operator = (isset($conditionalRegex->matches[2])) ? $conditionalRegex->matches[2] : null;
                        $right    = (isset($conditionalRegex->matches[3])) ? $conditionalRegex->matches[3] : null;

                        array_push($conditions, [
                            'left'     => $left,
                            'operator' => $operator,
                            'right'    => $right,
                        ]);
                    } else {
                        throw new Exception("Syntax Error in tag 'if' - Valid syntax: if [condition]");
                    }
                }


                if (count($logicalOperators)) {
                    /* If statement contains and/or */
                    $display = true;

                    foreach ($logicalOperators as $k => $logicalOperator) {
                        if ($logicalOperator == 'and') {
                            $display = $this->_interpretCondition($conditions[$k]['left'], $conditions[$k]['right'], $context, $conditions[$k]['operator']) && $this->_interpretCondition($conditions[$k + 1]['left'], $conditions[$k + 1]['right'], $context, $conditions[$k + 1]['operator']);
                        } else {
                            $display = $this->_interpretCondition($conditions[$k]['left'], $conditions[$k]['right'], $context, $conditions[$k]['operator']) || $this->_interpretCondition($conditions[$k + 1]['left'], $conditions[$k + 1]['right'], $context, $conditions[$k + 1]['operator']);
                        }
                    }

                } else {
                    /* If statement is a single condition */
                    $display = $this->_interpretCondition($conditions[0]['left'], $conditions[0]['right'], $context, $conditions[0]['operator']);
                }

                if ($display) {
                    foreach ($block[2] as $token) {
                        $token->reset();
                    }
                    $result = $this->executeAll($block[2], $context);

                    break;
                }
            }
        }

        $context->pop();

        return $result;
    }

    public function reset()
    {
        $this->index = 0;
    }
}
