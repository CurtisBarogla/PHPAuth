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

namespace Zoe\Component\Security\Acl\Resource\Loader;

use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Exception\ResourceNotFoundException;
use Zoe\Component\Security\Acl\Resource\Resource;
use Zoe\Component\Security\Acl\Entity\Entity;

/**
 * Load resource from an array defining all resources
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NativeResourceLoader implements ResourceLoaderInterface
{
    
    /**
     * Array defining all resources
     * 
     * @var array
     */
    private $resources;
    
    /**
     * Initialize loader
     * 
     * @param array $resources
     *   Array setting all resources
     */
    public function __construct(array $resources)
    {
        $this->resources = $resources;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\Loader\ResourceLoaderInterface::loadResource()
     */
    public function loadResource(string $name): ResourceInterface
    {
        if(!isset($this->resources[$name]))
            throw new ResourceNotFoundException(\sprintf("This resource '%s' cannot be loaded",
                $name));
            
        $behaviour = ($this->resources[$name]["behaviour"] === "whitelist") ? ResourceInterface::WHITELIST_BEHAVIOUR : ResourceInterface::BLACKLIST_BEHAVIOUR;
        $resource = new Resource($name, $behaviour);
        if(isset($this->resources[$name]["permissions"])) {
            foreach ($this->resources[$name]["permissions"] as $permission) {
                $resource->addPermission($permission);
            }
        }
        if(isset($this->resources[$name]["entities"])) {
            foreach ($this->resources[$name]["entities"] as $name => $entityValues) {
                $entity = new Entity($name, $entityValues["processor"] ?? null);
                foreach ($entityValues["values"] as $value => $permissions) {
                    $entity->add($value, $permissions);
                }
                $resource->addEntity($entity);
            }
        }
        
        return $resource;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\Loader\ResourceLoaderInterface::register()
     */
    public function register(): array
    {
        return \array_keys($this->resources);
    }

}
