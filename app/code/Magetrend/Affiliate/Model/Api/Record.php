<?php
/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * @category MageTrend
 * @package  Magetend/Affiliate
 * @author   Edvinas St. <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.magetrend.com/magento-2-affiliate
 */

namespace Magetrend\Affiliate\Model\Api;

class Record implements \Magetrend\Affiliate\Api\RecordInterface
{
    public $request;

    public $recordManager;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magetrend\Affiliate\Model\RecordManager $recordManager
    ) {
        $this->recordManager = $recordManager;
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function recordClick()
    {
        $data = $this->request->getParams();
        $this->recordManager->recordClick($data);
    }

    /**
     * @return string
     */
    public function recordInvoice()
    {
        $data = $this->request->getParams();
        $this->recordManager->recordTransaction($data);
    }

    /**
     * @return string
     */
    public function recordCreditmemo()
    {
        $data = $this->request->getParams();
        $this->recordManager->recordTransaction($data);
    }

    /**
     * @return string
     */
    public function recordOrder()
    {
        $data = $this->request->getParams();
        $this->recordManager->recordOrder($data);
    }
}
