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

use Zoe\Component\Security\User\Contracts\StorableUserInterface;

/**
 * User destined to be stored
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
final class StorableUser extends User implements StorableUserInterface
{
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\StorableUserInterface::storable()
     */
    public function storable(): bool
    {
        return true;
    }
    
}
