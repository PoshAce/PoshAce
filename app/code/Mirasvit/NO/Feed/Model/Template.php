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

namespace Mirasvit\Feed\Model;

use Exception;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Mirasvit\Core\Service\YamlService;
use Mirasvit\Feed\Model\ResourceModel\Template\CollectionFactory as TemplateCollectionFactory;
use Mirasvit\Feed\Service\Serialize;

/**
 * Template Model
 * @method string getName()
 * @method $this setName($name)
 * @method bool hasCreatedAt()
 * @method $this setCreatedAt($createdAt)
 */
class Template extends AbstractTemplate
{
    protected $config;

    protected $collectionFactory;

    protected $serializer;

    protected $messageManager;

    public function __construct(
        Config                    $config,
        TemplateCollectionFactory $templateCollectionFactory,
        Context                   $context,
        Registry                  $registry,
        Serialize                 $serializer,
        MessageManagerInterface   $messageManager
    ) {
        $this->config            = $config;
        $this->collectionFactory = $templateCollectionFactory;
        $this->serializer        = $serializer;
        $this->messageManager    = $messageManager;

        parent::__construct($context, $registry);
    }

    protected function _construct()
    {
        $this->_init('Mirasvit\Feed\Model\ResourceModel\Template');
    }

    public function export(): string
    {
        $path = $this->config->absolutePath($this->config->getTemplatePath()) . '/' . $this->getName() . '.yaml';

        $yaml = YamlService::dump($this->toArray([
            'name',
            'type',
            'format_serialized',
            'csv_delimiter',
            'csv_enclosure',
            'csv_include_header',
            'csv_extra_header',
            'csv_schema',
        ]), 10);

        try {
            file_put_contents($path, $yaml);
        } catch (Exception $e) {
            if (str_contains($e->getMessage(), 'Failed to open stream: Permission denied in')) {
                $this->messageManager->addWarningMessage(
                    __(
                        'There is no permission to export files. Please set Write access to the folder "%1" to export',
                        $this->config->printPath($this->config->getTemplatePath())
                    )
                );
            } else {
                $this->messageManager->addWarningMessage($e->getMessage());
            }
        }

        return $path;
    }

    /**
     * @return $this
     */
    public function import(string $relPath)
    {
        $absPath = $this->config->absolutePath($relPath);

        if (file_exists($absPath) && is_readable($absPath)) {
            $content = file_get_contents($absPath);
            $data    = YamlService::parse($content);
            $model   = $this->collectionFactory->create()
                ->addFieldToFilter('name', $data['name'])
                ->getFirstItem();

            $model->addData($data)
                ->save();

            return $model;
        } elseif (php_sapi_name() != "cli") {
            $this->messageManager->addWarningMessage(__(
                'There is no permission to import files. Please set Read access to the templates folder.'
            ));
        }
    }

    public function getRowsToExport(): array
    {

        $array = [
            'name',
            'type',
            'format_serialized',
            'csv_delimiter',
            'csv_enclosure',
            'csv_include_header',
            'csv_extra_header',
            'csv_schema',
        ];

        return $array;
    }
}
