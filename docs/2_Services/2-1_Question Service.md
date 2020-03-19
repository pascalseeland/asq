# Question Service

## Table of Contents

- [Question Creation](question-creation)
    - [Get the Question Creation Link](get-the-quesetion-creation-link)
    - [Create the Question without using the authoring environment](create-the-question-without-using-the-authoring-environment)  

## Question Creation
There are two ways to create questions:
1.  Recommended: Get the Question Creation Link and delegate the creation to the authoring environment _Assessment Question Service_.
2. Normally not Recommended: Create the Question without using the authoring environment.

### Get the Question Creation Link

#### Note


If you use this option for creating a question all requests to the authoring environment are routed through your application. Therefore you must guarantee the ILIAS Ctrl-Flow. Please note that you are responsible for checking the permissions for every authoring action!

#### Usage

<input type="checkbox">1. Add the ilCtrl Call _AsqQuestionAuthoringGUI_ to your GUI Class

```php
<?php

use srag\asq\AsqGateway;

/**
 * Class AsqDemoGUI
 *
 * @ilCtrl_Calls AsqDemoGUI: AsqQuestionAuthoringGUI
 */
class AsqDemoGUI {
[...]
```
>Please reload the control structure - by plugins increase the version of the plugin and update the plugin via the _ILIAS Administration_.

<input type="checkbox">2. Add the _Creation Link_

E.g add the link as a toolbar button
```php
public function renderToolbar()
{
        global $DIC;

        $link = AsqGateway::get()->link()->getCreationLink();
        $button = ilLinkButton::getInstance();
        $button->setUrl($link->getAction());
        $button->setCaption($link->getLabel(), false);
        $DIC->toolbar()->addButtonInstance($button);
}
```

<input type="checkbox">3. Catch any redirects to the `ilAsqQuestionAuthoringGUI` class

Catch any redirects to the `ilAsqQuestionAuthoringGUI` class within the `executeCommand()` and forward to ilAsqQuestionAuthoringGUI.

The _ilAsqQuestionAuthoringGUI_ needs by construction an _AuthoringContextContainer_ object to hold the metadata of your application. 

An implementation of the redirection may look like this:


```php
[...]
class AsqDemoGUI
{
[...]


/**
 * @return void
 */
public function executeCommand()
{
    global $DIC;

    $next_class = $DIC->ctrl()->getNextClass($this);

    switch (strtolower($next_class)) {
        case strtolower(AsqQuestionAuthoringGUI::class):
            if($DIC->access()->checkAccess('write', '', $this->object->getRefId())) {
                $this->forwardCommandToAuthoringGui();
            }
            // Handle permission mismatch;
            [...]
            break;

[...]

private function forwardCommandToAuthoringGui()
{
        global $DIC;

        $backLink = $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('back'),
            $DIC->ctrl()->getLinkTarget($this, self::CMD_EDIT_QUESTIONS));

        $authoring_context_container = new AuthoringContextContainer(
            $backLink,
            $this->object->getRefId(),
            $this->object->getId(),
            $this->object->getType(),
            $DIC->user()->getId()
        );

        $asq = new AsqQuestionAuthoringGUI($authoring_context_container);

        $DIC->ctrl()->forwardCommand($asq);
}
```

### Create the Question without using the authoring environment

#### Note

Alternatively and normally not needed, questions can be created directly without using the authoring environment.

> This API Call will be changed in future. The Question Type Parameter will be a _Fqdn class name_

#### Usage

```php
AsqGateway::get()->question()->createQuestion(int $type, int $container_id, string $content_editing_mode = ContentEditingMode::RTE_TEXTAREA)
```

## Get Questions

There are two ways to fetch questions:
* Get all questions of a container by ILIAS Object ID
* Get a single question by UUID 

#### Get all questions of a container by ILIAS Object ID

#### Note

A container is the object where questions are created and is thus basically the owner of these questions.
This method is a basic way to get questions of an object.

#### Usage

```
$questions = $question_service->getQuestionsOfContainer($il_obj_id);
```

#### Get a single question by UUID

#### Note

The ASQ identifies question by a Version 4 UUID. You may get any question of the installation regardless the current application has created the question or not using the UUID.

#### Usage

```
$question_dto = AsqGateway::get()->question()->getQuestionByQuestionId('7464973d-6cf3-4142-949a-3d7fd4d48169');
```