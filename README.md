## 1 Setting Up
[1.1 Setting Up a Repository Plugin](docs/1_SettingUp/1-1_RepositoryPlugin.md)




## Authoring Environment
The question service offers a complete authoring environment with the following features:
* Create Question Link

### Implement Create Question Link
To create a question, you must implement the Create Question button in your application. All requests to the authoring environment are routed through your application. Therefore you must guarantee the ILIAS Ctrl-Flow. Please note that you are responsible for checking the permissions for every authoring action!

**Add the ilCtrl Call _AsqQuestionAuthoringGUI_ to your GUI class. Please reload the control structure - by plugins increase the version of the plugin and update the plugin via the ILIAS Administration.**
```
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

**Add a _Create Question Button_ as a toolbar button**
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
**Catch any redirects to the `ilAsqQuestionAuthoringGUI` class within the `executeCommand()` or `performCommand()` methods, depending on whether you're working with a plugin or not and forward to ilAsqQuestionAuthoringGUI.**

It is highly advised to verify the permissions of users trying to access link the ilAsqQuestionAuthoringGUI.



TODO TODO TODO

require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrDemoTest/vendor/srag/asq/classes/class.AsqQuestionCreationGUI.php";

TODO TODO TODO


An AuthoringContextContainer object is required to hold this metadata. The following parameters are required:
```
UiStandardLink $backLink,
int $refId,
int $objId,
string $objType,
int $actorId,
bool $writeAccess,
```

The context has to be passed on to an `ilAsqQuestionAuthoringGUI` object. The last step is to forward the `ilAsqQuestionAuthoringGUI` object. An implementation of this functionality may look like this:


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
            $DIC->user()->getId(),
            $DIC->access()->checkAccess('write', '', $this->object->getRefId()));

        $asq = new AsqQuestionAuthoringGUI($authoring_context_container);

        $DIC->ctrl()->forwardCommand($asq);
    }
```



```
protected function forwardToAsqAuthoring()
{
	$backLink = self::dic()->ui()->factory()->link()->standard(
	    self::dic()->language()->txt('back'), 
	    self::dic()->ctrl()->getLinkTarget($this, self::CURRENT_COMMAND)); 
	
	$authoring_context_container = new AuthoringContextContainer(
	    $backLink,
	    $this->object->getRefId(),
	    $this->object->getId(),
	    $this->object->getType(),
	    self::dic()->user()->getId(),
	    self::dic()->access()->checkAccess('write', '', $this->object->getRefId()));
	    
	$asq = new ilAsqQuestionAuthoringGUI($authoring_context_container);
	
	self::dic()->ctrl()->forwardCommand($asq);
}
```

