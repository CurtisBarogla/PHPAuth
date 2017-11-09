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

use Zoe\Component\Internal\Exception\RuntimeException;

/**
 * Responsable of encoding and comparing password
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface PasswordEncoderInterface
{
    
    /**
     * Encode a password and return it
     * 
     * @param string $password
     *   Password to encode
     * 
     * @return string
     *   Password encoded
     *   
     * @throws RuntimeException
     *   When an error happen during the encoding process
     */
    public function encode(string $password): string;
    
    /**
     * Compare two password strings
     * 
     * @param string $comparedPassword
     *   Password to compare
     * @param string $encodedPassword
     *  Encoded password from encode() method 
     *
     * @return bool
     *   True if the two given passwords are considered same. False otherwise
     */
    public function compare(string $comparedPassword, string $encodedPassword): bool;
    
}
