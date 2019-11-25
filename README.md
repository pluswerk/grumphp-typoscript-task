# grumphp-typoscript-task

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
