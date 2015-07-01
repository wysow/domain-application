<?php

namespace AppBundle\Infrastructure\Identity;

use AppBundle\Domain\Identity\HashedPassword;
use AppBundle\Domain\Identity\Password;
use AppBundle\Domain\Identity\PasswordHashingService;

final class BcryptPasswordHashingService implements PasswordHashingService
{
    /**
     * @param Password $password
     *
     * @return HashedPassword
     */
    public function hashPassword(Password $password)
    {
        $plainPassword = $password->toString();
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

        return new HashedPassword($hashedPassword);
    }

    /**
     * @param Password       $plain
     * @param HashedPassword $hashed
     *
     * @return bool
     */
    public function check(Password $plain, HashedPassword $hashed)
    {
        return password_verify($plain->toString(), $hashed->toString());
    }
}
