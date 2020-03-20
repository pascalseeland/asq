# Setting Up

If you want to use the Assessment Question Service within a Repository Objects Plugin, proceed as follows.

<br>
<br>


## Table of Contents
- [Get the ASQ Library and the CQRS dependency by composer](#get-the-asq-library-and-the-cqrs-dependency-by-composer)
- [Install Data Tables and Update Languages](#install-data-tables-and-update-languages)  
    
<br>
<br>


## Get the ASQ Library and the CQRS dependency by composer
Your have to load the two libraries asq und cqrs from github and make sure the classmap includes the directory "vendor/srag/asq/classes" .

For this just integrate the following lines in your composer.json of your plugin and update the dependencies.

```json
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

Example: [composer.json_example](composer.json_example)
    
<br>
<br>


## Install Data Tables and Update Languages
Use the following setup statement in your sql/dbupdate.php
```php
<#1>
[...]
<#2>
<?php
\srag\asq\Infrastructure\Setup\Setup::new()->run();
?>
```