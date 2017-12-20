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
use Zoe\Component\Security\Exception\Acl\ResourceNotFoundException;
use Zoe\Component\Security\Acl\Resource\Resource;
use Zoe\Component\Security\Acl\Entity\Entity;

/**
 * Load resources from an array
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NativeResourceLoader implements ResourceLoaderInterface
{
    
    /**
     * Resources setted
     * 
     * @var array
     */
    private $resources;
    
    /**
     * Initialize loader
     * 
     * @param array $resources
     *   Array with all resources configured
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
            
        $infos = $this->resources[$name];
        $resource = new Resource($name, $infos["behaviour"]);
        foreach ($infos["permissions"] as $permission) {
            $resource->addPermission($permission);
        }
        if(isset($infos["entities"])) {
            foreach ($infos["entities"] as $identifier => $entity) {
                $entityInstance = new Entity($identifier, $entity["processor"] ?? null);
                foreach ($entity["values"] as $value => $permissions) {
                    $entityInstance->add($value, $permissions);
                }
                $resource->addEntity($entityInstance);
            }
        }
        
        return $resource;
    }
    
}
