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
 * Handle (user) password
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface PasswordHashingInterface
{
    
    /**
     * Hash a password
     * 
     * @param string $password
     *   Password to hash
     * @param string|null $salt
     *   Salt to apply
     * 
     * @return string
     *   Hashed password
     */
    public function hash(string $password, ?string $salt = null): string;
    
    /**
     * Check if a clear password correspong to its hashed version
     * 
     * @param string $password
     *   Raw password to check
     * @param string $hashed
     *   Hashed password
     * @param string|null $salt
     *   Salt applied
     * 
     * @return bool
     *   True if raw password correspond to its hashed version. False otherwise
     */
    public function check(string $password, string $hashed, ?string $salt = null): bool;
    
    /**
     * Check if hashed password is still valid considering extra parameters
     * 
     * @param string $hashed
     *   Hashed password
     * @param string|null $salt
     *   Salt setted
     * 
     * @return bool
     *   True if the hashed password needs to be rehashed to correspond new parameters. False otherwise
     */
    public function rehash(string $hashed, ?string $salt = null): bool;
    
}
