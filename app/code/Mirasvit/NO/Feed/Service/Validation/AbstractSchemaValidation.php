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


namespace Mirasvit\Feed\Service\Validation;

use Mirasvit\Feed\Api\Data\ValidationInterface;
use Mirasvit\Feed\Repository\ValidationRepository;
use Mirasvit\Feed\Model\Feed;
use Mirasvit\Feed\Validator\ValidatorInterface;

abstract class AbstractSchemaValidation
{
    private   $validationRepository;

    protected $lineNum         = 0;

    protected $invalidEntities = [];

    protected $productIndex    = [];

    protected $feed            = null;

    protected $schemaPath      = null;

    public function __construct(ValidationRepository $validationRepository)
    {
        $this->validationRepository = $validationRepository;
    }

    abstract protected function canValidate(): bool;

    protected function validateValue(string $attribute, string $value, array $validators = []): array
    {
        $result = [];

        if ($this->getFeed()) {
            foreach ($validators as $validator) {
                if (!$validator->isValid($value)) {
                    $result[] = [
                        ValidationInterface::LINE_NUM  => $this->lineNum,
                        ValidationInterface::FEED_ID   => $this->getFeed()->getId(),
                        ValidationInterface::ATTRIBUTE => $attribute,
                        ValidationInterface::VALIDATOR => $validator->getCode(),
                        ValidationInterface::VALUE     => $value,
                    ];
                }
            }
        }

        return $result;
    }

    protected function getValidators(array $validators): array
    {
        $validatorInstances = [];
        foreach ($validators as $code) {
            if ($this->validationRepository->getValidatorByCode($code)) {
                $validatorInstances[] = $this->validationRepository->getValidatorByCode($code);
            }
        }

        return $validatorInstances;
    }

    public function getInvalidEntityQty(): int
    {
        return count($this->invalidEntities);
    }

    public function getFeed(): ?Feed
    {
        return $this->feed;
    }

    public function setFeed(Feed $feed)
    {
        $this->feed = $feed;
    }
}
