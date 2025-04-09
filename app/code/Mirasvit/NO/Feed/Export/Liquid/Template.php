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
use Mirasvit\Feed\Export\Liquid\Document;

/**
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 */

defined('LIQUID_HAS_PROPERTY_METHOD') or define('LIQUID_HAS_PROPERTY_METHOD', 'field_exists');
/**
 * This method is called on object when resolving variables when
 * a given property exists
 */
defined('LIQUID_GET_PROPERTY_METHOD') or define('LIQUID_GET_PROPERTY_METHOD', 'get');
/**
 * Separator between filters
 */
defined('LIQUID_FILTER_SEPARATOR') or define('LIQUID_FILTER_SEPARATOR', '\|');
/**
 * Separator for arguments
 */
defined('LIQUID_ARGUMENT_SEPARATOR') or define('LIQUID_ARGUMENT_SEPARATOR', ',');
/**
 * Separator for argument names and values
 */
defined('LIQUID_FILTER_ARGUMENT_SEPARATOR') or define('LIQUID_FILTER_ARGUMENT_SEPARATOR', ':');
/**
 * Separator for variable attributes
 */
defined('LIQUID_VARIABLE_ATTRIBUTE_SEPARATOR') or define('LIQUID_VARIABLE_ATTRIBUTE_SEPARATOR', '.');
/**
 * Allow Templatenames with extension in include and extends tags. default = false
 */
defined('LIQUID_INCLUDE_ALLOW_EXT') or define('LIQUID_INCLUDE_ALLOW_EXT', false);
/**
 * Suffix for include files
 */
defined('LIQUID_INCLUDE_SUFFIX') or define('LIQUID_INCLUDE_SUFFIX', 'liquid');
/**
 * Prefix for include files
 */
defined('LIQUID_INCLUDE_PREFIX') or define('LIQUID_INCLUDE_PREFIX', '_');
/**
 * Tag start
 */
defined('LIQUID_TAG_START') or define('LIQUID_TAG_START', '{%');
/**
 * Tag end
 */
defined('LIQUID_TAG_END') or define('LIQUID_TAG_END', '%}');
/**
 * Variable start
 */
defined('LIQUID_VARIABLE_START') or define('LIQUID_VARIABLE_START', '{{');
/**
 * Variable end
 */
defined('LIQUID_VARIABLE_END') or define('LIQUID_VARIABLE_END', '}}');
/**
 * The characters allowed in a variable
 */
defined('LIQUID_ALLOWED_VARIABLE_CHARS') or define('LIQUID_ALLOWED_VARIABLE_CHARS', '[a-zA-Z_.-:]');
/**
 * Regex for quoted fragments
 */
defined('LIQUID_QUOTED_FRAGMENT') or define('LIQUID_QUOTED_FRAGMENT', '"[^"]+"|\'[^\']+\'|[^\s,|]+');
/**
 * Regex for recognizing tab attributes
 */
defined('LIQUID_TAG_ATTRIBUTES') or define('LIQUID_TAG_ATTRIBUTES', '/(\w+)\s*\:\s*(' . LIQUID_QUOTED_FRAGMENT . ')/');
/**
 * Regex used to split tokens
 */
defined('LIQUID_TOKENIZATION_REGEXP') or define('LIQUID_TOKENIZATION_REGEXP', '/(' . LIQUID_TAG_START . '.*?' . LIQUID_TAG_END . '|' . LIQUID_VARIABLE_START . '.*?' . LIQUID_VARIABLE_END . ')/');
defined('LIQUID_PATH') or define('LIQUID_PATH', dirname(__FILE__));
defined('LIQUID_AUTOLOAD') or define('LIQUID_AUTOLOAD', true);

class Template
{

    private        $root;

    private        $filters;

    private static $tags = [];

    public function __construct()
    {
        $this->filters = [];
    }

    public function getRoot(): Document
    {
        return $this->root;
    }

    public function registerTag(string $name, string $class)
    {
        self::$tags[$name] = $class;
    }

    public static function getTags(): array
    {
        return self::$tags;
    }

    public function registerFilter(array $filter): self
    {
        $this->filters[] = $filter;

        return $this;
    }

    public function registerFilters(array $filters): self
    {
        foreach ($filters as $filter) {
            $this->filters[] = $filter;
        }

        return $this;
    }


    public static function tokenize(string $source): array
    {
        return (!$source)
            ? []
            : preg_split(LIQUID_TOKENIZATION_REGEXP, $source, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    }


    public function parse(string $source, bool $isXml = true): self
    {
        $tokens = Template::tokenize($source);

        if ($isXml) {
            foreach ($tokens as $idx => $token) {
                $tagRegexp = new Regexp('/^' . LIQUID_TAG_START . '\s*(\w+)\s*(.*)?' . LIQUID_TAG_END . '$/');

                if ($tagRegexp->match($token)) {
                    // if is tag (if, for), need remove PHP_EOL before
                    if ($idx > 0) {
                        $t   = explode("\n", $tokens[$idx - 1]);
                        $end = end($t);
                        if (strlen(trim($end)) == 0) {
                            array_pop($t);
                        }
                        $tokens[$idx - 1] = implode("\n", $t);
                    }
                }
            }
        }

        $this->root = new Document($tokens);

        return $this;
    }

    public function execute(Context $context): string
    {
        $context->setTemplate($this);

        return $this->root->execute($context);
    }

    public function getIndex(): int
    {
        return $this->root->getIndex();
    }

    public function getLength(): int
    {
        return $this->root->getLength();
    }

    public function toArray(): array
    {
        return $this->root->toArray();
    }

    public function fromArray(array $array): self
    {
        $this->root->fromArray($array);

        return $this;
    }
}
