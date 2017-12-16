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

namespace Zoe\Component\Security\Acl;

use Zoe\Component\Security\Acl\Resource\ImmutableResourceInterface;
use Zoe\Component\Security\User\AuthenticatedUserInterface;

/**
 * Convenient way to set permission permission into user.
 * MUST never interact whatsoever with an implementation of UserInterface.
 * MUST never be stored for obvious reasons
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface AclUserInterface extends AuthenticatedUserInterface
{
    
    /**
     * Grant some permissions over a resource
     * 
     * @param ImmutableResourceInterface $resource
     *   Resource which permissions are granted
     * @param array $permissions
     *   Permissions to grant. Can be raw permissions setted into the resource or values setted into a resource entity
     */
    public function grant(ImmutableResourceInterface $resource, array $permissions): void;
    
    /**
     * Deny some permissions over a resource
     *
     * @param ImmutableResourceInterface $resource
     *   Resource which permissions are granted
     * @param array $permissions
     *   Permissions to deny. Can be raw permissions setted into the resource or values setted into a resource entity
     */
    public function deny(ImmutableResourceInterface $resource, array $permissions): void; 
    
}
