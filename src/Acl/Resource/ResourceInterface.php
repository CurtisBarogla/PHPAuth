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
use Zoe\Component\Security\Acl\Entity\Processor\EntityProcessorInterface;
use Zoe\Component\Security\Acl\AclUserInterface;

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
     * Get all entities registered for this resource
     * 
     * @return EntityInterface[]|null
     *   All entitied registered. Return null if no entity has been registered
     */
    public function getEntities(): ?array;
    
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
    
    /**
     * Process all registered entities over a set of entity processors.
     * All processors are indexed by their identifier
     * 
     * @param AclUserInterface $user
     *   User processed
     * @param EntityProcessorInterface[] $processors
     *   Set of entity processors
     *   
     * @throws \RuntimeException
     *   When a processor is not registered or invalid
     */
    public function process(AclUserInterface $user, array $processors): void;
    
    /**
     * Check if entities associated to the resource has been processed
     * 
     * @return bool
     *   True if all entities has been processed. False otherwise
     */
    public function isProcessed(): bool;
    
}
