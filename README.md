[![Packagist](https://img.shields.io/packagist/v/pluswerk/grumphp-typoscript-task.svg?style=flat-square)](https://packagist.org/packages/pluswerk/grumphp-typoscript-task)
[![Packagist](https://img.shields.io/packagist/l/pluswerk/grumphp-typoscript-task.svg?style=flat-square)](https://opensource.org/licenses/LGPL-3.0)
[![Build Status](https://travis-ci.com/pluswerk/grumphp-typoscript-task.svg?branch=master)](https://travis-ci.com/pluswerk/grumphp-typoscript-task)
[![Coverage Status](https://img.shields.io/codecov/c/gh/pluswerk/grumphp-typoscript-task.svg?style=flat-square)](https://codecov.io/gh/pluswerk/grumphp-typoscript-task)
[![Quality Score](https://img.shields.io/scrutinizer/g/pluswerk/grumphp-typoscript-task.svg?style=flat-square)](https://scrutinizer-ci.com/g/pluswerk/grumphp-typoscript-task)

# grumphp-typoscript-task

GrumPHP task to lint TYPO3 CMS TypoScript files.

This package adds a TYPO3 CMS TypoScript file linter task to grumphp based
on [martin-helmich/typo3-typoscript-lint](https://github.com/martin-helmich/typo3-typoscript-lint) of Martin Helmich.

## Quick guide

### grumphp.yml

Basic inclusion in grumphp.yml:

```yaml
parameters:
    tasks:
        typoscriptlint: ~ 
    extensions:
        - Pluswerk\TypoScriptLinter\ExtensionLoader
```

### Composer

``composer require --dev pluswerk/grumphp-typoscript-task``

## Configuration

### typoscript linter

The linter can be configured in the grumphp.yml file in the same way as without grumphp see [TypoScript Linter configuration](https://github.com/martin-helmich/typo3-typoscript-lint#configuration)

#### Example:

```yaml
parameters:
  tasks:
    typoscriptlint:
      sniffs:
        - class: Indentation
          parameters:
            useSpaces: true
            indentPerLevel: 2
            indentConditions: true
        - class: DeadCode
  extensions:
    - Pluswerk\TypoScriptLinter\ExtensionLoader
```

### triggered_by

The file extensions, which trigger the linter.

```yaml
parameters:
  tasks:
    typoscriptlint:
      triggered_by:
        - 'typoscript'
```

### ignore_patterns

Ignore file with grumphp ignore patterns.

```yaml
parameters:
  tasks:
    typoscriptlint:
      ignore_patterns:
        - 'pattern'
```
