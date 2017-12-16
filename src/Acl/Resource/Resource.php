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

/**
 * Basic implementation of ResourceInterface
 * Can be jsonified
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
     * Resource permissions
     * 
     * @var MaskCollection
     */
    private $permissions;
    
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
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::addPermission()
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
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::getBehaviour()
     */
    public function getBehaviour(): int
    {
        return $this->behaviour;
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
            "permissions"   =>  $this->permissions
        ];
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
