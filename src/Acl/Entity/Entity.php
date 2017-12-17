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

use Zoe\Component\Security\Acl\Resource\ResourceAwareInterface;
use Zoe\Component\Security\Acl\Resource\ResourceAwareTrait;
use Zoe\Component\Security\Exception\Acl\InvalidEntityException;
use Zoe\Component\Security\Acl\Resource\ImmutableResourceInterface;

/**
 * Native implementation of EntityInterface
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class Entity implements EntityInterface, ResourceAwareInterface
{
    
    use ResourceAwareTrait;
    
    /**
     * Resource identifier
     * 
     * @var string
     */
    private $identifier;
    
    /**
     * Processor name
     * 
     * @var string|null
     */
    private $processor;
    
    /**
     * Values setted into the entity
     * 
     * @var array
     */
    private $values = [];
    
    /**
     * Initialize entity
     * 
     * @param string $identifier
     *   Entity identifier
     * @param string $processor
     *   Entity processor
     */
    public function __construct(string $identifier, ?string $processor = null)
    {
        $this->identifier = $identifier;
        $this->processor = $processor;
    }
    
    /**
     * {@inheritdoc}
     * @see \IteratorAggregate::getIterator()
     * 
     * @return \Generator
     */
    public function getIterator(): \Generator
    {
        foreach ($this->values as $name => $permission) {
            yield $name => $permission;
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Entity\EntityInterface::getIdentifier()
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Entity\EntityInterface::add()
     */
    public function add(string $value, array $permissions): void
    {
        if($this->resource instanceof ImmutableResourceInterface)
            throw new \BadMethodCallException(\sprintf("Cannot add value to this entity '%s' as the resource linked '%s' is in an immutable state",
                $this->identifier,
                $this->resource->getName()));
        
        $this->values[$value] = $permissions;
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Entity\EntityInterface::get()
     */
    public function get(string $value): array
    {
        if(!isset($this->values[$value])) {
            $message = (null === $this->resource) 
                ? \sprintf("This value '%s' for entity '%s' not linked is invalid",
                        $value,
                        $this->identifier)
                : \sprintf("This value '%s' for entity '%s' linked to '%s' resource is invalid",
                        $value,
                        $this->identifier,
                        $this->getResource()->getName());
            
            throw new InvalidEntityException($message);
            
        }
            
        return $this->values[$value];
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Entity\EntityInterface::has()
     */
    public function has(string $value): bool
    {
        return isset($this->values[$value]);
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Entity\EntityInterface::isEmpty()
     */
    public function isEmpty(): bool
    {
        return empty($this->values);
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Entity\EntityInterface::getProcessor()
     */
    public function getProcessor(): ?string
    {
        return $this->processor;
    }
    
    /**
     * {@inheritDoc}
     * @see \JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize(): array
    {
        return [
            "identifier"    =>  $this->identifier,
            "processor"     =>  $this->processor,
            "values"        =>  $this->values
        ];
    }
    
    /**
     * @return Entity
     *   Restored entity
     * 
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Common\JsonSerializable
     */
    public static function restoreFromJson($json): Entity
    {
        if(!\is_array($json))
            $json = \json_decode($json, true);
        
        $entity = new Entity($json["identifier"], $json["processor"]);
        $entity->values = $json["values"];
        
        return $entity;
    }
    
}
