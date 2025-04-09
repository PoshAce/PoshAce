<?php

namespace Magecomp\Mobilelogin\Plugin;

class EavValidationRules
{
    public function afterBuild(\Magento\Ui\DataProvider\EavValidationRules $subject, $result, $attribute)
    {
        if ($attribute->getAttributeCode() == "mobilenumber") {
            $validationClasses = explode(' ', $attribute->getFrontendClass());
            $rules = [];
            foreach ($validationClasses as $class) {
                $rules[$class] = true;
            }

            return $rules;
        }
    }
}
