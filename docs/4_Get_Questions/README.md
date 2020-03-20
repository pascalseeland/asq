# Get Questions

There are two ways to fetch questions:
* Get all questions of a container by ILIAS Object ID
* Get a single question by UUID 

<br>
<br>


## Table of Contents

- [Get all questions of a container by ILIAS Object ID](#get-all-questions of-a-container-by-ilias-object-id)
- [Get a single question by UUID](#get-a-single-question-by-uuid)  
- [Get question component for test runs](#get-question-component-for-test-runs)  
    
<br>
<br>


## Get all questions of a container by ILIAS Object ID

### Note

A container is the object where questions are created and is thus basically the owner of these questions.
This method is a basic way to get questions.

### Usage

```
$question_dtos = AsqGateway::get()->question()->getQuestionsOfContainer($this->object->getId());
```
    
<br>
<br>


## Get a single question by UUID

### Note

The ASQ identifies question by a Version 4 UUID. You may get any question of the installation regardless the current application has created the question or not using the UUID.

### Usage

```
$question_dto = AsqGateway::get()->question()->getQuestionByQuestionId('7464973d-6cf3-4142-949a-3d7fd4d48169');
```
    
<br>
<br>


## Get question component for test runs

### Note

The ASQ provides a question component without any form container. You have to enclose the component with your own form container.

The component can be provided with an answer through the method setAnswer().

The entered answer of the user can be extracted from the component through the method readAnswer();

### Usage

```php
$question_component = AsqGateway::get()->ui()->getQuestionComponent($question_dto);
        
$save_button = ilSubmitButton::getInstance();
$save_button->setCaption($DIC->language()->txt('submit_answer'), false);
$save_button->setCommand(self::CMD_RUN_TEST);

$DIC->ui()->mainTemplate()->setContent(
    '<form method="post" action="' . 
                $DIC->ctrl()->getFormAction(
                    $this, self::CMD_RUN_TEST
                ) . '">' .
                $question_component->renderHtml(). '<br />' .
                $save_button->render() .
    '</form>'
);
```





