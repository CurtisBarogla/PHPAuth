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

namespace Zoe\Component\Security\User;

use Zoe\Component\Security\Exception\User\InvalidUserCredentialException;
use Zoe\Component\Security\Exception\User\InvalidUserRoleException;

/**
 * User passed to the authentication process
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface AuthenticationUserInterface extends UserInterface
{
    
    /**
     * Change the current username
     * 
     * @param string $name
     *   New username
     */
    public function changeName(string $name): void;
    
    /**
     * Get the user's password
     * 
     * @return string|null
     *   User password or null if no password has been setted
     */
    public function getPassword(): ?string;
    
    /**
     * Add a role
     * 
     * @param string $role
     *   Role
     */
    public function addRole(string $role): void;
    
    /**
     * Delete a role 
     * 
     * @param string $role
     *   Role
     *   
     * @throws InvalidUserRoleException
     *   If the user has not the given role setted
     */
    public function deleteRole(string $role): void;
    
    /**
     * Add a credential value
     * 
     * @param string $credential
     *   Credential name
     * @param mixed $value
     *   Credential value
     */
    public function addCredential(string $credential, $value): void;
    
    /**
     * Get all user's credentials
     * 
     * @return array
     *   All credentials
     */
    public function getCredentials(): array;
    
    /**
     * Get a specific user's credential
     * 
     * @param string $credential
     *   Credential name
     * 
     * @return mixed
     *   Credential value
     *   
     * @throws InvalidUserCredentialException
     *   When the requested credential is invalid
     */
    public function getCredential(string $credential);
    
    /**
     * Check if the user has a specific credential setted
     * 
     * @param string $credential
     *   Credential name
     * 
     * @return bool
     *   True if the user has the request credential. False otherwise
     */
    public function hasCredential(string $credential): bool;
    
    /**
     * Delete a credential
     * 
     * @param string $credential
     *   Credential name
     *   
     * @throws InvalidUserCredentialException
     *   If the user has not the given credential setted
     */
    public function deleteCredential(string $credential): void;
    
    /**
     * Delete all credentials setted for the user
     */
    public function deleteCredentials(): void;
    
}
