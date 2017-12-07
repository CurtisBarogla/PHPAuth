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

use Zoe\Component\Security\User\Contracts\UserInterface;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;

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
     * @param MutableUserInterface $loadedUser
     *   User from the loader
     * @param UserInterface $user
     *   User given to the authentification process
     * 
     * @return int
     *   One of the "enum const" defined into the interface
     */
    public function process(MutableUserInterface $loadedUser, UserInterface $user): int;
    
    /**
     * Handle the user after the authentication process.
     * Be careful when generating a new instance of user to not loose informations from a previous strategy process
     * 
     * @param MutableUserInterface $user
     *   This user is considered authenticated
     * 
     * @return MutableUserInterface|null
     *   Can return a (new) version of a mutable user or null to skip this process
     */
    public function handle(MutableUserInterface $user): ?MutableUserInterface;
    
}
