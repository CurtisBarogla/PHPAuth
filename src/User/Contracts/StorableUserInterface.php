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

namespace Zoe\Component\Security\User\Contracts;

/**
 * User immutable
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface StorableUserInterface extends UserInterface
{
    
    /**
     * Just return true
     * 
     * @return bool
     *   User is storable
     */
    public function storable(): bool;
    
}
