<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\EventStore;

use ILIAS\UI\NotImplementedException;
use srag\CQRS\Aggregate\DomainObjectId;
use srag\CQRS\Event\AbstractDomainEvent;
use srag\CQRS\Event\DomainEvents;
use srag\CQRS\Event\EventID;
use srag\CQRS\Event\EventStore;
use srag\asq\Application\Exception\AsqException;
use ilDateTime;

/**
 * Class QuestionEventStore
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionEventStore extends EventStore {
    protected function getEventArClass(): string
    {
        return QuestionEventStoreAr::class;
    }
}