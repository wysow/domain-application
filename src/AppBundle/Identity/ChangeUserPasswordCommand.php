<?php

namespace AppBundle\Identity;

use RayRutjes\DomainFoundation\Serializer\Serializable;

final class ChangeUserPasswordCommand implements Serializable
{
    /**
     * @var string
     */
    private $userIdentifier;

    /**
     * @var string
     */
    private $newPassword;

    /**
     * @param $userIdentifier
     * @param $newPassword
     */
    public function __construct($userIdentifier, $newPassword)
    {
        $this->userIdentifier = $userIdentifier;
        $this->newPassword = $newPassword;
    }

    /**
     * @return string
     */
    public function userIdentifier()
    {
        return $this->userIdentifier;
    }

    /**
     * @return string
     */
    public function newPassword()
    {
        return $this->newPassword;
    }
}
