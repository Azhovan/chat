<?php

namespace Chat\Entities;
/*
 * User Value Object
 * as a value object this class does not have any specific behaviour
 * but can represent properties of an entity
 */

class UserObject
{
    /**
     * The name of the user
     *
     * @var string
     */
    private $name;

    /**
     * The uuid of the user
     *
     * @var string
     */
    private $uuid;

    /**
     * UserObject constructor.
     * @param string $name
     * @param string $uuid
     */
    public function __construct(string $name, string $uuid)
    {
        $this->name = $name;
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getUuid(): string
    {
        return $this->uuid ?? random_bytes(16);
    }
}