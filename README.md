# grumphp-typoscript-task

## Quick guide

GrumPHP task to lint TYPO3 CMS TypoScript files.

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

The linter can be configured in the grumphp.yml file in the same way as without grumphp see [TypoScript Linter configuration](https://github.com/martin-helmich/typo3-typoscript-lint#configuration)

### Example:

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
