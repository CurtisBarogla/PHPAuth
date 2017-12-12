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

namespace Zoe\Component\Security\User;

/**
 * This user is considered authenticated
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface AuthenticatedUserInterface extends UserInterface
{
    
    /**
     * Time which the user has been authenticated
     * 
     * @return \DateTime
     *   Datetime instance corresponding to the creation of the user
     */
    public function authenticatedAt(): \DateTime;
    
}
