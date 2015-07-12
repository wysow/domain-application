<?php

namespace AppBundle\Domain\Identity;

final class Username
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $username
     */
    public function __construct($username)
    {
        if (!is_string($username) || strlen($username) < 3) {
            throw new \InvalidArgumentException('Username should be a string containing at least 3 characters.');
        }
        $this->value = $username;
    }

    /**
     * @param ValueObject $other
     *
     * @return bool
     */
    public function sameValueAs(Username $other)
    {
        if (!$other instanceof self) {
            return false;
        }

        return $this->toString() === $other->toString();
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->value;
    }
}
