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

use Zoe\Component\Security\Acl\Mask\MaskCollection;
use Zoe\Component\Security\Acl\Mask\Mask;
use Zoe\Component\Security\Exception\Acl\InvalidPermissionException;
use Zoe\Component\Security\Acl\Entity\EntityInterface;
use Zoe\Component\Security\Exception\Acl\InvalidEntityException;
use Zoe\Component\Security\Common\JsonSerializable;

/**
 * Resource are registered into an acl.
 * Defined all permissions and entities linked to it
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface ResourceInterface extends JsonSerializable
{
    
    /**
     * User has by default all permissions over the resource.
     * Permissions are blacklisted
     * 
     * @var int
     */
    public const BLACKLIST = 0;
    
    /**
     * User has by default no permission over the resource
     * Permissions are whitelisted
     * 
     * @var int
     */
    public const WHITELIST = 1;
    
    /**
     * 32 bits
     * 
     * @var int
     */
    public const MAX_PERMISSIONS = 31;
    
    /**
     * Get resource name
     * 
     * @return string
     *   Resource name
     */
    public function getName(): string;
    
    /**
     * Add a permission
     * 
     * @param string $permission
     *   Permission name
     *   
     * @throws \LogicException
     *   When max permissions count is reached
     */
    public function addPermission(string $permission): void;
    
    /**
     * Get all permissions associated to this resource, or only defined ones
     * 
     * @param array|null $permissions
     *   Specifics permissions or null to get all permissions
     *  
     * @return MaskCollection
     *   All permissions (requested)
     *   
     * @throws InvalidPermissionException
     *   When a specified permission is not setted
     */
    public function getPermissions(?array $permissions = null): MaskCollection;
    
    /**
     * Get a specific resource permission
     * 
     * @param string $permission
     *   Permission name
     * 
     * @return Mask
     *   Permission
     *   
     * @throws InvalidPermissionException
     *   When the given permission is not setted
     */
    public function getPermission(string $permission): Mask;
    
    /**
     * Check if a permission is defined for the resource
     * 
     * @param string $permission
     *   Permission name
     * 
     * @return bool
     *   True if the permission is setted. False otherwise
     */
    public function hasPermission(string $permission): bool;
    
    /**
     * Add an entity to the resource
     * 
     * @param EntityInterface $entity
     *   Resource entity
     */
    public function addEntity(EntityInterface $entity): void;
    
    /**
     * Get all entities registered for this resource
     * 
     * @return EntityInterface[]
     *   All entitied registered
     */
    public function getEntities(): array;
    
    /**
     * Get a resource entity
     * 
     * @param string $entity
     *   Entity name
     * 
     * @return EntityInterface
     *   Entity instance
     *   
     * @throws InvalidEntityException
     *   When the given entity is not registered
     */
    public function getEntity(string $entity): EntityInterface;
    
    /**
     * Get resource behaviour
     * Use one of the const defined into the interface if comparaison is done
     * 
     * @return int
     *   Resource behaviour
     */
    public function getBehaviour(): int;
    
}
