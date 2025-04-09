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

namespace Mirasvit\Feed\Model\Feed;

use Exception;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Filesystem\Io\Ftp;
use Magento\Framework\Filesystem\Io\Sftp;
use Mirasvit\Feed\Model\Feed;

class Deliverer
{

    const CONNECTION_TIMEOUT = 10;

    protected $sftp;

    protected $ftp;

    protected $eventManager;

    public function __construct(
        Sftp         $sftp,
        Ftp          $ftp,
        EventManager $eventManager
    ) {
        $this->sftp         = $sftp;
        $this->ftp          = $ftp;
        $this->eventManager = $eventManager;
    }

    public function delivery(Feed $feed): bool
    {
        try {
            if ($feed->isFeedAvailable()) {
                $this->uploadFile($feed, $feed->getFilePath(), $feed->getFilename());
            } else {
                throw new Exception('Please generate the feed before delivery');
            }

            $this->eventManager->dispatch('feed_delivery_success', ['feed' => $feed]);
        } catch (Exception $e) {
            $this->eventManager->dispatch('feed_delivery_fail', ['feed' => $feed, 'error' => $e->getMessage()]);

            throw $e;
        }

        return true;
    }

    public function validate(Feed $feed): bool
    {
        return $this->uploadFile($feed, __FILE__, 'deliverer.txt');
    }

    protected function uploadFile(Feed $feed, string $source, string $filename): bool
    {
        if ($feed->getFtpProtocol() == 'ftp') {
            $args = [
                'host'     => $feed->getFtpHost(),
                'user'     => $feed->getFtpUser(),
                'password' => $feed->getFtpPassword(),
                'passive'  => (bool)$feed->getFtpPassiveMode(),
                'path'     => $feed->getFtpPath(),
                'timeout'  => self::CONNECTION_TIMEOUT,
                'ssl'      => true
            ];

            $this->ftp->open($args);
            $this->ftp->cd($feed->getFtpPath());
            $this->ftp->write($filename, $source);
            $this->ftp->close();
        } elseif ($feed->getFtpProtocol() == 'sftp') {

            if (!defined("NET_SFTP_LOCAL_FILE")) {
                define("NET_SFTP_LOCAL_FILE", 1);
            }
            if (!defined("NET_SFTP_STRING")) {
                define("NET_SFTP_STRING", 2);
            }

            $args = [
                'host'     => $feed->getFtpHost(),
                'username' => $feed->getFtpUser(),
                'password' => $feed->getFtpPassword(),
                'timeout'  => self::CONNECTION_TIMEOUT,
            ];

            $this->sftp->open($args);
            $this->sftp->cd($feed->getFtpPath());
            $this->sftp->write($filename, $source);
            $this->sftp->close();
        }

        return true;
    }
}
