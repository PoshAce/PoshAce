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


namespace Mirasvit\Feed\Helper\CategoryMapping;

class FileReader implements FileInterface
{
    protected $limit            = 100;

    protected $file;

    protected $mappingDelimiter = ' > ';

    public function setLimit(int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setFile(string $file)
    {
        $this->file = $file;

        return $this;
    }

    public function getFile(): string
    {
        return $this->file;
    }


    public function getRows(string $search): array
    {
        $result = [];
        $handle = fopen($this->getFile(), "r");
        if ($handle) {
            $i = 0;
            while (($buffer = fgets($handle, 4096)) !== false) {
                if (($this->getLimit() && $i >= $this->getLimit())) {
                    break;
                }
                if (stripos($buffer, $search) !== false) {
                    $categories      = explode($this->getMappingDelimiter(), trim($buffer));
                    $buffer          = implode($this->getMappingDelimiter(), $categories);
                    $result[$buffer] = $this->getFileName();
                    $i++;
                }
            }
            fclose($handle);
        }

        return $result;
    }

    public function getMappingDelimiter(): string
    {
        return $this->mappingDelimiter;
    }

    public function setMappingDelimiter(string $delimiter)
    {
        $this->mappingDelimiter = $delimiter;

        return $this;
    }

    protected function getFileName(): string
    {
        return basename($this->getFile());
    }
}
