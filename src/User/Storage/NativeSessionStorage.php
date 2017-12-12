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

namespace Zoe\Component\Security\User\Storage;

use Zoe\Component\Security\User\AuthenticatedUserInterface;
use Zoe\Component\Security\Exception\User\UserNotFoundException;
use Zoe\Component\Security\User\AuthenticatedUser;

/**
 * User native session mechanisms from php
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NativeSessionStorage implements UserStorageInterface
{
    
    /**
     * Session array (mostly $_SESSION)
     * 
     * @var array
     */
    private $session;
    
    /**
     * If the session id must be refresh when an user if added or refreshed
     * 
     * @var bool
     */
    private $refresh;
    
    /**
     * Use json user's representation
     * 
     * @var bool
     */
    private $json;
   
    /**
     * Initialize store.
     * If the store is setted to store user as json, be careful about the attributes setted.
     * If an attribute cannot be jsonified (does not implement JsonSerializable), it will be lost during the storing process
     * 
     * @param bool $refresh
     *   Set to true to refresh the session id when an user is added or refreshed
     * @param bool $json
     *   Set to true to store json representation of the user into the session instead of the instance. 
     */
    public function __construct(bool $refresh, bool $json = false)
    {
        if(session_status() !== PHP_SESSION_ACTIVE)
            throw new \LogicException("Session MUST be active before able to store user into NativeSessionStorage");
        
        $this->session = &$_SESSION;
        $this->refresh = $refresh;
        $this->json = $json;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Storage\UserStorageInterface::addUser()
     */
    public function addUser(string $identifier, AuthenticatedUserInterface $user): void
    {
        if($this->refresh)
            session_regenerate_id();
        
        if($this->json)
            $user = \json_encode($user);
        
        $this->session[$identifier] = $user;
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Storage\UserStorageInterface::getUser()
     */
    public function getUser(string $identifier): AuthenticatedUserInterface
    {
        $this->checkUser($identifier);
        
        $user = $this->session[$identifier];
        
        if($this->json)
            $user = AuthenticatedUser::restoreFromJson($user);

        // check user integrity
        if(!$user instanceof AuthenticatedUserInterface) {
            unset($this->session[$user]);
            throw new UserNotFoundException(null, UserNotFoundException::GENERAL_ERROR_CODE);
        }
        
        return $user;
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Storage\UserStorageInterface::deleteUser()
     */
    public function deleteUser(string $identifier): void
    {
        $this->checkUser($identifier);
        
        unset($this->session[$identifier]);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Storage\UserStorageInterface::refreshUser()
     */
    public function refreshUser(string $identifier, AuthenticatedUserInterface $user): void
    {
        $this->checkUser($identifier, $user);
        
        unset($this->session[$identifier]);
        
        $this->addUser($identifier, $user);
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Storage\UserStorageInterface::isStored()
     */
    public function isStored(string $identifier): bool
    {
        return isset($this->session[$identifier]);
    }
    
    /**
     * Check if a user if already store
     * 
     * @param string $identifer
     *   Store identifier
     * @param AuthenticatedUserInterface|null $user
     *   User
     *   
     * @throws UserNotFoundException
     *   When the user is not stored
     */
    private function checkUser(string $identifier, ?AuthenticatedUserInterface $user = null): void
    {
        if(!isset($this->session[$identifier]))
            throw new UserNotFoundException($user, UserNotFoundException::STORAGE_ERROR_CODE);
    }

}
