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

namespace ZoeTest\Component\Security\Fixtures\Authentication;

use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface;
use Zoe\Component\Security\User\Contracts\UserInterface;

/**
 * For testing purpose only
 * 
 * @see \ZoeTest\Component\Security\Authentication\AuthenticationTest
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticationStrategyFixture implements AuthenticationStrategyInterface
{
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface::process()
     */
    public function process(UserInterface $loadedUser, UserInterface $user): int
    {
        return self::SUCCESS;
    }

}
