<?php

namespace cmath10\MapperBundle\DependencyInjection;

use cmath10\Mapper\MapperInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class MapperCompilerPass implements CompilerPassInterface
{
    public const TAG = 'cmath10.mapper.map';

    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(MapperInterface::class)) {
            $definition = $container->getDefinition(MapperInterface::class);
            foreach ($container->findTaggedServiceIds(self::TAG) as $id => $attributes) {
                $definition->addMethodCall('register', [new Reference($id)]);
            }
        }
    }
}
