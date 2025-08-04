<?php
/**
 * PoshAce Performance Optimization Module
 *
 * @category  PoshAce
 * @package   PoshAce_Performance
 * @author    PoshAce
 * @copyright Copyright (c) 2024 PoshAce
 * @license   https://opensource.org/licenses/MIT MIT License
 */

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'PoshAce_Performance',
    __DIR__
);