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

use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Exception\Acl\ResourceNotFoundException;
use Zoe\Component\Security\User\AuthenticatedUserInterface;

interface AclInterface
{
    
    /**
     * Get a resource
     * 
     * @param string $resource
     *   Resource name
     * 
     * @return ResourceInterface
     *   Resource instance
     * 
     * @throws ResourceNotFoundException
     *   If the resource is not found
     */
    public function getResource(string $resource): ResourceInterface;
    
    /**
     * Check if a user has the required permissions over a resource to perform action
     * 
     * @param AuthenticatedUserInterface $user
     *   User handled by the acl
     * @param string $resource
     *   Resource name
     * @param array $permissions
     *   Permission to check
     * @param callable|null $process
     *   Process executed before every checking. Take as parameters the processed resource and user
     * 
     * @return bool
     *   True if the user is able to perform the action. False otherwise
     */
    public function isAllowed(AuthenticatedUserInterface $user, string $resource, array $permissions, ?callable $process = null): bool;
    
}
