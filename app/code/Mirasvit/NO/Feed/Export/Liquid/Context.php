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
use Magento\Framework\Debug;

/**
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 */
class Context
{

    protected $productExportStep = 0;

    public    $registers;

    private   $filterBank;

    public    $environments      = [];

    protected $timeoutCallback   = null;

    protected $iterationCallback = null;

    protected $template;

    public    $isBreak           = false;

    public    $resolver;

    private   $assigns;

    public function __construct(object $resolver, array $assigns = [])
    {
        $this->assigns   = [$assigns];
        $this->assigns[] = ['context' => $resolver];

        $this->resolver   = $resolver;
        $this->filterBank = new Filterbank($this);
    }

    public function setProductExportStep(int $step): self
    {
        $this->productExportStep = $step;

        return $this;
    }

    public function getProductExportStep(): int
    {
        return $this->productExportStep;
    }

    public function setTimeoutCallback($callback): self
    {
        $this->timeoutCallback = $callback;

        return $this;
    }

    public function setIterationCallback($callback): self
    {
        $this->iterationCallback = $callback;

        return $this;
    }

    public function setTemplate(Template $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function addFilters(array $filters)
    {
        foreach ($filters as $filter) {
            $this->filterBank->addFilter($filter);
        }
    }

    public function invoke(string $name, $value, array $args = null)
    {
        return $this->filterBank->invoke($name, $value, $args);
    }

    public function merge(array $newAssigns)
    {
        $this->assigns[0] = array_merge($this->assigns[0], $newAssigns);
    }


    public function push(): bool
    {
        array_unshift($this->assigns, []);

        return true;
    }

    public function pop()
    {
        if (count($this->assigns) == 1) {
            Debug::backtrace();
            throw new Exception('No elements to pop');
        }

        array_shift($this->assigns);
    }


    public function get(string $key, array $args = [])
    {
        return $this->resolve($key, $args);
    }

    public function set(string $key, $value, bool $global = false)
    {
        if ($global) {
            for ($i = 0; $i < count($this->assigns); $i++) {
                $this->assigns[$i][$key] = $value;
            }
        } else {
            $this->assigns[0][$key] = $value;
        }
    }

    public function hasKey(?string $key): bool
    {
        return (!is_null($this->resolve($key)));
    }

    public function resolve(?string $key, array $args = [])
    {
        // this shouldn't happen
        if (is_array($key)) {
            throw new Exception("Cannot resolve arrays as key");
        }

        if (is_null($key) || $key == 'null') {
            return null;
        }

        if ($key == 'true') {
            return true;
        }

        if ($key == 'false') {
            return false;
        }

        if (preg_match('/^\'(.*)\'$/', $key, $matches)) {
            return $matches[1];
        }

        if (preg_match('/^"(.*)"$/', $key, $matches)) {
            return $matches[1];
        }

        if (preg_match('/^(\d+)$/', $key, $matches)) {
            return $matches[1];
        }

        if (preg_match('/^(\d[\d\.]+)$/', $key, $matches)) {
            return $matches[1];
        }

        return $this->variable($key, $args);
    }


    public function fetch(string $key)
    {
        foreach ($this->environments as $environment) {
            if (array_key_exists($key, $environment)) {
                return $environment[$key];
            }
        }

        foreach ($this->assigns as $scope) {
            if (array_key_exists($key, $scope)) {
                $obj = $scope[$key];

                return $obj;
            }
        }
    }

    public function variable(string $key, array $args = [])
    {
        /* Support [0] style array indices */
        if (preg_match("|\[[0-9]+\]|", $key)) {
            $key = preg_replace("|\[([0-9]+)\]|", ".$1", $key);
        }

        $parts = explode(LIQUID_VARIABLE_ATTRIBUTE_SEPARATOR, $key);

        $object = $this->fetch(array_shift($parts));

        if (!is_null($object)) {
            while (count($parts) > 0) {
                $nextPartName = array_shift($parts);

                if (is_array($object)) {
                    // if the last part of the context variable is .size we just return the count
                    if ($nextPartName == 'size' && count($parts) == 0 && !array_key_exists('size', $object)) {
                        return count($object);
                    }

                    if (array_key_exists($nextPartName, $object)) {
                        $object = $object[$nextPartName];
                    } else {
                        return null;
                    }
                } elseif (is_object($object)) {
                    $object = $this->resolver->resolve($object, $nextPartName, $args);
                }

                if (is_object($object) && method_exists($object, 'toLiquid')) {
                    $object = $object->toLiquid();
                }
            }

            return $object;
        } else {
            return null;
        }
    }

    /**
     * @return bool|mixed
     */
    public function isTimeout()
    {
        if ($this->iterationCallback) {
            $index  = $this->template->getIndex();
            $length = $this->template->getLength();

            call_user_func($this->iterationCallback, ['index' => $index, 'length' => $length]);
        }

        if ($this->timeoutCallback) {
            return call_user_func($this->timeoutCallback);
        }

        return false;
    }
}
