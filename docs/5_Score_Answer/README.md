# Score Answer

ASQ provides the following informations and possibilites for scoring answers.
* Get the score of a given Answer
* Get the max score of a question
* Get the best possible answer

## Table of Contents

- [Get score](#get-score)
- [Get max score](#get-max-score)  
- [Get best answer](#get-best-answer)  
    
<br>
<br>


## Get score

### Note
Get the reached score for the provided answer and question.

### Usage

```php
$score = AsqGateway::get()->answer()->getScore($question, $answer)
```
    
<br>
<br>


## Get max score

### Note

Get the maximal possible score of a question.

### Usage

```php
$max_score =  AsqGateway::get()->answer()->getMaxScore($question)
```
    
<br>
<br>


## Get best answer

### Note

Gets the best answer that is possible on that question.
caveat. some question types do not have a best possible answer (FileUploadQuestions ...)

### Usage

```php
$best_answer =  AsqGateway::get()->answer()->getBestAnswer($question)
```
<br>
<br>