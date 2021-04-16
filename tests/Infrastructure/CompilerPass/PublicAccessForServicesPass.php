<?php

namespace cmath10\MapperBundle\Tests\Infrastructure\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PublicAccessForServicesPass implements CompilerPassInterface
{
    /**
     * A regex to match the services that should be public.
     */
    private string $regex;

    /**
     * @param string $regex
     */
    public function __construct($regex = '|.*|')
    {
        $this->regex = $regex;
    }

    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            if (preg_match($this->regex, $id)) {
                $definition->setPublic(true);
            }
        }

        foreach ($container->getAliases() as $id => $alias) {
            if (preg_match($this->regex, $id)) {
                $alias->setPublic(true);
            }
        }
    }
}
