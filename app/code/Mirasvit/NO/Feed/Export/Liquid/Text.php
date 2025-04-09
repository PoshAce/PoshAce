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
class Text
{
    private $name;

    private $text;

    private $length = 1;

    private $index  = 0;

    public function __construct(string $text)
    {
        $this->name = 'text';

        $this->text = $text;
    }

    public function getName(): string
    {
        return $this->name;
    }

    function execute($context)
    {
        if ($this->index == 0) {
            $this->index = 1;

            return $this->text;
        }
    }

    public function toArray(): array
    {
        return [
            'name'   => $this->name,
            'index'  => $this->index,
            'length' => $this->length,
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
