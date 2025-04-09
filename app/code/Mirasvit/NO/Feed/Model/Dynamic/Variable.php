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

namespace Mirasvit\Feed\Model\Dynamic;

use Magento\Backend\Block\Template as BlockTemplate;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\TemplateEngine\Php as PhpEngine;
use Mirasvit\Feed\Model\Config;
use Magento\Catalog\Model\Product;
use Mirasvit\Feed\Export\Resolver\ProductResolver;

/**
 * @method string getName()
 * @method string getCode()
 */
class Variable extends AbstractModel
{
    protected $objectManager;

    private   $config;

    private   $phpEngine;

    private   $blockTemplate;

    public function __construct(
        ObjectManagerInterface $objectManager,
        Context                $context,
        Config                 $config,
        PhpEngine              $phpEngine,
        BlockTemplate          $blockTemplate,
        Registry               $registry
    ) {
        $this->objectManager = $objectManager;
        $this->config        = $config;
        $this->phpEngine     = $phpEngine;
        $this->blockTemplate = $blockTemplate;

        parent::__construct($context, $registry);
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    public function getValue(Product $product, ?ProductResolver $resolver): string
    {
        if (!$this->getFilePath()) {
            return '';
        }

        $value   = '';
        $absPath = $this->config->getRootPath() . $this->getFilePath();

        if (!file_exists($absPath)) {
            return '';
        }

        $value = $this->phpEngine->render($this->blockTemplate, $absPath, ['product' => $product, 'objectManager' => $this->objectManager]);

        return $value;
    }

    public function getAbsFilePath(): string
    {
        return $this->config->getRootPath() . $this->getFilePath();
    }

    public function getFilePath(): string
    {
        return $this->getFileData()['path'];
    }

    public function getPhpCode(): string
    {
        return $this->getFileData()['code'];
    }

    public function isValid(?string $code = null): bool
    {
        if (!$code) {
            $code = $this->getPhpCode();
        }

        $code = escapeshellarg('<?php ' . $code . ' ?>');
        $lint = "echo $code | php -l";

        return (preg_match('/No syntax errors detected in -/', $lint));
    }

    public function getRowsToExport(): array
    {
        $array = [
            'name',
            'code',
            'php_code',
        ];

        return $array;
    }

    protected function _construct()
    {
        $this->_init(\Mirasvit\Feed\Model\ResourceModel\Dynamic\Variable::class);
    }

    private function getFileData(): array
    {
        if (!$this->getCode()) {
            return [
                'path' => '',
                'code' => (string)__('Please set "Variable Code" and save the variable before continuing...')
            ];
        }

        $paths = [
            'var/mst_feed/' . $this->getCode() . '.php',
            'app/code/Mirasvit/Feed/' . $this->getCode() . '.php'
        ];

        foreach ($paths as $path) {
            $filePath = $this->config->getRootPath() . $path;

            if (file_exists($filePath)) {
                if (is_readable($filePath)) {
                    return ['path' => $path, 'code' => file_get_contents($filePath)];
                } else {
                    return ['path' => '', 'code' => (string)__('The file %1 is not readable', $path)];
                }
            }
        }

        return ['path' => '', 'code' => (string)__('Please create the file %1 or %2 with source code', $paths)];
    }
}
