# Get Questions

There are two ways to fetch questions:
* Get all questions of a container by ILIAS Object ID
* Get a single question by UUID 

<br>
<br>


## Table of Contents

- [Get all questions of a container by ILIAS Object ID](#get-all-questions of-a-container-by-ilias-object-id)
- [Get a single question by UUID](#get-a-single-question-by-uuid)  
    
<br>
<br>

## Get all questions of a container by ILIAS Object ID

### Note

A container is the object where questions are created and is thus basically the owner of these questions.
This method is a basic way to get questions of an object.

### Usage

```
$questions = $question_service->getQuestionsOfContainer($il_obj_id);
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