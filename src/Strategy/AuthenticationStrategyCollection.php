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

use Zoe\Component\User\AuthenticationUserInterface;
use Zoe\Component\User\Loader\LoadedUserAwareTrait;

/**
 * Try to authenticate a user over multiple authentication strategies.
 * Will return success a soon as a strategy return success. Will continue when a strategy fails or is skipped.
 * Will return error if no strategy was able to validate a user
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticationStrategyCollection implements AuthenticationStrategyInterface
{
    
    use LoadedUserAwareTrait;
    
    /**
     * Strategies registered
     * 
     * @var AuthenticationStrategyInterface[]
     */
    private $strategies;
    
    /**
     * Initialize strategy
     * 
     * @param AuthenticationStrategyInterface $strategy
     *   Default strategy
     */
    public function __construct(AuthenticationStrategyInterface $strategy)
    {
        $this->strategies[] = $strategy;
    }
    
    /**
     * Add a strategy able to authenticate a user
     * 
     * @param AuthenticationStrategyInterface $strategy
     *   Strategy to process
     */
    public function add(AuthenticationStrategyInterface $strategy): void
    {
        $this->strategies[] = $strategy;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Authentication\Strategy\AuthenticationStrategyInterface::process()
     */
    public function process(AuthenticationUserInterface $user): int
    {
        foreach ($this->strategies as $strategy) {
            $result = $strategy->process($user);
            if($result === AuthenticationStrategyInterface::SKIP || $result === AuthenticationStrategyInterface::ERROR)
                continue;
            
            return AuthenticationStrategyInterface::SUCCESS;
        }
        
        return AuthenticationStrategyInterface::ERROR;
    }

}
