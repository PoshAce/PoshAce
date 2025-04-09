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

namespace Mirasvit\Feed\Controller\Adminhtml\Feed;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Response\Http\Interceptor;
use Magento\Framework\Registry;
use Mirasvit\Core\Helper\Io;
use Mirasvit\Feed\Model\FeedFactory;
use Mirasvit\Feed\Model\Feed\Exporter;
use Mirasvit\Feed\Model\ResourceModel\Feed\CollectionFactory;
use Mirasvit\Feed\Model\TemplateFactory;
use Mirasvit\Feed\Helper\Data as Helper;

class Preview extends Save
{
    protected $exporter;

    protected $layout;

    protected $io;

    public function __construct(
        CollectionFactory $feedCollectionFactory,
        Exporter          $exporter,
        FeedFactory       $feedFactory,
        TemplateFactory   $templateFactory,
        Registry          $registry,
        Helper            $helper,
        Context           $context,
        Io                $io
    ) {
        $this->exporter = $exporter;
        $this->layout = $context->getView()->getLayout();
        $this->io = $io;

        parent::__construct($feedCollectionFactory, $feedFactory, $templateFactory, $registry, $helper, $context);
    }

    public function execute()
    {
        $feed = $this->initModel();

        /** @var Http $request */
        $request = $this->getRequest();

        if ($post = $request->getPostValue('data')) {
            //we receive form values as query string
            parse_str($post, $data);

            $data = $this->filterPostData($data);

            $feed->addData($data);
        }

        $contentType = 'text/html';

        try {
            $this->exporter->exportPreview($feed);
            $content = $this->io->fileGetContents($feed->getPreviewFilePath());

            if ($request->getPostValue()) {
                $content = $this->layout->createBlock('Magento\Backend\Block\Template')
                    ->setTemplate('Mirasvit_Feed::feed/preview.phtml')
                    ->setContent($content)
                    ->toHtml();
            } else {
                if ($feed->isXml()) {
                    $contentType = 'application/xml';
                } else {
                    $contentType = 'text/plain';
                }
            }
        } catch (Exception $e) {
            $content = $e;
        }

        /** @var Interceptor $response */
        $response = $this->getResponse();

        return $response
            ->setHeader('Content-Type', $contentType)
            ->setBody($content);
    }

    public function _processUrlKeys(): bool
    {
        return true;
    }
}
