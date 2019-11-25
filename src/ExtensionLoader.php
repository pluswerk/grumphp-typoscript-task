<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptLinter;

use GrumPHP\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class ExtensionLoader implements ExtensionInterface
{
    /**
     * @param ContainerBuilder $container
     *
     * @return Definition
     */
    public function load(ContainerBuilder $container): Definition
    {
        $container->register('linter.typoscriptlint', TypoScriptLinter::class);
        return $container->register('task.typoscriptlint', TypoScriptLint::class)
            ->addArgument(new Reference('config'))
            ->addArgument(new Reference('linter.typoscriptlint'))
            ->addTag('grumphp.task', ['config' => 'typoscriptlint']);
    }
}
