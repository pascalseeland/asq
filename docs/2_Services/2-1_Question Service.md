<link href="https://unpkg.com/@primer/css/dist/primer.css" rel="stylesheet" />

2.1 Question Service
----
{:.no_toc}
----
## 2.1.1 Question Creation {:toc}
### 2.1.1.1 Question _Creation Link_
The easiest way to create a question is to delegate it to the Assessment Question service.

<div class="flash">
  Flash message goes here.
</div>





``Please Note``

<input type="checkbox"> Implement the _Create Question Link_ in your application. 



All requests to the authoring environment are routed through your application. Therefore you must guarantee the ILIAS Ctrl-Flow. Please note that you are responsible for checking the permissions for every authoring action!

**Add the ilCtrl Call _AsqQuestionAuthoringGUI_ to your GUI Class**
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
 Please reload the control structure - by plugins increase the version of the plugin and update the plugin via the _ILIAS Administration_.
 
**Add the _Creation Link_ as a toolbar button**
```
public function renderToolbar()
{
        global $DIC;

        $link = AsqGateway::get()->link()->getCreationLink(
            $this->plugin->txt('create_question')
        );
        $button = ilLinkButton::getInstance();
        $button->setUrl($link->getAction());
        $button->setCaption($link->getLabel(), false);
        $DIC->toolbar()->addButtonInstance($button);
}
```
**Catch any redirects to the `ilAsqQuestionAuthoringGUI` class**

 Catch any redirects to the `ilAsqQuestionAuthoringGUI` class within the `executeCommand()` or `performCommand()` methods, depending on whether you're working with a plugin or not and forward to ilAsqQuestionAuthoringGUI.

It is highly advised to verify the permissions of users trying to access link the ilAsqQuestionAuthoringGUI.

The _ilAsqQuestionAuthoringGUI_ needs by construction an _AuthoringContextContainer_ object to hold the metadata of your application. 

An implementation of the redirection may look like this:


```
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

## 2.1.2 Get _Questions Of Container_
### Get Questions
There are several ways to fetch questions. Currently, it's possible to fetch questions with either their UUID or with a container id.
#### Fetch with UUID
```
$question = $question_service->getQuestionByQuestionId($id)
```
#### Fetch with container ID
A container is the object where questions are created and is thus basically the owner of these questions.
This method is a basic way to get questions of an object and is discuraged from actual use, as Question consumers are expected to store the ids of their quesitons themselves.
```
$questions = $question_service->getQuestionsOfContainer($container_id)
```
$questions = AsqGateway::get()->question()->getQuestionsOfContainer($this->object->getId());