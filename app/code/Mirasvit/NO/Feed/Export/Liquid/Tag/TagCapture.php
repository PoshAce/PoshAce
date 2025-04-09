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
use Mirasvit\Feed\Export\Liquid\Block;
use Mirasvit\Feed\Export\Liquid\Context;
use Mirasvit\Feed\Export\Liquid\Regexp;

class TagCapture extends Block
{
    private $to;

    public function __construct($markup, &$tokens)
    {
        $syntaxRegexp = new Regexp('/(\w+)/');
        if ($syntaxRegexp->match($markup)) {
            $this->to = $syntaxRegexp->matches[1];
            parent::__construct($markup, $tokens);
        } else {
            throw new Exception("Syntax Error in 'capture' - Valid syntax: assign [var] = [source]"); // harry
        }
    }

    /**
     * Renders the block
     * @throws Exception
     */
    public function execute(Context &$context): string
    {
        $context->push();
        $result = $this->executeAll($this->nodeList, $context);
        $context->pop();

        $context->set($this->to, $result);

        return '';
    }

    public function reset()
    {
        $this->resetNodes();
    }

    protected function resetNodes()
    {
        foreach ($this->nodeList as $token) {
            $token->reset();
        }
    }
}
