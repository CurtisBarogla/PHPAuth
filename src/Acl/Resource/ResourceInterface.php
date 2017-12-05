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
use Zoe\Component\Security\Acl\Entity\Entity;
use Zoe\Component\Security\Exception\InvalidEntityException;
use Zoe\Component\Security\Exception\RuntimeException;

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
     * 32 bits
     *
     * @var int
     */
    public const MAX_PERMISSIONS = 31;
    
    /**
     * Add a permission for the resource
     * 
     * @param string $name
     *   Permission name
     *   
     * @throws RuntimeException
     *   When impossible to add more permissions for this resource
     */
    public function addPermission(string $name): void;
    
    /**
     * Get all permissions for the resource
     * 
     * @param array|null $permissions
     *   If not null, will only set into the MaskCollection the given ones
     * 
     * @return MaskCollection
     *   All permissions
     */
    public function getPermissions(?array $permissions = null): MaskCollection;
    
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
     * Add an entity into the resource
     * 
     * @param Entity $entity
     *   Entity initialized
     */
    public function addEntity(Entity $entity): void;
    
    /**
     * Get all entities registered into this resource
     * 
     * @return Entity[]
     *   All entities registered
     */
    public function getEntities(): array;
    
    /**
     * Get an entity from the resource
     * 
     * @param string $entity
     *   Entity name
     * 
     * @return Entity
     *   Entity instance
     *   
     * @throws InvalidEntityException
     *   When the given entity name is not registered
     */
    public function getEntity(string $entity): Entity;
    
    /**
     * Get resource behaviour
     * 
     * @return int
     *   One of the resource behaviour const defined
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
