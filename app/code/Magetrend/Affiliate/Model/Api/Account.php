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

class Account implements \Magetrend\Affiliate\Api\AccountInterface
{
    public $request;

    public $registrationManager;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magetrend\Affiliate\Model\RegistrationManager $registrationManager
    ) {
        $this->registrationManager = $registrationManager;
        $this->request = $request;
    }

    public function register()
    {
        $data = $this->request->getParams();
        $this->registrationManager->registerAccount($data);
    }
}
