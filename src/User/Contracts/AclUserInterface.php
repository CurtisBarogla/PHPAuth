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

use Zoe\Component\Security\Acl\Mask\Mask;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Exception\ResourceNotFoundException;

/**
 * User interacting with an acl
 * Acl user is mutable for only acl part
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface AclUserInterface extends UserInterface
{
    
    /**
     * Identifier for registering acl permissions into attributes user
     * 
     * @var string
     */
    public const ACL_ATTRIBUTES_IDENTIFIER = "ACL_PERMISSIONS";
    
    /**
     * Grant a user some permissions for a resource
     * 
     * @param ResourceInterface $resource
     *   Resource which permissions are applied
     * @param array $permissions
     *   Permissions to apply
     */
    public function grant(ResourceInterface $resource, array $permissions): void;
    
    /**
     * Deny a user some permission for a resource
     * 
     * @param ResourceInterface $resource
     *   Resource which permissions are denied
     * @param array $permissions
     *   Permissions to deny
     */
    public function deny(ResourceInterface $resource, array $permissions): void;
    
    /**
     * Get permission for a resource
     * 
     * @param string $resource
     *   Resource which to get permissions
     * 
     * @return Mask
     *   User permission mask for the given resource
     *   
     * @throws ResourceNotFoundException
     *   When the user has no permission setted for the given resource
     */
    public function getPermission(string $resource): Mask;
    
}
