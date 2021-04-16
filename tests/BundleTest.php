<?php

namespace cmath10\MapperBundle\Tests;

use cmath10\Mapper\Mapper;
use cmath10\Mapper\MapperInterface;
use cmath10\MapperBundle\CMath10MapperBundle;
use cmath10\MapperBundle\DependencyInjection\MapperCompilerPass;
use cmath10\MapperBundle\Tests\Infrastructure\CompilerPass\PublicAccessForServicesPass;
use cmath10\MapperBundle\Tests\Infrastructure\TestKernel;
use LogicException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Contracts\Service\ResetInterface;
use function uniqid;

final class BundleTest extends TestCase
{
    private ?TestKernel $kernel = null;

    protected function getBundleClass(): string
    {
        return CMath10MapperBundle::class;
    }

    public function testInit(): void
    {
        $this->bootKernel();

        $container = $this->kernel->getContainer();

        self::assertTrue($container->has('cmath10_mapper.mapper'));

        $mapper = $container->get('cmath10_mapper.mapper');

        self::assertInstanceOf(Mapper::class, $mapper);
    }

    public function testMapping(): void
    {
        $kernel = $this->createKernel();
        $kernel->setConfigureClosure(static function (ContainerBuilder $container) {
            $def = new Definition(Fixture\Map::class);
            $def->addTag(MapperCompilerPass::TAG);

            $container->setDefinition(Fixture\Map::class, $def);
        });

        $this->bootKernel();

        $container = $this->kernel->getContainer();

        self::assertTrue($container->has(Fixture\Map::class), 'Map class registered');

        /** @var MapperInterface $mapper */
        $mapper = $container->get('cmath10_mapper.mapper');

        $input = new Fixture\Input();
        $input->text = 'test';

        $output = $mapper->map($input, Fixture\Output::class);

        self::assertInstanceOf(Fixture\Output::class, $output);
        self::assertEquals('test', $output->text);
    }

    private function bootKernel(): void
    {
        $this->ensureKernelShutdown();

        if (null === $this->kernel) {
            $this->createKernel();
        }

        $this->kernel->boot();
    }

    private function createKernel(): TestKernel
    {
        $this->kernel = new TestKernel(uniqid('cache', false));
        $this->kernel->addBundle(CMath10MapperBundle::class);
        $this->kernel->addCompilerPasses([
            new PublicAccessForServicesPass(),
        ]);

        return $this->kernel;
    }

    /**
     * Shuts the kernel down if it was used in the test.
     *
     * @after
     */
    public function ensureKernelShutdown(): void
    {
        if (null !== $this->kernel) {
            try {
                $container = $this->kernel->getContainer();
            } catch (LogicException $e) {
                $container = null;
            }
            $this->kernel->shutdown();
            if ($container instanceof ResetInterface) {
                $container->reset();
            }
        }
    }
}
