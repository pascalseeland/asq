<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\Projection;

interface ProjectionAr
{
    /**
     * @return int
     */
    public function getId() : int;

    /**
     * @return mixed
     */
    public function getCreated();

    /**
     * @return string
     */
    public function getQuestionId() : string;

    /**
     * @return int
     */
    public function getquestionIntId(): int;

    /**
     * @return string
     */
    public function getRevisionId() : string;

    /**
     * @return int
     */
    public function getContainerObjId() : int;

    /**
     *
     */
    public function create();
}