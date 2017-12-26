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

use Zoe\Component\Security\User\AuthenticationUserInterface;

/**
 * Strategy processed over a Authentication user
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface AuthenticationStrategyInterface
{
    
    /**
     * If the strategy must be skipped for whatever reason
     * 
     * @var int
     */
    public const SKIP = 0;
    
    /**
     * If the strategy consider the user invalid
     * 
     * @var int
     */
    public const ERROR = -1;
    
    /**
     * If the user is considered valid
     * 
     * @var int
     */
    public const SUCCESS = 1;
    
    /**
     * Process a strategy over a user.
     * Strategy can return 3 values. <br />
     * SKIP - to skip the strategy if the user does not correspond a parameter or for whatever reason <br />
     * ERROR - if the strategy conclude the user invalid <br />
     * SUCCESS - if the user is considered valid 
     * 
     * @param AuthenticationUserInterface $user
     *   User to process
     * 
     * @return int
     *   One of the const defined into the interface
     */
    public function process(AuthenticationUserInterface $user): int;
    
}
