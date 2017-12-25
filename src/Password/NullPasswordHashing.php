<?php
//StrictType
declare(strict_types = 1);

/*
 * Zoe
 * Security component
 *
 * Author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */

namespace Zoe\Component\Security\Password;

/**
 * Do nothing.
 * Can be used for testing purpose
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NullPasswordHashing implements PasswordHashingInterface
{
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Password\PasswordHashingInterface::hash()
     */
    public function hash(string $password, ?string $salt = null): string
    {
        return $password;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Password\PasswordHashingInterface::check()
     */
    public function check(string $password, string $hashed, ?string $salt = null): bool
    {
        return $password === $hashed;
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Password\PasswordHashingInterface::rehash()
     */
    public function rehash(string $hashed, ?string $salt = null): bool
    {
        return false;
    }
    
}
