<?php
//StrictType
declare(strict_types = 1);

/*
 * Ness
 * Authentication component
 *
 * Author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
 
namespace Ness\Component\Authentication\Strategy;

use Ness\Component\Authentication\User\AuthenticationUserInterface;

/**
 * Process a verification over an user given to an authentication component and an user given by an user loader component
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface AuthenticationStrategyInterface
{
    
    /**
     * Return by a strategy when loaded user and given user cannot be authenticated
     * 
     * @var int
     */
    public const ERROR = 0;
    
    /**
     * Returned by a strategy considering the user authenticated
     * 
     * @var int
     */
    public const SUCCESS = 1;
    
    /**
     * Process a verification over a loaded user and a given user
     * 
     * @param AuthenticationUserInterface $user
     *   User to compare to a loaded user
     * 
     * @return int
     *   One of the const defined into the interface
     */
    public function process(AuthenticationUserInterface $user): int;
    
    /**
     * Get currently loaded user given by a loader linked to the strategy.
     * 
     * @return AuthenticationUserInterface
     *   Linked user
     */
    public function getLoadedUser(): AuthenticationUserInterface;
    
    /**
     * Link a converted user given by a user loader
     * 
     * @param AuthenticationUserInterface $user
     *   User loaded and converted
     */
    public function setLoadedUser(AuthenticationUserInterface $user): void;
    
}
