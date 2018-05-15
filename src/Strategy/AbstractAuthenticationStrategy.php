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
 * Common to all AuthenticationStrategy implementations
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
abstract class AbstractAuthenticationStrategy implements AuthenticationStrategyInterface
{
    
    /**
     * User setted
     * 
     * @var AuthenticationUserInterface
     */
    private $user;
    
    /**
     * {@inheritDoc}
     * @see \Ness\Component\Authentication\Strategy\AuthenticationStrategyInterface::getLoadedUser()
     */
    public function getLoadedUser(): AuthenticationUserInterface
    {
        return $this->user; 
    }
    
    /**
     * {@inheritDoc}
     * @see \Ness\Component\Authentication\Strategy\AuthenticationStrategyInterface::setLoadedUser()
     */
    public function setLoadedUser(AuthenticationUserInterface $user): void
    {
        $this->user = $user;
    }
    
}
