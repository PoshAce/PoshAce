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

namespace Mirasvit\Feed\Export\Step;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;
use Mirasvit\Feed\Export\Context;
use Mirasvit\Feed\Export\Liquid\Tag\TagFor;
use Mirasvit\Feed\Export\Resolver\GeneralResolver;
use Mirasvit\Core\Helper\Io;
use Mirasvit\Feed\Model\Config;
use Mirasvit\Feed\Export\Liquid\Context as LiquidContext;
use Mirasvit\Feed\Export\Liquid\Template as LiquidTemplate;

class Exporting extends AbstractStep
{
    const STEP = 'exporting';

    protected $resource;

    protected $resolver;

    protected $config;

    protected $io;

    protected $objectManager;

    protected $liquidTemplate;

    public function __construct(
        ResourceConnection     $resource,
        Io                     $io,
        Config                 $config,
        GeneralResolver        $resolver,
        ObjectManagerInterface $objectManager,
        Context                $context
    ) {
        $this->resource      = $resource;
        $this->resolver      = $resolver;
        $this->config        = $config;
        $this->io            = $io;
        $this->objectManager = $objectManager;

        parent::__construct($context);
    }

    public function beforeExecute()
    {
        parent::beforeExecute();

        $this->length = $this->resolver->getProducts()->getSize();
        $this->index  = 0;
    }

    public function execute()
    {
        if ($this->isReady()) {
            $this->beforeExecute();
        }

        $index = $this->index;

        $template = $this->context->getFeed()->getLiquidTemplate();

        $liquidState = [];
        if (isset($this->data['liquid'])) {
            $liquidState = $this->data['liquid'];
        }

        $liquidTemplate = new LiquidTemplate();
        $liquidTemplate->parse($template, $this->context->getFeed()->isXml())
            ->fromArray($liquidState);

        $this->liquidTemplate = $liquidTemplate;

        $liquidContext = new LiquidContext($this->resolver, []);

        $liquidContext->addFilters($this->objectManager->get('\Mirasvit\Feed\Export\Filter\Pool')->getScopes());

        $liquidContext->setTimeoutCallback([$this->context, 'isTimeout'])
            ->setIterationCallback([$this, 'onIndexUpdate']);

        $liquidContext->setProductExportStep($this->context->getProductExportStep());

        $result = $liquidTemplate->execute($liquidContext);

        if ($this->context->getFeed()->getFbMetadataEnabled()) {
            $result = $this->addFacebookMetadata($result);
        }

        $filePath = $this->config->getTmpPath() . DIRECTORY_SEPARATOR . $this->context->getFeed()->getId() . '.dat';

        // remove duplicate break lines
        $result = preg_replace("/[\r\n]+/", "\n", $result);

        // remove trailing newline if a feed is completed
        if ($this->isCompleted()) {
            $result = rtrim($result);
        }

        $this->io->write($filePath, $result, 'a');

        $this->data['liquid'] = $liquidTemplate->toArray();

        if ($this->index == $index) {
            #index was not changed
            $this->index = $this->length;
        }

        if ($this->isCompleted()) {
            $this->afterExecute();
        }
    }

    public function afterExecute()
    {
        $this->setData('count', $this->getLength());

        return parent::afterExecute();
    }

    public function getLength(): int
    {
        return $this->length - $this->getSubtractLength();
    }

    public function onIndexUpdate(array $iteration)
    {
        $this->index  = $iteration['index'];
        $this->length = $iteration['length'];
    }

    private function getSubtractLength(): int
    {
        $subtractNum    = 0;
        $liquidTemplate = $this->liquidTemplate;

        if (!$liquidTemplate) {
            $liquidTemplate = new LiquidTemplate();
            $liquidTemplate->parse($this->context->getFeed()->getLiquidTemplate());
        }

        foreach ($liquidTemplate->getRoot()->getNodeList() as $tag) {
            if (!$tag instanceof TagFor) {
                $subtractNum++;
            }
        }

        return $subtractNum;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function addFacebookMetadata(string $result): string
    {
        $feed     = $this->context->getFeed();
        $feedId   = $feed->getId();
        $feedType = $feed->getType();
        $refAppId = $this->config->getFacebookAppID();
        $storeUrl = parse_url($feed->getStore()->getBaseUrl(), PHP_URL_HOST);
        $metaTags = "<metadata>"
            . "\n\t\t" . "<ref_application_id>$refAppId</ref_application_id>"
            . "\n\t\t" . "<ref_asset_id>$storeUrl-$feedId</ref_asset_id>"
            . "\n\t" . "</metadata>" . "\n";

        if (strpos($result, '<entry>') !== false) {
            // set metadata in Atom XML feeds
            $result = str_replace('<entry>', $metaTags . "\n\t" . '<entry>', $result);
        } elseif (strpos($result, 'xmlns:g="http://base.google.com/ns/1.0"') !== false) {
            // set metadata in RSS XML feeds
            $result = str_replace('<channel>', '<channel>' . "\n\t" . $metaTags, $result);
        } elseif ($feedType == 'csv' || $feedType == 'txt') {
            // set metadata in csv and txt feeds
            if (preg_match('/(fb|facebook)/', strtolower($feed->getName())) == 1 ||
                preg_match('/(fb|facebook)/', strtolower($feed->getFilename())) == 1
            ) {
                if ($this->context->getFeed()->getData('csv_extra_header') == '' &&
                    strpos($result, 'id') !== false &&
                    strpos($result, 'link') !== false &&
                    strpos($result, 'title') !== false &&
                    strpos($result, 'description') !== false &&
                    strpos($result, 'availability') !== false &&
                    strpos($result, 'image_link') !== false &&
                    strpos($result, 'price') !== false
                ) {
                    $metaTags = "# ref_application_id $refAppId\n" .
                        "# ref_asset_id $storeUrl" . '-' . $feedId . "\n";
                    $result   = $metaTags . $result;
                }
            }
        }

        return $result;
    }
}
