<?php
//StrictType
declare(strict_types = 1);

/*
 * Zoe
 * Authentication component
 *
 * Author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */

namespace Zoe\Component\Authentication\Strategy;

use Zoe\Component\User\Loader\LoadedUserAwareInterface;
use Zoe\Component\User\AuthenticationUserInterface;

/**
 * Process a verification over a user
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface AuthenticationStrategyInterface extends LoadedUserAwareInterface
{
    
    /**
     * When strategy must be skipped for whatever reason
     * 
     * @var int
     */
    public const SKIP = -1;
    
    /**
     * When strategy is failing to authenticate a user
     * 
     * @var int
     */
    public const ERROR = 0;
    
    /**
     * When strategy is considering the user valid
     * 
     * @var int
     */
    public const SUCCESS = 1;
    
    /**
     * Verify informations about a user.
     * At this state, user provided by a UserLoader is accessible via the interface
     * 
     * @param AuthenticationUserInterface $user
     *   User to verify
     * 
     * @return int
     *   One of the const defined into the interface
     */
    public function process(AuthenticationUserInterface $user): int;
    
}
