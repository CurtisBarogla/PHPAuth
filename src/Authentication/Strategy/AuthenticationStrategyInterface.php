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

use Zoe\Component\Security\User\UserInterface;

/**
 * Responsible to filter user before the storing process
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface AuthenticationStrategyInterface
{
    
    /**
     * Process the strategy over a loaded user and the user given to the authentification process
     * 
     * @param UserInterface $loadedUser
     *   User from the loader
     * @param UserInterface $user
     *   User given to the authentification process
     * 
     * @return bool
     *   True if the loaded user and the given user are valid
     */
    public function process(UserInterface $loadedUser, UserInterface $user): bool;
    
}
