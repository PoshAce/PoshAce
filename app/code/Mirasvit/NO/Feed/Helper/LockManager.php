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

namespace Mirasvit\Feed\Helper;

use Magento\Framework\Lock\LockManagerInterface;
use Mirasvit\Core\Service\CompatibilityService;

class LockManager
{

    private static $locks = [];

    /**
     * @return $this|LockManagerInterface
     */
    public function getLockManager()
    {
        if (CompatibilityService::is21()) {
            return $this;
        }

        return CompatibilityService::getObjectManager()
            ->get(LockManagerInterface::class);
    }

    public function lock(string $lockName, string $lockFile): bool
    {
        $lockPointer = fopen($lockFile, "w");

        if (flock($lockPointer, LOCK_EX | LOCK_NB)) {
            self::$locks[$lockName] = $lockPointer;

            return true;
        }

        return false;
    }

    public function unlock(string $lockName): bool
    {
        if (!$this->isLocked($lockName)) {
            return true;
        }

        $lockPointer = self::$locks[$lockName];

        if (flock($lockPointer, LOCK_UN)) {
            fclose($lockPointer);
            unset(self::$locks[$lockName]);

            return true;
        }

        return false;
    }

    public function isLocked(string $lockName): bool
    {
        return isset(self::$locks[$lockName]);
    }
}
