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

namespace Zoe\Component\Security\User\Contracts;

use Zoe\Component\Security\Exception\InvalidUserCredentialException;

/**
 * User holding credentials informations.
 * This informations SHOULD NOT be persisted after the user is logged in.
 * User is still mutable at this point
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface CredentialUserInterface extends MutableUserInterface
{
 
    /**
     * Get the user password
     * 
     * @return string|null
     *   User password
     */
    public function getPassword(): ?string;
    
    /**
     * Get all credentials setted to the user
     * 
     * @return array|null
     *   All user credentials
     */
    public function getCredentials(): ?array;
    
    /**
     * Get a specific user credential
     * 
     * @param string $credential
     *   Credential name
     * 
     * @return string
     *   Credential value
     *   
     * @throws InvalidUserCredentialException
     *   When the credential is not setted
     */
    public function getCredential(string $credential): string;
    
    /**
     * Check if the user has a specific credential
     * 
     * @param string $credential
     *   Credential name
     * 
     * @return bool
     *   True if the user has the specific credential. False otherwise
     */
    public function hasCredential(string $credential): bool;
    
    /**
     * Add a credential to the user
     * 
     * @param string $credential
     *   Credential name
     * @param string $value
     *   Credential value
     * 
     * @return self
     *   self
     */
    public function addCredential(string $credential, string $value): CredentialUserInterface;
    
}