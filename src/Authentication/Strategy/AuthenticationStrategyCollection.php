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
     * If the strategies has been already ordered
     * 
     * @var bool
     */
    private $ordered = false;
    
    /**
     * Add a strategy to the collection.
     * For optimisation reason, you should add strategy that can return SHUNT_ON_SUCCESS or FAIL at higher priority
     * 
     * @param AuthenticationStrategyInterface $strategy
     *   Authentication strategy
     * @param int $priority
     *   Order which the strategy is executed
     */
    public function add(AuthenticationStrategyInterface $strategy, int $priority = 0): void
    {
        $this->strategies[$priority][] = $strategy;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface::process()
     */
    public function process(MutableUserInterface $loadedUser, UserInterface $user): int
    {
        $processed = 0;
        if(!$this->ordered) {
            \krsort($this->strategies);
            $this->ordered = true;
        }
        
        foreach ($this->strategies as $strategies) {
            foreach ($strategies as $strategy) {
                $process = $strategy->process($loadedUser, $user);
                if($process === AuthenticationStrategyInterface::SKIP)
                    continue;
                switch ($process) {
                    case AuthenticationStrategyInterface::SHUNT_ON_SUCCESS:
                        return AuthenticationStrategyInterface::SUCCESS;
                    case AuthenticationStrategyInterface::FAIL:
                        return $process;
                    case AuthenticationStrategyInterface::SUCCESS:
                        $processed++;
                        break;
                    default:
                        throw new \UnexpectedValueException(\sprintf("Invalid return value on '%s' strategy",
                        \get_class($strategy)));
                }
            }
        }
        
        return ($processed !== 0) ? AuthenticationStrategyInterface::SUCCESS : AuthenticationStrategyInterface::FAIL;     
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface::handle()
     */
    public function handle(MutableUserInterface $user): ?MutableUserInterface
    {
        if(!$this->ordered) {
            \krsort($this->strategies);
            $this->ordered = true;
        }
        $handled = 0;
        foreach ($this->strategies as $strategies) {
            foreach ($strategies as $strategy) {
                if(null !== $handledUser = $strategy->handle($user)) {
                    $user = $handledUser;
                    $handled++;
                }
            }
        }
        
        if($handled !== 0)
            return $user;
        
        return null;
    }

}