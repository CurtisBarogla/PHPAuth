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
 
namespace Ness\Component\Authentication\User;

use Ness\Component\User\UserInterface;

/**
 * User considered authenticated
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface AuthenticatedUserInterface extends UserInterface
{
    
    /**
     * Check if the user is root
     * 
     * @return bool
     *   True if the user is root. False otherwise
     */
    public function isRoot(): bool;
    
    /**
     * Get moment when the user has been authenticated by an authentication component
     * 
     * @return \DateTimeInterface
     *   Datetime
     */
    public function authenticatedAt(): \DateTimeInterface;
    
}
