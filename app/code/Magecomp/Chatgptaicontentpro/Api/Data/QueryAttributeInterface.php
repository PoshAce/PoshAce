<?php

namespace Magecomp\Chatgptaicontentpro\Api\Data;

interface QueryAttributeInterface
{
    public function getValue(): string;
    public function getName(): string;
}
