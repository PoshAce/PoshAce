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

namespace Magetrend\Affiliate\Model;

class ProgramManager
{
    public $programFactory;

    public $json;

    public function __construct(
        \Magetrend\Affiliate\Model\ProgramFactory $programFactory,
        \Magento\Framework\Json\Helper\Data $json
    ) {
        $this->programFactory = $programFactory;
        $this->json = $json;
    }

    public function deleteProgram($id)
    {
        $model = $this->programFactory->create()
            ->load($id);
        if ($model->getId()) {
            $model->delete();
        }
    }

    public function saveProgram($data, $id)
    {
        $model = $this->programFactory->create();
        if ($id) {
            $model->load($id);
        }

        if (isset($data['commission'])) {
            $data['commission'] = $this->json->jsonEncode($data['commission']);
        }

        if (isset($data['coupon'])) {
            $data['coupon'] = $this->json->jsonEncode($data['coupon']);
        }

        $model->addData($data);
        $model->save();

        return $model;
    }
}
