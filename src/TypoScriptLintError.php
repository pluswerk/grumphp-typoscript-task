<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptLinter;

use GrumPHP\Linter\LintError;
use Helmich\TypoScriptLint\Linter\Report\File;
use Helmich\TypoScriptLint\Linter\Report\Issue;
use Helmich\TypoScriptParser\Parser\ParseError;

final class TypoScriptLintError extends LintError
{
    public static function fromIssue(Issue $issue, string $filePathName): self
    {
        return new self(
            $issue->getSeverity(),
            $issue->getMessage(),
            $filePathName,
            $issue->getLine() ?? -1
        );
    }
}
