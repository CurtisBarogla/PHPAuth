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
use Zoe\Component\Security\User\MutableAclUser;
use Zoe\Component\Security\Acl\AclInterface;

/**
 * Set all permissions for a user over resources loaded via an acl
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AclAttributionStrategy implements AuthenticationStrategyInterface
{
    
    /**
     * Acl initialized
     * 
     * @var AclInterface
     */
    private $acl;
    
    /**
     * Initiliaze strategy
     * 
     * @param AclInterface $acl
     *   Acl instance
     */
    public function __construct(AclInterface $acl)
    {
        $this->acl = $acl;
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
        $user = new MutableAclUser($user->getName(), $user->isRoot(), $user->getRoles(), $user->getAttributes());
        
        $this->acl->executeProcessables($user);
        
        return $user;
    }

}
