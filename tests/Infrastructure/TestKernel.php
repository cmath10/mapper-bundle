<?php

namespace cmath10\MapperBundle\Tests\Infrastructure;

use Closure;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use function array_unique;

final class TestKernel extends Kernel
{
    /** @var string[] */
    private array $bundlesToRegister = [];

    private string $cachePrefix;

    private ?Closure $configureClosure = null;

    /** @var CompilerPassInterface[] */
    private array $compilerPasses = [];

    public function __construct(string $cachePrefix)
    {
        parent::__construct($cachePrefix, true);

        $this->cachePrefix = $cachePrefix;

        $this->addBundle(FrameworkBundle::class);
    }

    public function addBundle(string $FQN): void
    {
        $this->bundlesToRegister[] = $FQN;
    }

    /**
     * @param CompilerPassInterface[] $compilerPasses
     */
    public function addCompilerPasses(array $compilerPasses): void
    {
        $this->compilerPasses = $compilerPasses;
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir(). '/CMath10MapperBundleTest/' . $this->cachePrefix;
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/CMath10MapperBundleTest/log';
    }

    public function getProjectDir(): string
    {
        return realpath(__DIR__ . '/../../../../');
    }

    public function setConfigureClosure(Closure $closure): void
    {
        $this->configureClosure = $closure;
    }

    public function registerBundles(): array
    {
        $this->bundlesToRegister = array_unique($this->bundlesToRegister);
        $bundles = [];
        foreach ($this->bundlesToRegister as $bundle) {
            $bundles[] = new $bundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->loadFromExtension('framework', [
                'annotations' => false,
                'csrf_protection' => false,
                'form' => false,
                'router' => [
                    'type' => 'service',
                    'resource' => null,
                ],
                'secret' => 'test',
                'session' => false,
                'test' => false,
                'translator' => false,
                'validation' => ['enabled' => false],
            ]);

            if ($this->configureClosure !== null) {
                $configureClosure = $this->configureClosure;
                $configureClosure($container);
            }

            $container->addObjectResource($this);
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function buildContainer()
    {
        $container = parent::buildContainer();

        foreach ($this->compilerPasses as $pass) {
            $container->addCompilerPass($pass);
        }

        return $container;
    }
}
