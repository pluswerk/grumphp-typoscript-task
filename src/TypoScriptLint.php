<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptLinter;

use GrumPHP\Exception\RuntimeException;
use GrumPHP\Runner\TaskResult;
use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\AbstractLinterTask;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use Pluswerk\TypoScriptLinter\Sniff\RepeatingRValueSniff;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TypoScriptLint extends AbstractLinterTask
{
    /**
     * @var TypoScriptLinter
     */
    protected $linter;

    public function getName(): string
    {
        return 'typoscriptlint';
    }

    public function getConfigurableOptions(): OptionsResolver
    {
        $resolver = parent::getConfigurableOptions();
        $resolver->setDefaults(
            [
                'triggered_by' => ['typoscript'],
                'sniffs' => [
                    0 => [
                        'class'      => 'Indentation',
                        'parameters' => [
                            'useSpaces'        => true,
                            'indentPerLevel'   => 2,
                            'indentConditions' => false,
                        ],
                    ],
                    1 => [
                        'class' => 'DeadCode',
                    ],
                    2 => [
                        'class' => 'OperatorWhitespace',
                    ],
                    3 => [
                        'class' => RepeatingRValueSniff::class,
                        'parameters' => [
                            'ignoreClassNameValues' => true,
                            'ignorePatterns' => []
                        ]
                    ],
                    4 => [
                        'class' => 'DuplicateAssignment',
                    ],
                    5 => [
                        'class' => 'EmptySection',
                    ],
                    6 => [
                        'class'      => 'NestingConsistency',
                        'parameters' => [
                            'commonPathPrefixThreshold' => 1,
                        ],
                    ],
                ],
                'paths'        => [],
                'filePatterns' => [],
            ]
        );
        $resolver->addAllowedTypes('triggered_by', ['array']);
        $resolver->addAllowedTypes('sniffs', ['array']);
        $resolver->addAllowedTypes('paths', ['array']);
        $resolver->addAllowedTypes('filePatterns', ['array']);
        return $resolver;
    }

    public function canRunInContext(ContextInterface $context): bool
    {
        return ($context instanceof GitPreCommitContext || $context instanceof RunContext);
    }

    public function run(ContextInterface $context): TaskResultInterface
    {
        $config = $this->getConfiguration();
        $this->linter->initializeConfiguration($config);
        $files = $context->getFiles()->extensions($config['triggered_by']);

        if (0 === count($files)) {
            return TaskResult::createSkipped($this, $context);
        }

        try {
            $lintErrors = $this->lint($files);
        } catch (RuntimeException $exception) {
            return TaskResult::createFailed($this, $context, $exception->getMessage());
        }

        if ($lintErrors->count()) {
            return TaskResult::createFailed($this, $context, (string)$lintErrors);
        }

        return TaskResult::createPassed($this, $context);
    }
}
