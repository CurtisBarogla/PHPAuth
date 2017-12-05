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

use Zoe\Component\Security\Acl\Entity\Entity;
use Zoe\Component\Security\Acl\Mask\Mask;
use Zoe\Component\Security\Acl\Mask\MaskCollection;
use Zoe\Component\Security\Acl\Mask\MaskFactory;
use Zoe\Component\Security\Exception\InvalidEntityException;
use Zoe\Component\Security\Exception\InvalidMaskException;
use Zoe\Component\Security\Exception\InvalidResourceBehaviourException;
use Zoe\Component\Security\Exception\InvalidResourcePermissionException;
use Zoe\Component\Security\Exception\RuntimeException;

/**
 * Native ResourceInterface implementation
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class Resource implements ResourceInterface, \JsonSerializable
{

    /**
     * Resource name
     * 
     * @var string
     */
    private $name;
    
    /**
     * Resource behaviour
     * 
     * @var int
     */
    private $behaviour;
    
    /**
     * Permissions
     * 
     * @var MaskCollection
     */
    private $permissions;
    
    /**
     * Resource entities
     * 
     * @var Entity[]
     */
    private $entities = [];
    
    /**
     * Initialize resource
     * 
     * @param string $name
     *   Resource name
     * @param int $behaviour
     *   Resource behaviour (blacklist or whitelist)
     */
    public function __construct(string $name, int $behaviour)
    {
        $this->name = $name;
        $this->behaviour = $behaviour;
        $this->validateBehaviour();
        $this->permissions = new MaskCollection(ResourceInterface::PERMISSIONS_IDENTIFIER.$name);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::addPermission()
     */
    public function addPermission(string $name): void
    {
        $index = \count($this->permissions);
        if($index === self::MAX_PERMISSIONS)
            throw new RuntimeException(\sprintf("Cannot add this permission '%s' into '%s' resource. Resource permissions limit is setted to '%d'",
                $name,
                $this->name,
                self::MAX_PERMISSIONS));
        
        $mask = new Mask($name, 0x0001);
        $mask->left($index);
        
        $this->permissions->add($mask);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::getPermissions()
     */
    public function getPermissions(?array $permissions = null): MaskCollection
    {
        if(null !== $permissions) {
            $collection = new MaskCollection(ResourceInterface::PERMISSIONS_IDENTIFIER.$this->name);
            foreach ($permissions as $permission)
                $collection->add($this->getPermission($permission));
            
            return $collection;
        }
        
        return $this->permissions;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::getPermission()
     */
    public function getPermission(string $name): Mask
    {
        try {
            return $this->permissions->get($name);
        } catch (InvalidMaskException $e) {
            throw new InvalidResourcePermissionException($this, $name);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::hasPermission()
     */
    public function hasPermission(string $name): bool
    {
        return $this->permissions->has($name);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::addEntity()
     */
    public function addEntity(Entity $entity): void
    {
        $this->entities[$entity->getName()] = $entity;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::getEntities()
     */
    public function getEntities(): array
    {
        return $this->entities;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::getEntity()
     */
    public function getEntity(string $entity): Entity
    {
        if(!isset($this->entities[$entity]))
            throw new InvalidEntityException(\sprintf("This entity '%s' is not registered into this resource '%s'",
                $entity,
                $this->name));
            
        return $this->entities[$entity];
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::getBehaviour()
     */
    public function getBehaviour(): int
    {
        return $this->behaviour;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::getName()
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * {@inheritdoc}
     * @see \JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize(): array
    {
        return [
            "name"          =>  $this->name,
            "behaviour"     =>  $this->behaviour,
            "permissions"   =>  $this->permissions,
            "entities"      =>  $this->entities
        ];
    }
    
    /**
     * Create a resource from his json representation
     * Can be a dejsonified array value or its raw string representation
     * 
     * @param string|array $json
     *   Json resource representation
     * 
     * @return ResourceInterface
     *   Resource restored
     */
    public static function createResourceFromJson($json): ResourceInterface
    {
        if(!\is_array($json))
            $json = \json_decode($json, true);
        
        $permissions = MaskFactory::createCollectionFromJson($json["permissions"]);
        $resource = new Resource($json["name"], $json["behaviour"]);
        $resource->permissions = $permissions;
        $entities = [];
        foreach ($json["entities"] as $name => $entity) {
            $entities[$name] = Entity::createEntityFromJson($entity);
        }
        $resource->entities = $entities;
        
        return $resource;
    }
    
    /**
     * Validate resource behaviour parameter
     * 
     * @throws InvalidResourceBehaviourException
     *   When given behaviour is not handled
     */
    private function validateBehaviour(): void
    {
        if(
            $this->behaviour !== ResourceInterface::BLACKLIST_BEHAVIOUR && 
            $this->behaviour !== ResourceInterface::WHITELIST_BEHAVIOUR)
            throw new InvalidResourceBehaviourException($this);
    }
    
}
