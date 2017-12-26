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
use Zoe\Component\Security\Exception\Acl\InvalidMaskException;
use Zoe\Component\Security\Exception\Acl\InvalidResourceBehaviour;
use Zoe\Component\Security\Exception\Acl\InvalidPermissionException;
use Zoe\Component\Security\Acl\Entity\EntityInterface;
use Zoe\Component\Security\Exception\Acl\InvalidEntityException;
use Zoe\Component\Security\Acl\Entity\Entity;
use Zoe\Component\Security\Acl\AclUserInterface;

/**
 * Basic implementation of ResourceInterface
 * Can be jsonified
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class Resource implements ResourceInterface
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
     * Resource permissions
     * 
     * @var MaskCollection
     */
    private $permissions;
    
    /**
     * Registered entities
     * 
     * @var EntityInterface[]|null
     */
    private $entities;
    
    /**
     * If entities has been processed
     * 
     * @var bool
     */
    private $processed = false;

    /**
     * Initialize a resource
     * 
     * @param string $name
     *   Resource name
     * @param int $behaviour
     *   ResourceInterface::BLACKLIST or ResourceInterface::WHITELIST
     */
    public function __construct(string $name, int $behaviour)
    {
        $this->name = $name;
        $this->behaviour = $behaviour;
        $this->checkBehaviour();
        
        $this->permissions = new MaskCollection("PERMISSIONS_{$this->name}");
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
     * Add a permission for the resource
     * 
     * @param string $permission
     *   Permission name
     * 
     * @throws \LogicException
     *   When the permission cannot be added
     */
    public function addPermission(string $permission): void
    {
        $index = \count($this->permissions);
        if($index > self::MAX_PERMISSIONS)
            throw new \LogicException(\sprintf("Cannot add more permission for '%s' resource",
                $this->name));

        $mask = new Mask($permission, 1);
        if($index === 0) {
            $this->permissions->add($mask);
            return;
        }
        
        $mask->lshift($index);
        $this->permissions->add($mask);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::getPermissions()
     */
    public function getPermissions(?array $permissions = null): MaskCollection
    {
        if(null === $permissions) 
            return $this->permissions;
        
        $collection = new MaskCollection("PERMISSIONS");
        foreach ($permissions as $permission)
            $collection->add($this->getPermission($permission));
        
        return $collection;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::getPermission()
     */
    public function getPermission(string $permission): Mask
    {
        try {
            return $this->permissions->get($permission);
        } catch (InvalidMaskException $e) {
            $exception = (new InvalidPermissionException(\sprintf("This permission '%s' is not defined into '%s' resource",
                $permission,
                $this->name)));
            $exception->setInvalidPermission($permission);
            
            throw $exception;
        }
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::hasPermission()
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions->has($permission);
    }
    
    /**
     * Register an entity into the resource
     * 
     * @param EntityInterface $entity
     *   Entity instance
     */
    public function addEntity(EntityInterface $entity): void
    {
        $this->entities[$entity->getIdentifier()] = $entity;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::getEntities()
     */
    public function getEntities(): ?array
    {
        return $this->entities;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::getEntity()
     */
    public function getEntity(string $entity): EntityInterface
    {
        if(!isset($this->entities[$entity]))
            throw new InvalidEntityException(\sprintf("This entity '%s' for resource '%s' is not registered",
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
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::process()
     */
    public function process(AclUserInterface $user, array $processors): void
    {
        if(null === $this->entities) {
            $this->processed = true;
            return;
        }
        
        foreach ($this->entities as $entity) {
            $processor = $entity->getProcessor();
            if(null === $processor || $entity->isEmpty())
                continue;
            
            if(!isset($processors[$processor]))
                throw new \RuntimeException(\sprintf("This processor '%s' for '%s' entity into '%s' resource is not registered",
                    $processor,
                    $entity->getIdentifier(),
                    $this->name));
            
            if($entity instanceof ResourceAwareInterface)
                $entity->setResource($this);
            
            $processors[$processor]->process($entity, $user);
        }
        
        $this->processed = true;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::isProcessed()
     */
    public function isProcessed(): bool
    {
        return $this->processed;
    }
    
    /**
     * {@inheritDoc}
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
     * @return Resource
     *   Restored resource
     * 
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Common\JsonSerializable
     */
    public static function restoreFromJson($json): Resource
    {
        if(!\is_array($json))
            $json = \json_decode($json, true);
        
        $resource = new Resource($json["name"], $json["behaviour"]);
        $resource->permissions = MaskCollection::restoreFromJson($json["permissions"]);
        if(null !== $json["entities"])
            $resource->entities = \array_map(function(array $entity): EntityInterface {
                return Entity::restoreFromJson($entity); 
            }, $json["entities"]);
        
        return $resource;
    }
    
    /**
     * Check if the behaviour property is valid
     * 
     * @throws InvalidResourceBehaviour
     *   If the setted behaviour is invalid
     */
    private function checkBehaviour(): void
    {
        if(!\in_array($this->behaviour, [ResourceInterface::BLACKLIST, ResourceInterface::WHITELIST]))
            throw new InvalidResourceBehaviour(\sprintf("Given behaviour is invalid for '%s' resource",
                $this->name));
    }
    
}
