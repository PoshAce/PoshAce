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

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection;
use Mirasvit\Feed\Service\Serialize;

/**
 * Abstract Template Model
 * @method string getType()
 * @method $this setType($type)
 * @method $this setFormat($format)
 * @method array getCsvSchema()
 * @method $this setCsvSchema(array $schema)
 * @method string getXmlSchema()
 * @method $this setXmlSchema($schema)
 */
abstract class AbstractTemplate extends AbstractModel
{
    protected $serializer;

    protected function _construct()
    {
        parent::_construct();
        $this->serializer = ObjectManager::getInstance()->get(
            Serialize::class
        );
    }

    protected function _afterLoad()
    {
        $this->extract();

        return parent::_afterLoad();
    }

    public function beforeSave()
    {
        $this->serializeFormat();

        return parent::beforeSave();
    }

    protected function serializeFormat(): self
    {
        if ($this->isCsv()) {
            if ($this->hasData('csv')) {
                $this->setData('format_serialized', $this->serializer->serialize($this->getData('csv')));
            }
        } else {
            if ($this->hasData('xml')) {
                $this->setData('format_serialized', $this->serializer->serialize($this->getData('xml')));
            }
        }

        return $this;
    }

    public function isCsv(): bool
    {
        return in_array($this->getType(), ['txt', 'csv']);
    }

    public function isXml(): bool
    {
        return !$this->isCsv();
    }

    public function isFeed(): bool
    {
        return $this instanceof Feed;
    }

    public function isTemplate(): bool
    {
        return $this instanceof Template;
    }

    protected function extract(): self
    {
        $data = $this->getData('format_serialized') ? $this->serializer->unserialize($this->getData('format_serialized')) : [];

        if ($this->isCsv()) {
            foreach ($data as $key => $value) {
                $this->setData('csv_' . $key, $value);
            }

            if (is_array($this->getCsvSchema())) {
                // sort columns by order
                $orders = [];
                $schema = $this->getCsvSchema();
                foreach ($schema as $key => $row) {
                    $orders[$key] = isset($row['order']) ? $row['order'] : 0;
                }
                array_multisort($orders, SORT_ASC, $schema);
                $this->setData('csv_schema', $schema);
            } else {
                $this->setCsvSchema([]);
            }
        } else {
            foreach ($data as $key => $value) {
                $this->setData('xml_' . $key, $value);
            }
        }

        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    public function getLiquidTemplate(): string
    {
        $this->serializeFormat()
            ->extract();

        $liquid = '';

        if ($this->isCsv()) {
            $delimiter = $this->getData('csv_delimiter') == 'tab' ? "\t" : $this->getData('csv_delimiter');
            $enclosure = $this->getData('csv_enclosure');

            if ($this->getData('csv_extra_header')) {
                $liquid .= $this->getData('csv_extra_header') . PHP_EOL;
            }

            if ($this->getData('csv_include_header')) {
                $headers = array_map(function ($column) {
                    $delimiter = $this->getData('csv_delimiter') == 'tab' ? "\t" : $this->getData('csv_delimiter');

                    if ($column['header'] == "XALL") {
                        $all = [];
                        foreach ($this->getAttributes() as $attribute) {
                            if ($attribute->getStoreLabel()) {
                                $all[] = $attribute->getStoreLabel();
                            }
                        }

                        return implode($delimiter, $all);
                    }

                    return $column['header'];
                }, $this->getCsvSchema());

                $liquid .= implode($delimiter, $headers) . PHP_EOL;
            }

            $liquid .= '{% for product in context.products %}';

            $columns = [];
            foreach ($this->getCsvSchema() as $column) {
                $variable = '';

                if ($column['header'] == "XALL") {
                    foreach ($this->getAttributes() as $attribute) {
                        if ($attribute->getStoreLabel()) {
                            $columns[] = '{{ product.' . $attribute->getAttributeCode() . ' }}';
                        }
                    }

                    continue;
                }

                if ($column['type'] == 'pattern') {
                    $variable .= $column['pattern'];
                } elseif (isset($column['attribute']) && $column['attribute']) {
                    $variable .= '{{ product';

                    if ($type = $column['type']) {
                        $variable .= '.' . $type;
                    }

                    $variable .= '.' . $column['attribute'];

                    $column['modifiers'][] = [
                        'modifier' => 'csv',
                        'args'     => [$delimiter, $enclosure],
                    ];

                    foreach ($column['modifiers'] as $modifier) {
                        if (!$modifier['modifier']) {
                            continue;
                        }

                        $modifier['args'] = isset($modifier['args']) ? $modifier['args'] : [];

                        $variable .= ' | ' . $modifier['modifier'];

                        $args = array_map(function ($arg) {
                            if (is_string($arg)) {
                                $arg = "'$arg'";
                            }

                            return $arg;
                        }, $modifier['args']);

                        if (count($args)) {
                            $variable .= ': ' . implode(', ', $args);
                        }
                    }

                    $variable .= ' }}';
                }

                $columns[] = $variable;
            }

            $liquid .= implode($delimiter, $columns) . PHP_EOL;

            $liquid .= '{% endfor %}';
        } else {
            $liquid = (string)$this->getXmlSchema();
        }

        return $liquid;
    }

    protected function getAttributes(): Collection
    {
        $om = ObjectManager::getInstance();
        /** @var Collection $collection */
        $collection = $om->create(Collection::class);
        $collection->addFieldToFilter("entity_type_id", 4);

        return $collection;
    }
}
