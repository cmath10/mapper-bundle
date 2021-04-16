<?php

namespace cmath10\MapperBundle\Tests\Fixture;

use cmath10\Mapper\AbstractMap;

final class Map extends AbstractMap
{
    public function __construct()
    {
        $this->setupDefaults();
    }

    public function getSourceType(): string
    {
        return Input::class;
    }

    public function getDestinationType(): string
    {
        return Output::class;
    }
}
