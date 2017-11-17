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

use Zoe\Component\Security\User\Contracts\CredentialUserInterface;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\User\Contracts\StorableUserInterface;

/**
 * Create and convert user implementation
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserFactory
{
    
    /**
     * Initialize a CredentialUser from a MutableUser
     * 
     * @param MutableUserInterface $user
     *   Mutable user instance
     * @param string|null $password
     *   User password
     * @param array|null $credentials
     *   Credentials to set
     * @return CredentialUserInterface
     *   Credential user with informations from MutableUser setted
     */
    public static function createCredentialUser(
        MutableUserInterface $user, 
        ?string $password = null,
        ?array $credentials = null): CredentialUserInterface
    {
        return new CredentialUser($user->getName(), $password, $user->isRoot(), $user->getRoles(), $credentials, $user->getAttributes());
    }
    
    /**
     * Initialize a StorableUser from a MutableUser
     * 
     * @param MutableUserInterface $user
     *   Mutable user instance
     * 
     * @return StorableUserInterface
     *   Storable user instance with informations from MutableUser setted
     */
    public static function createStorableUser(MutableUserInterface $user): StorableUserInterface
    {
        return new StorableUser($user->getName(), $user->isRoot(), $user->getRoles(), $user->getAttributes());
    }
    
    /**
     * Initialize a StorabeUser from a his json representation
     * 
     * @param string $json
     *   Json user representation
     * 
     * @return StorableUserInterface
     *   Storable user from his json representation
     */
    public static function createStorableUserFromJson(string $json): StorableUserInterface
    {
        $json = \json_decode($json, true);
        
        return new StorableUser($json["name"], $json["root"], $json["roles"], $json["attributes"]);  
    }
    
}
