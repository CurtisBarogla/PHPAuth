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

namespace Zoe\Component\Security\Acl\Entity;

use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Common\JsonSerializable;
use Zoe\Component\Security\Exception\Acl\InvalidEntityValueException;

/**
 * Entities are linked to resource.
 * Entity can be mostly anything (role, username...) and can be processed over EntityProcessor 
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface EntityInterface extends JsonSerializable, \IteratorAggregate
{
    
    /**
     * Get entity identifier 
     * 
     * @return string
     *   Entity identifier
     */
    public function getIdentifier(): string;
    
    /**
     * Add a value to the entity
     * 
     * @param string $entity
     *   Entity name
     * @param array $permissions
     *   Permissions accorded to this value 
     *   (all permissions defined must refer existing one from the resource which the entity is associated)
     *   
     * @throws \BadMethodCallException
     *   If the resoure which the entity is linked is immutable
     */
    public function add(string $value, array $permissions): void;
    
    /**
     * Get a value from the entity
     * 
     * @param string $entity
     *   Entity name
     * 
     * @return array
     *   Array of permissions associated to this value
     *   
     * @throws InvalidEntityValueException
     *   If the entity value if not registered
     */
    public function get(string $value): array;
    
    /**
     * Check if a value is registered into the entity
     * 
     * @param string $value
     *   Value name
     * 
     * @return bool
     *   True if the value is registered. False otherwise
     */
    public function has(string $value): bool;
    
    /**
     * Check if the entity has values defined into it
     * 
     * @return bool
     *   True if the entity has no value associated to it. False otherwise
     */
    public function isEmpty(): bool;
    
    /**
     * Get the processor handling the entity.
     * Can be null
     * 
     * @return string|null
     */
    public function getProcessor(): ?string;
    
    /**
     * Get resource which this entity is linked
     * 
     * @return ResourceInterface
     *   Resource linked to this entity
     */
    public function getResource(): ResourceInterface;
    
}
