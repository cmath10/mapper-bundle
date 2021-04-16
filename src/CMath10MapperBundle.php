<?php

namespace cmath10\MapperBundle;

use cmath10\MapperBundle\DependencyInjection\MapperCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CMath10MapperBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new MapperCompilerPass());
    }
}
