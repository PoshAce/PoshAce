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

/**
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 */
class Block extends Tag
{
    protected $nodeList;

    protected $index  = 0;

    protected $length = 0;


    public function getNodeList(): array
    {
        return $this->nodeList;
    }

    public function parse(array &$tokens)
    {
        $startRegexp = new Regexp('/^' . LIQUID_TAG_START . '/');

        $tagRegexp           = new Regexp('/^' . LIQUID_TAG_START . '\s*(\w+)\s*(.*)?' . LIQUID_TAG_END . '$/');
        $variableStartRegexp = new Regexp('/^' . LIQUID_VARIABLE_START . '/');

        $this->nodeList = [];

        if (!is_array($tokens)) {
            return;
        }

        $tags = Template::getTags();

        while (count($tokens)) {
            $token = array_shift($tokens);

            if ($startRegexp->match($token)) {
                if ($tagRegexp->match($token)) {
                    // if we found the proper block delimiter just end parsing here and let the outer block proceed
                    if ($tagRegexp->matches[1] == $this->blockDelimiter()) {
                        return $this->endTag();
                    }

                    if (array_key_exists($tagRegexp->matches[1], $tags)) {
                        $tagName = $tags[$tagRegexp->matches[1]];
                    } else {
                        // search for a defined class of the right name, instead of searching in an array
                        $tagName = '\Mirasvit\Feed\Export\Liquid\Tag\Tag' . ucwords($tagRegexp->matches[1]);
                        $tagName = (class_exists($tagName) === true) ? $tagName : null;
                    }

                    if (!is_null($tagName) && class_exists($tagName)) {
                        $this->nodeList[] = new $tagName($tagRegexp->matches[2], $tokens);

                        if ($tagRegexp->matches[1] == 'extends') {
                            return true;
                        }
                    } else {
                        if (is_string($tagRegexp->matches[2])) {
                            $tagRegexp->matches[2] = [$tagRegexp->matches[2]];
                        }
                        $this->unknownTag($tagRegexp->matches[1], $tagRegexp->matches[2], $tokens);
                    }
                } else {
                    throw new Exception("Tag $token was not properly terminated");
                }
            } elseif ($variableStartRegexp->match($token)) {
                $this->nodeList[] = $this->createVariable($token);
            } elseif ($token != '') {
                $this->nodeList[] = new Text($token);
            }
        }

        $this->assertMissingDelimitation();
    }


    protected function endTag()
    {
    }


    protected function unknownTag(string $tag, array $params, array &$tokens)
    {
        switch ($tag) {
            case 'else':
                throw new Exception($this->getBlockName() . " does not expect else tag");

            case 'end':
                throw new Exception("'end' is not a valid delimiter for " . $this->getBlockName() . " tags.");

            default:
                throw new Exception("Unknown tag $tag");
        }

    }

    function blockDelimiter(): string
    {
        return "end" . $this->getBlockName();
    }


    private function getBlockName(): string
    {
        return str_replace('mirasvit\feed\export\liquid\tag\tag', '', strtolower(get_class($this)));
    }

    private function createVariable(string $token): Variable
    {
        $variableRegexp = new Regexp('/^' . LIQUID_VARIABLE_START . '(.*)' . LIQUID_VARIABLE_END . '$/');
        if ($variableRegexp->match($token)) {
            return new Variable($variableRegexp->matches[1]);
        }

        throw new Exception("Variable $token was not properly terminated");
    }

    public function execute(Context &$context)
    {
        return $this->executeAll($this->nodeList, $context);
    }

    public function getIndex(): int
    {
        $index = $this->index;

        foreach ($this->nodeList as $token) {
            $index += $token->getIndex();
        }

        return $index;
    }

    public function getLength(): int
    {
        $length = $this->length;

        foreach ($this->nodeList as $token) {
            $length += $token->getLength();
        }

        return $length;
    }

    public function toArray(): array
    {
        $array = [];
        foreach ($this->nodeList as $token) {
            $array[] = $token->toArray();
        }

        return $array;
    }

    public function fromArray(array $array)
    {
        foreach ($this->nodeList as $index => $token) {
            if (isset($array[$index])) {
                $token->fromArray($array[$index]);
            }
        }
    }

    function assertMissingDelimitation()
    {
        throw new Exception($this->getBlockName() . " tag was never closed");
    }

    protected function executeAll(array $list, Context &$context): string
    {
        $result = '';
        foreach ($list as $token) {
            $token  = (is_object($token) && method_exists($token, 'execute')) ? $token->execute($context) : $token;
            $token  = is_array($token) ? print_r($token, true) : $token;
            $result .= $token;

            if ($context->isBreak) {
                break;
            }
        }

        $context->isTimeout();

        return $result;
    }
}
