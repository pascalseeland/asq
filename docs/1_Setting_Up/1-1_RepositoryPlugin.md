# 1.1 Setting Up a Repository Plugin
If you want to use the Assessment Question Service within a Repository Objects Plugin, proceed as follows.
## Get the ASQ Library and the cqrs dependency by composer
Your have to load the two libraries asq und cqrs from github and make sure the classmap includes the directory "vendor/srag/asq/classes" .

For this just integrate the following lines in your composer.json of your plugin and update the dependencies.

```
"repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/studer-raimann/asq.git"
    },
    {
      "type": "vcs",
      "url": "https://github.com/studer-raimann/cqrs.git"
    }
  ],
  "require": {
   [...]
    "srag/asq": "dev-review",
    "srag/cqrs": "dev-review"
  },
  "autoload": {
    [...]
    "classmap": [
      "classes/",
      "vendor/srag/asq/classes"
    ]
  },
```

Example: [composer.json_example](../Examples/composer.json_example)

## Install Data Tables and Update Languages
Use the following setup statement in your sql/dbupdate.php
```
<#1>
[...]
<#2>
<?php
\srag\asq\Infrastructure\Setup\Setup::new()->run();
?>
```