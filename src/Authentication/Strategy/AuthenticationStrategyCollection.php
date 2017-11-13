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
 * Strategy processing over a collection of AuthenticationStrategyInterface implementation
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticationStrategyCollection implements AuthenticationStrategyInterface
{

    /**
     * Strategies to process
     * 
     * @var AuthenticationStrategyInterface[]
     */
    private $strategies = [];
    
    /**
     * Add a strategy to the collection.
     * For optimisation reason, you should add strategy that can return SHUNT_ON_SUCCESS at first position
     * 
     * @param AuthenticationStrategyInterface $strategy
     *   Authentication strategy
     */
    public function add(AuthenticationStrategyInterface $strategy): void
    {
        $this->strategies[] = $strategy;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface::process()
     */
    public function process(UserInterface $loadedUser, UserInterface $user): int
    {
        $error = 0;
        $processed = 0;
        foreach ($this->strategies as $strategy) {
            $process = $strategy->process($loadedUser, $user);
            if($process === AuthenticationStrategyInterface::SKIP)
                continue;
            else if($process === AuthenticationStrategyInterface::SHUNT_ON_SUCCESS)
                return AuthenticationStrategyInterface::SUCCESS;
            elseif ($process === AuthenticationStrategyInterface::FAIL) {
                $processed++;
                $error++;
            } else {
                throw new \UnexpectedValueException(\sprintf("Invalid return value on '%s' strategy",
                    \get_class($strategy)));
            }
        }
        
        return ($processed !== 0 && $error === 0) ? AuthenticationStrategyInterface::SUCCESS : AuthenticationStrategyInterface::FAIL;
    }

}