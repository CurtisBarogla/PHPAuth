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

use Zoe\Component\Security\Exception\InvalidEntityException;

/**
 * Entities are registered into resource.
 * Values are named and permissions are applied to it.
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class Entity implements \JsonSerializable, \IteratorAggregate
{
    
    /**
     * Processor name handling this resource
     * 
     * @var string
     */
    private $processor;
    
    /**
     * Entity name
     * 
     * @var string
     */
    private $name;
    
    /**
     * Entity values
     * 
     * @var array
     */
    private $values;
    
    /**
     * Initialize entity
     * 
     * @param string $name
     *   Entity name
     * @param string|null $processor
     *   Processor handling the entity or null if entity does not build the user
     */
    public function __construct(string $name, ?string $processor = null)
    {
        $this->name = $name;
        $this->processor = $processor;
    }
    
    /**
     * {@inheritDoc}
     * @see \IteratorAggregate::getIterator()
     */
    public function getIterator(): \Generator
    {
        foreach ($this->values as $role => $permissions)
            yield $role => $permissions;
    }
    
    /**
     * Add a value into the entity
     * 
     * @param string $name
     *   Entity name
     * @param array $permissions
     *   Permissions applied to this entity
     *   
     * @return self
     *   Self
     */
    public function add(string $name, array $permissions): self
    {
        $this->values[$name] = $permissions;
        
        return $this;
    }
    
    /**
     * Check if a value is registered into the entity
     * 
     * @param string $value
     *   Value name
     * 
     * @return bool
     *   True if the value if registered into the entity. False otherwise
     */
    public function has(string $name): bool
    {
        return isset($this->values[$name]);
    }
    
    /**
     * Get all permissions applied to an entity value 
     * 
     * @param string $name
     *   Value name
     * 
     * @return array 
     *   All permissions applied for this value
     *   
     * @throws InvalidEntityException
     *   When given entity is not registered
     */
    public function get(string $name): array
    {
        if(!isset($this->values[$name]))
            throw new InvalidEntityException(\sprintf("This value '%s' into '%s' resource is not registered",
                $name,
                $this->name));
        
        return $this->values[$name];
    }
    
    /**
     * Get the entity name
     * 
     * @return string
     *   Entity name
     */
    public function getName(): string
    {
        return $this->name;        
    }
    
    /**
     * Get the processor handling this entity
     * 
     * @return string|null
     *   Processor name
     */
    public function getProcessor(): ?string
    {
        return $this->processor;        
    }
    
    /**
     * Check if the entity is empty
     * 
     * @return bool
     *   True if the entity has no value setted. False otherwise
     */
    public function isEmpty(): bool
    {
        return null === $this->values;
    }
    
    /**
     * {@inheritDoc}
     * @see \JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize(): array
    {
        return [
            "processor" =>  $this->processor,
            "name"      =>  $this->name,
            "values"    =>  $this->values
        ];
    }
    
    /**
     * Create an entity from his json representation
     * Can be a dejsonified array value or its raw string representation
     * 
     * @param string|array $json
     *   Json entity representation
     * 
     * @return Entity
     *   Entity restored
     */
    public static function createEntityFromJson($json): Entity
    {
        if(!\is_array($json))
            $json = \json_decode($json, true);
        
        $entity = new Entity($json["name"], $json["processor"]);
        $entity->values = $json["values"];
       
        return $entity;
    }
    
}
