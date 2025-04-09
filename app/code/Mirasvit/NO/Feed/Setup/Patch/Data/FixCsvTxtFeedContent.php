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

namespace Mirasvit\Feed\Setup\Patch\Data;

use Exception;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Mirasvit\Feed\Model\Feed;
use Mirasvit\Feed\Model\ResourceModel\Feed as FeedResource;
use Mirasvit\Feed\Model\ResourceModel\Feed\CollectionFactory;
use Mirasvit\Feed\Service\Serialize;
use Psr\Log\LoggerInterface;

/**
 * Patch to fix CSV and TXT feeds where modifier arguments were stored incorrectly, which could cause
 * the "Content Settings" tab on the feed edit page in the admin panel to load endlessly.
 * In version 1.3.13 of the module, the saving of modifier arguments has been fixed.
 */
class FixCsvTxtFeedContent implements DataPatchInterface
{
    const MODIFIER_ARGUMENTS = [
        'replace'            => 2,
        'lowercase'          => 0,
        'uppercase'          => 0,
        'append'             => 1,
        'prepend'            => 1,
        'capitalize'         => 0,
        'escape'             => 0,
        'html_entity_decode' => 0,
        'nl2br'              => 0,
        'remove'             => 1,
        'stripHtml'          => 0,
        'strip_newlines'     => 0,
        'newline_to_br'      => 0,
        'truncate'           => 1,
        'truncatewords'      => 1,
        'split'              => 1,
        'plain'              => 0,
        'ifEmpty'            => 1,
        'dateFormat'         => 1,
        'rtrim'              => 1,
        'json'               => 0,
        'clean'              => 0,
        'convert'            => 1,
        'inclTax'            => 1,
        'exclTax'            => 1,
        'first'              => 0,
        'last'               => 0,
        'join'               => 1,
        'count'              => 0,
        'select'             => 1,
        'toPlain'            => 0,
        'plus'               => 1,
        'minus'              => 1,
        'times'              => 1,
        'divided_by'         => 1,
        'modulo'             => 1,
        'ceil'               => 0,
        'floor'              => 0,
        'round'              => 1,
        'numberFormat'       => 3,
        'price'              => 0,
        'secure'             => 0,
        'unsecure'           => 0,
        'mediaSecure'        => 0,
        'mediaUnsecure'      => 0,
        'replaceMediaUrl'    => 2,
        'resize'             => 2
    ];

    private $moduleDataSetup;

    private $feedCollectionFactory;

    private $feedResource;

    private $serialize;

    private $logger;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CollectionFactory        $feedCollectionFactory,
        FeedResource             $feedResource,
        Serialize                $serialize,
        LoggerInterface          $logger
    ) {
        $this->moduleDataSetup       = $moduleDataSetup;
        $this->feedCollectionFactory = $feedCollectionFactory;
        $this->feedResource          = $feedResource;
        $this->serialize             = $serialize;
        $this->logger                = $logger;
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        try {
            $feeds = $this->feedCollectionFactory->create();
            $feeds->addFieldToFilter('type', ['in' => ['csv', 'txt']]);

            /** @var Feed $feed */
            foreach ($feeds as $feed) {
                $format = $this->serialize->unserialize($feed->getFormatSerialized());
                $changed = false;

                if (isset($format['schema'])) {
                    foreach ($format['schema'] as $fieldId => $field) {
                        if (isset($field['modifiers'])) {
                            foreach ($field['modifiers'] as $modifierId => $modifier) {
                                if (isset($modifier['modifier']) && isset($modifier['args'])) {
                                    if ($modifier['modifier'] === '') {
                                        unset($format['schema'][$fieldId]['modifiers'][$modifierId]['args']);
                                        $changed = true;

                                        continue;
                                    }

                                    if (array_key_exists($modifier['modifier'], self::MODIFIER_ARGUMENTS)) {
                                        $length = self::MODIFIER_ARGUMENTS[$modifier['modifier']];

                                        if (count($modifier['args']) !== $length) {
                                            if ($length === 0) {
                                                unset($format['schema'][$fieldId]['modifiers'][$modifierId]['args']);
                                            } else {
                                                $format['schema'][$fieldId]['modifiers'][$modifierId]['args'] = array_slice($modifier['args'], 0, $length);
                                            }

                                            $changed = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if ($changed) {
                    $feed->setFormatSerialized($this->serialize->serialize($format));
                    $this->feedResource->save($feed);
                }
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function getAliases()
    {
        return [];
    }

    public static function getDependencies()
    {
        return [];
    }
}
