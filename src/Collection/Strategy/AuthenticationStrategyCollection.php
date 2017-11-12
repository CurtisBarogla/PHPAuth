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

namespace Zoe\Component\Security\Collection\Strategy;

use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface;
use Zoe\Component\Security\User\UserInterface;

/**
 * Collection of strategies processable
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticationStrategyCollection
{
    
    /**
     * AuthenticationStrategies registered
     * 
     * @var AuthenticationStrategyInterface[]
     */
    private $strategies;

    /**
     * Add an authentication strategy to the collection
     * 
     * @param AuthenticationStrategyInterface $strategy
     *   AuthenticationStrategyInterface instance
     */
    public function add(AuthenticationStrategyInterface $strategy): void
    {
        $this->strategies[] = $strategy;
    }
    
    /**
     * Process all registered strategies
     * 
     * @param UserInterface $loadedUser
     *   User loaded by a UserLoaderInterface
     * @param UserInterface $user
     *   User requested
     * 
     * @return bool
     *   True if all strategies are processed successfully. False otherwise
     */
    public function process(UserInterface $loadedUser, UserInterface $user): bool
    {
        foreach ($this->strategies as $strategy) {
            if(!$strategy->process($loadedUser, $user))
                return false;
        }
        
        return true;
    }
    
}
