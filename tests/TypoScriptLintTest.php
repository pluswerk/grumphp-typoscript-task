<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptLinter\Tests;

use GrumPHP\Collection\FilesCollection;
use GrumPHP\Configuration\GrumPHP;
use GrumPHP\Exception\RuntimeException;
use GrumPHP\Runner\TaskResult;
use GrumPHP\Task\Context\RunContext;
use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptLinter\TypoScriptLint;
use Pluswerk\TypoScriptLinter\TypoScriptLinter;

/**
 * Class TypoScriptLintTest
 * @package Pluswerk\TypoScriptLinter\Tests
 * @covers \Pluswerk\TypoScriptLinter\TypoScriptLint
 * @covers \Pluswerk\TypoScriptLinter\TypoScriptLinter
 * @covers \Pluswerk\TypoScriptLinter\TypoScriptLintError
 */
final class TypoScriptLintTest extends TestCase
{
    /**
     * @test
     * @dataProvider failingTestFilesProvider
     */
    public function ifTypoScriptLinterDetectsAFailureTheTaskFails($filePath): void
    {
        $grumphp = $this->createMock(GrumPHP::class);
        $typoScriptLinter = new TypoScriptLinter();

        $file = new \SplFileInfo($filePath);
        $fileCollection = new FilesCollection();
        $fileCollection->add($file);
        $context = new RunContext($fileCollection);

        $typoScriptLint = new TypoScriptLint($grumphp, $typoScriptLinter);

        $result = $typoScriptLint->run($context);

        $this->assertSame(TaskResult::FAILED, $result->getResultCode());
    }

    public function failingTestFilesProvider(): array
    {
        $basePath = __DIR__ . '/Fixtures/';
        return [
            'nesting' => [
                'filePath' => $basePath . 'nesting.typoscript'
            ],
            'whitespace 01' => [
                'filePath' => $basePath . 'whitespace_01.typoscript'
            ],
            'whitespace 02' => [
                'filePath' => $basePath . 'whitespace_02.typoscript'
            ],
            'dead code' => [
                'filePath' => $basePath . 'deadCode.typoscript'
            ],
            'indentation' => [
                'filePath' => $basePath . 'indentation.typoscript'
            ],
            'repeating values' => [
                'filePath' => $basePath . 'repeatingValues.typoscript'
            ],
            'duplicate assignment' => [
                'filePath' => $basePath . 'duplicateAssignment.typoscript'
            ],
            'empty blocks' => [
                'filePath' => $basePath . 'emptyBlocks.typoscript'
            ]
        ];
    }

    /**
     * @test
     */
    public function ifNoFilesAreGivenTheTaskIsSkipped(): void
    {
        $grumphp = $this->createMock(GrumPHP::class);
        $typoScriptLinter = new TypoScriptLinter();

        $fileCollection = new FilesCollection();
        $context = new RunContext($fileCollection);

        $typoScriptLint = new TypoScriptLint($grumphp, $typoScriptLinter);

        $result = $typoScriptLint->run($context);

        $this->assertSame(TaskResult::SKIPPED, $result->getResultCode());
    }

    /**
     * @test
     */
    public function ifNoLintingFailuresDetectedTheTaskPasses(): void
    {
        $grumphp = $this->createMock(GrumPHP::class);
        $typoScriptLinter = new TypoScriptLinter();

        $file = new \SplFileInfo(__DIR__ . '/Fixtures/passing.typoscript');
        $fileCollection = new FilesCollection();
        $fileCollection->add($file);
        $context = new RunContext($fileCollection);

        $typoScriptLint = new TypoScriptLint($grumphp, $typoScriptLinter);

        $result = $typoScriptLint->run($context);

        $this->assertSame(TaskResult::PASSED, $result->getResultCode());
    }
    
    /**
     * @test
     */
    public function ifRuntimeExceptionIsThrownDuringLintingTheTaskFails(): void
    {
        $grumphp = $this->createMock(GrumPHP::class);
        $typoScriptLinter = $this->createMock(TypoScriptLinter::class);

        $file = new \SplFileInfo(__DIR__ . '/Fixtures/passing.typoscript');
        $fileCollection = new FilesCollection();
        $fileCollection->add($file);
        $context = new RunContext($fileCollection);

        $typoScriptLint = new TypoScriptLint($grumphp, $typoScriptLinter);

        $typoScriptLinter->expects($this->at(1))->method('isInstalled')->willReturn(true);
        $typoScriptLinter->expects($this->at(2))->method('lint')->willThrowException(new RuntimeException());

        $result = $typoScriptLint->run($context);

        $this->assertSame(TaskResult::FAILED, $result->getResultCode());
    }
}
