# ILIAS - ASQ - Assessment Question Service

The ILIAS ASQ is designed as a component that offers services around assessment questions. The way other components can interact with asq is as easy and flexible as possible. The asq provides a complete authoring and scoring environment for assessment questions.

![](asq_authoring_environment.png)

ASQ provides no higher level business logic. Those must be handled by the consumer. E.g. the business logic that a question can only be answered once or the business logic for handling a group of questions such as that a question can only be answered once. 
  
<br>
<br>


# Status
BETA

 
<br>
<br>


# Features

## Authoring of Question Types

* Cloze
* Error
* Essay
* FileUpload
* Formula
* Kprim
* Matching
* MultipleChoice
* Numeric
* Ordering
* TextSubset

## Scoring of Answers
* Score User Answer
* Get Max Score
* Get Best Answer
  
<br>
<br>


# Requirements
* PHP 7.2
* ILIAS 6 - ILIAS 7
* https://github.com/studer-raimann/cqrs
  
<br>
<br>


# Architecture
* Event Sourcing
* CQRS
  
<br>
<br>


# How to use?
* [1 Setting Up](docs/1_Setting_Up/README.md)
* [2 Create Questions](docs/2_Create_Questions/README.md)
* [3 Edit Questions](docs/3_Edit_Questions/README.md)
* [4 Get Questions](docs/4_Get_Questions/README.md)
* [5_Score_Answer](docs/5_Score_Answer/README.md)
 
<br>
<br>


# Authors
This is an OpenSource project by studer + raimann ag (https://studer-raimann.ch)
 
<br>
<br>


# License
This project is licensed under the GPL v3 License
 
<br>
<br>


# Credits

## Development and Software Architecture
* al@studer-raimann.ch
* bh@bjoernheyser.de
* ms@studer-raimann.ch
* tt@studer-raimann.ch

## Quality control
* dw@studer-raimann.ch

## Supervision
* ILIAS SIG E-Assessment, https://docu.ilias.de/goto_docu_grp_5174.html - first of all denis.strassner@uni-hohenheim.de
* ILIAS Technical Board, https://docu.ilias.de/goto_docu_grp_5089.html - first of all stephan.winiker@hslu.ch