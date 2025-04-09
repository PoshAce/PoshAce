<?php
/**
 * Copyright © 2016 MB Vienas bitas. All rights reserved.
 * @website    www.magetrend.com
 * @package    MT Email for M2
 * @author     Edvinas Stulpinas <edwin@magetrend.com>
 */

namespace Magetrend\Email\Helper;

class Vars
{
    public $varRegister = null;

    public function resolveVariables($object)
    {
        if (empty($this->varRegister)) {
            return;
        }

        $data = $object->getData();
        $attributeCodes = array_keys($data);

        foreach ($this->varRegister as $key => $value) {
            if (!in_array($key, $attributeCodes)) {
                continue;
            }

            if (!empty($data[$key])) {
                continue;
            }

            $object->setData($key, $value);
        }
    }
}