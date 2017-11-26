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

namespace Zoe\Component\Security\Authentication\Strategy;

use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\User\Contracts\UserInterface;
use Zoe\Component\Security\Role\RoleCollection;

/**
 * Attribute roles to users based on a RoleCollection.
 * Attribute is only done based on the loaded one
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class RoleAttributionStrategy implements AuthenticationStrategyInterface
{
    
    /**
     * Role collection
     * 
     * @var RoleCollection
     */
    private $collection;
    
    /**
     * If roles are setted into both user (loaded one and given one)
     * 
     * @var bool
     */
    private $both;
    
    /**
     * Initialiaze strategy
     * 
     * @param RoleCollection $collection
     *   RoleCollection
     * @param bool $both
     *   Set to true to alter role of both users (false by default)
     */
    public function __construct(RoleCollection $collection, bool $both = false)
    {
        $this->collection = $collection;
        $this->both = $both;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface::process()
     */    
    public function process(MutableUserInterface $loadedUser, UserInterface $user): int
    {
        $roles = [];
        foreach ($loadedUser->getRoles() as $role) {
            $roles = \array_merge($this->collection->get($role), $roles);
        }
        $roles = \array_unique($roles);
        
        foreach ($roles as $role) {
            $loadedUser->addRole($role);
            if($this->both && $user instanceof MutableUserInterface)
                $user->addRole($role);
        }
        
        return self::SKIP;
    }

}
