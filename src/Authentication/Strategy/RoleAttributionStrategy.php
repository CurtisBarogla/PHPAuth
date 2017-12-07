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
     * Initialiaze strategy
     * 
     * @param RoleCollection $collection
     *   RoleCollection
     */
    public function __construct(RoleCollection $collection)
    {
        $this->collection = $collection;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface::process()
     */    
    public function process(MutableUserInterface $loadedUser, UserInterface $user): int
    {        
        return self::SKIP;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface::handle()
     */
    public function handle(MutableUserInterface $user): ?MutableUserInterface
    {
        $roles = [];
        foreach ($user->getRoles() as $role) {
            $roles = \array_merge($this->collection->get($role), $roles);
        }
        $roles = \array_unique($roles);
        
        foreach ($roles as $role) {
            $user->addRole($role);
        }
        
        return $user;
    }

}
