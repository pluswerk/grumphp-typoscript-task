<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptLinter\Tests;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptLinter\ExtensionLoader;
use Pluswerk\TypoScriptLinter\TypoScriptLint;
use Pluswerk\TypoScriptLinter\TypoScriptLinter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ExtensionLoaderTest
 * @package Pluswerk\TypoScriptLinter\Tests
 * @covers ExtensionLoader
 */
final class ExtensionLoaderTest extends TestCase
{
    /**
     * @test
     */
    public function typoscriptLinterIsRegisteredAsGrumphpTask(): void
    {
        $extensionLoader = new ExtensionLoader();

        $container = $this->createMock(ContainerBuilder::class);
        $definition = $this->createMock(Definition::class);
        $referenceA = new Reference('config');
        $referenceB = new Reference('linter.typoscriptlint');

        $container->expects($this->at(0))->method('register')->with('linter.typoscriptlint', TypoScriptLinter::class);
        $container->expects($this->at(1))
                  ->method('register')
                  ->with('task.typoscriptlint', TypoScriptLint::class)
                  ->willReturn($definition);

        $definition->expects($this->at(0))->method('addArgument')->with($referenceA)->willReturn($definition);
        $definition->expects($this->at(1))->method('addArgument')->with($referenceB)->willReturn($definition);
        $definition->expects($this->at(2))->method('addTag')->with('grumphp.task', ['config' => 'typoscriptlint'])->willReturn($definition);

        $extensionLoader->load($container);
    }
}
