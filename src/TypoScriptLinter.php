<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptLinter;

use GrumPHP\Collection\LintErrorsCollection;
use GrumPHP\Linter\LinterInterface;
use Helmich\TypoScriptLint\Linter\LinterConfiguration;
use Helmich\TypoScriptLint\Linter\Report\File;
use Helmich\TypoScriptLint\Linter\Report\Issue;
use Helmich\TypoScriptLint\Linter\Report\Report;
use Helmich\TypoScriptLint\Linter\Sniff\SniffLocator;
use Helmich\TypoScriptLint\Logging\LinterLoggerInterface;
use Helmich\TypoScriptParser\Parser\AST\Statement;
use Helmich\TypoScriptParser\Parser\ParseError;
use Helmich\TypoScriptParser\Parser\Parser;
use Helmich\TypoScriptParser\Tokenizer\Tokenizer;
use Helmich\TypoScriptParser\Tokenizer\TokenizerException;
use SplFileInfo;
use Symfony\Component\Config\Definition\Processor;

final class TypoScriptLinter implements LinterInterface
{
    /**
     * @var Tokenizer
     */
    private $tokenizer;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var SniffLocator
     */
    private $sniffLocator;

    /**
     * @var LinterConfiguration
     */
    private $configuration;

    /**
     * @var Processor
     */
    private $processor;

    public function __construct()
    {
        $this->tokenizer = new Tokenizer();
        $this->parser = new Parser($this->tokenizer);
        $this->sniffLocator = new SniffLocator();
        $this->configuration = new LinterConfiguration();
        $this->processor = new Processor();
    }

    /**
     * @param array $configArray
     */
    public function initializeConfiguration(array $configArray): void
    {
        $preProcessedConfig = $configArray;
        // Remove grumphp configuration, which is not allowed in TypoScript linter.
        unset($preProcessedConfig['ignore_patterns'], $preProcessedConfig['triggered_by']);
        $processedConfiguration = $this->processor->processConfiguration($this->configuration, [$preProcessedConfig]);
        $this->configuration->setConfiguration($processedConfiguration);
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $file
     *
     * @return LintErrorsCollection
     * @throws \Exception
     */
    public function lint(SplFileInfo $file): LintErrorsCollection
    {
        $filename = $file->getPathname();
        $file = new File($filename);
        $lintErrors = new LintErrorsCollection();

        try {
            $tokens     = $this->tokenizer->tokenizeStream($filename);
            $statements = $this->parser->parseTokens($tokens);

            $file = $this->lintTokenStream($tokens, $file, $this->configuration);
            $file = $this->lintSyntaxTree($statements, $file, $this->configuration);
        } catch (TokenizerException $tokenizerException) {
            $file->addIssue(Issue::createFromTokenizerError($tokenizerException));
        } catch (ParseError $parseError) {
            $file->addIssue(Issue::createFromParseError($parseError));
        }

        foreach ($file->getIssues() as $issue) {
            $lintErrors->add(TypoScriptLintError::fromIssue($issue, $filename));
        }

        return $lintErrors;
    }

    /**
     * @param array               $tokens
     * @param File                $file
     * @param LinterConfiguration $configuration
     *
     * @return File
     * @throws \Exception
     */
    private function lintTokenStream(
        array $tokens,
        File $file,
        LinterConfiguration $configuration
    ): File {
        $sniffs = $this->sniffLocator->getTokenStreamSniffs($configuration);

        foreach ($sniffs as $sniff) {
            $sniffReport = $file->cloneEmpty();

            $sniff->sniff($tokens, $sniffReport, $configuration);

            $file = $file->merge($sniffReport);
        }

        return $file;
    }

    /**
     * @param Statement[]           $statements
     * @param File                  $file
     * @param LinterConfiguration   $configuration
     * @param LinterLoggerInterface $logger
     * @return File
     */
    private function lintSyntaxTree(
        array $statements,
        File $file,
        LinterConfiguration $configuration
    ): File {
        $sniffs = $this->sniffLocator->getSyntaxTreeSniffs($configuration);

        foreach ($sniffs as $sniff) {
            $sniffReport = $file->cloneEmpty();

            $sniff->sniff($statements, $sniffReport, $configuration);

            $file = $file->merge($sniffReport);
        }

        return $file;
    }

    public function isInstalled(): bool
    {
        $extesions = get_loaded_extensions();
        return true;
    }
}
