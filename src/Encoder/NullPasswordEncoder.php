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

namespace Zoe\Component\Security\Encoder;

/**
 * Basically, do nothing on password.
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NullPasswordEncoder implements PasswordEncoderInterface
{
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Encoder\PasswordEncoderInterface::encode()
     */
    public function encode(string $password): string
    {
        return $password;
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Encoder\PasswordEncoderInterface::compare()
     */
    public function compare(string $comparedPassword, string $encodedPassword): bool
    {
        return $comparedPassword === $encodedPassword;
    }

}