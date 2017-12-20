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
use Zoe\Component\Security\Exception\Acl\InvalidEntityValueException;

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
     * Add a value to the entity
     * 
     * @param string $entity
     *   Entity name
     * @param array $permissions
     *   Permissions accorded to this value
     */
    public function add(string $value, array $permissions): void
    {
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
            
            $exception = new InvalidEntityValueException($message);
            $exception->setInvalidValue($value);
            
            throw $exception;
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
