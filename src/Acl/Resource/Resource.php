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
use Zoe\Component\Security\Exception\InvalidMaskException;
use Zoe\Component\Security\Exception\InvalidResourcePermissionException;

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
     * Initialize resource
     * 
     * @param string $name
     *   Resource name
     * @param int $behaviour
     *   Resource behaviour on entities (blacklist or whitelist)
     */
    public function __construct(string $name, int $behaviour)
    {
        $this->name = $name;
        $this->behaviour = $behaviour;
        $this->permissions = new MaskCollection(ResourceInterface::PERMISSIONS_IDENTIFIER.$name);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::addPermission()
     */
    public function addPermission(string $name): void
    {
        $index = \count($this->permissions);
        $mask = new Mask($name, 0x0001);
        $mask->left($index);
        
        $this->permissions->add($mask);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::getPermissions()
     */
    public function getPermissions(): MaskCollection
    {
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
            "permissions"   =>  $this->permissions
        ];
    }
    
    /**
     * Create a resource for his json representation
     * Can be a dejsonified array value or its raw string representation
     * 
     * @param string|array $json
     *   Json resource representation
     * 
     * @return ResourceInterface
     *   Resource restaured
     */
    public static function createResourceFromJson($json): ResourceInterface
    {
        if(!\is_array($json))
            $json = \json_decode($json, true);
        
        $permissions = MaskCollection::createCollectionFromJson($json["permissions"]);
        $resource = new Resource($json["name"], $json["behaviour"]);
        $resource->permissions = $permissions;
        
        return $resource;
    }
    
}
