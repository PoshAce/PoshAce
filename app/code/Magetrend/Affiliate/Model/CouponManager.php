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

class CouponManager
{
    public $programCollectionFactory;

    public $couponCollectionFactory;

    public $accountFactory;

    public $json;

    public $massgenerator;

    private $accountCoupons = null;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    public $ruleFactory;

    public $massgeneratorFactory;

    public $couponFactory;

    public function __construct(
        \Magetrend\Affiliate\Model\AccountFactory $accountFactory,
        \Magetrend\Affiliate\Model\ResourceModel\Program\CollectionFactory $programCollectionFactory,
        \Magetrend\Affiliate\Model\ResourceModel\Coupon\CollectionFactory $couponCollectionFactory,
        \Magento\Framework\Json\Helper\Data $json,
        \Magento\SalesRule\Model\Coupon\Massgenerator $massgenerator,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\SalesRule\Model\Coupon\MassgeneratorFactory $massgeneratorFactory,
        \Magetrend\Affiliate\Model\CouponFactory $couponFactory
    ) {
        $this->programCollectionFactory = $programCollectionFactory;
        $this->accountFactory = $accountFactory;
        $this->json = $json;
        $this->massgenerator = $massgenerator;
        $this->couponCollectionFactory = $couponCollectionFactory;
        $this->ruleFactory = $ruleFactory;
        $this->massgeneratorFactory = $massgeneratorFactory;
        $this->couponFactory = $couponFactory;
    }

    public function createCoupons($accountId)
    {
        $account = $this->accountFactory->create()
            ->load($accountId);

        $programIds = $account->getProgram();

        $accountPrograms = $this->programCollectionFactory->create()
            ->addFieldToFilter('entity_id', ['in' => $programIds]);

        if ($accountPrograms->getSize() == 0) {
            return;
        }

        foreach ($accountPrograms as $program) {
            if ($program->getCouponIsActive() != 1) {
                continue;
            }
            $this->createCouponsByProgram($account, $program);
        }
    }

    public function createCouponsByProgram($account, $program)
    {
        $cartPriceRules = $program->getCoupon();
        if (empty($cartPriceRules)) {
            return;
        }

        if (!is_array($cartPriceRules)) {
            $cartPriceRules = $this->json->jsonDecode($cartPriceRules);
        }

        $acountCoupons = $this->getAccountCoupons($account->getId());
        $createdCouponsRulesIds = [];
        if ($acountCoupons->getSize() > 0) {
            foreach ($acountCoupons as $coupon) {
                $createdCouponsRulesIds[] = $coupon->getRuleId();
            }
        }

        foreach ($cartPriceRules as $ruleId) {
            if (in_array($ruleId, $createdCouponsRulesIds)) {
                continue;
            }

            $couponData = $this->getUniqueDiscountCode($ruleId, $program);
            if (empty($couponData)) {
                continue;
            }

            $this->couponFactory->create()
                ->setData([
                    'coupon_code' => $couponData->getCode(),
                    'rule_id' => $couponData->getRuleId(),
                    'coupon_id' => $couponData->getId(),
                    'referral_id' => $account->getId(),
                    'program_id' => $program->getId(),
                ])->save();
        }
    }

    public function getUniqueDiscountCode($ruleId, $program)
    {
        $rule = $this->ruleFactory->create()->load($ruleId);
        if (!$rule->getId()) {
            return '';
        }

        $codeGenerator = $this->massgeneratorFactory->create();
        $codeGenerator->setData('qty', 1);
        $codeGenerator->setData('rule_id', $rule->getId());
        $codeGenerator->setData('length', $program->getCouponLength());
        $codeGenerator->setData('format', $program->getCouponFormat());
        $codeGenerator->setData('prefix', $program->getCouponPrefix());
        $codeGenerator->setData('suffix', $program->getCouponSuffix());
        $codeGenerator->setData('dash', $program->getCouponDash());
        $codeGenerator->setData('uses_per_coupon', 1);
        $codeGenerator->setData('uses_per_customer', 1);

        $codeGenerator->generatePool();
        $latestCoupon = max($rule->getCoupons());

        if ($rule->getToDate()) {
            $latestCoupon->setData(
                'expiration_date',
                $rule->getToDate()
            )->save();
        }

        return $latestCoupon;
    }

    public function getAccountCoupons($accountId)
    {
        if ($this->accountCoupons == null) {
            $this->accountCoupons = $this->couponCollectionFactory->create()
                ->addFieldToFilter('referral_id', $accountId);
        }

        return $this->accountCoupons;
    }
}
