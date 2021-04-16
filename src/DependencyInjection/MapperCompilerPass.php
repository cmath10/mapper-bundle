<?php

namespace cmath10\MapperBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class MapperCompilerPass implements CompilerPassInterface
{
    public const ID = 'cmath10_mapper.mapper';
    public const TAG = 'cmath10_mapper.map';

    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(self::ID)) {
            $definition = $container->getDefinition(self::ID);
            foreach ($container->findTaggedServiceIds(self::TAG) as $id => $attributes) {
                $definition->addMethodCall('register', [new Reference($id)]);
            }
        }
    }
}
