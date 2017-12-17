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

use Zoe\Component\Security\Common\JsonSerializable;

/**
 * This user is considered authenticated
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface AuthenticatedUserInterface extends UserInterface, JsonSerializable
{
    
    /**
     * Time which the user has been authenticated
     * 
     * @return \DateTimeInterface
     *   Datetime instance corresponding to the creation of the user
     */
    public function authenticatedAt(): \DateTimeInterface;
    
}
