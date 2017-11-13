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
     * Skip the strategy for whatever reason.
     * 
     * @var int
     */
    public const SKIP = -1;
    
    /**
     * User is not valid
     * 
     * @var int
     */
    public const FAIL = 0;
    
    /**
     * User is valid
     * 
     * @var int
     */
    public const SUCCESS = 1;
    
    /**
     * User is valid no matter what happen next
     * 
     * @var int
     */
    public const SHUNT_ON_SUCCESS = 2;
    
    /**
     * Process the strategy over a loaded user and the user given to the authentification process
     * 
     * @param UserInterface $loadedUser
     *   User from the loader
     * @param UserInterface $user
     *   User given to the authentification process
     * 
     * @return int
     *   One of the "enum const" defined into the interface
     */
    public function process(UserInterface $loadedUser, UserInterface $user): int;
    
}
