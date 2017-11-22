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

namespace Zoe\Component\Security\Acl\Resource;

use Zoe\Component\Security\Acl\Mask\Mask;
use Zoe\Component\Security\Acl\Mask\MaskCollection;
use Zoe\Component\Security\Exception\InvalidResourcePermissionException;

/**
 * Acl resource
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface ResourceInterface
{
    
    /**
     * By default, all permissions are denied and must be whitelisted
     * 
     * @var int
     */
    public const WHITELIST_BEHAVIOUR = 0;
    
    /**
     * By default, all permissions are granted and must be blacklisted
     * 
     * @var int
     */
    public const BLACKLIST_BEHAVIOUR = 1;
    
    /**
     * Permissions identifier
     * 
     * @var string
     */
    public const PERMISSIONS_IDENTIFIER = "PERMISSIONS_";
    
    /**
     * Add a permission for the resource
     * 
     * @param string $name
     *   Permission name
     */
    public function addPermission(string $name): void;
    
    /**
     * Get all permissions for the resource
     * 
     * @return MaskCollection
     *   All permissions
     */
    public function getPermissions(): MaskCollection;
    
    /**
     * Get a specific permission for the resource
     * 
     * @param string $name
     *   Permission name
     * 
     * @return Mask
     *   Permission mask
     *   
     * @throws InvalidResourcePermissionException
     *   When requested permission is not registered
     */
    public function getPermission(string $name): Mask;
    
    /**
     * Check if a permission is registered into the resource
     * 
     * @param string $name
     *   Permission name
     * 
     * @return bool
     *   True if the resource has the given permission
     */
    public function hasPermission(string $name): bool;
    
    /**
     * Get resource behaviour
     * 
     * @var int
     */
    public function getBehaviour(): int;
    
    /**
     * Get resource name
     * 
     * @return string
     *   Resource name
     */
    public function getName(): string;
    
}
