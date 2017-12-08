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

use Zoe\Component\Security\Acl\Entity\Entity;
use Zoe\Component\Security\Acl\Resource\Resource;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;

/**
 * Load resource from an array defining all resources
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NativeResourceLoader extends AbstractResourceLoader
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
     * @see \Zoe\Component\Security\Acl\Resource\Loader\ResourceLoaderInterface::register()
     */
    public function register(): array
    {
        return \array_keys($this->resources);
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\Loader\AbstractResourceLoader::doLoadResource()
     */
    protected function doLoadResource(string $resource): ?ResourceInterface
    {
        if(!isset($this->resources[$resource]))
            return null;
            
        $behaviour = ($this->resources[$resource]["behaviour"] === "whitelist") ? ResourceInterface::WHITELIST_BEHAVIOUR : ResourceInterface::BLACKLIST_BEHAVIOUR;
        $resourceInstance = new Resource($resource, $behaviour);
        if(isset($this->resources[$resource]["permissions"])) {
            foreach ($this->resources[$resource]["permissions"] as $permission) {
                $resourceInstance->addPermission($permission);
            }
        }
        if(isset($this->resources[$resource]["entities"])) {
            foreach ($this->resources[$resource]["entities"] as $name => $entityValues) {
                $entity = new Entity($name, $entityValues["processor"] ?? null);
                foreach ($entityValues["values"] as $value => $permissions) {
                    $entity->add($value, $permissions);
                }
                $resourceInstance->addEntity($entity);
            }
        }
        
        return $resourceInstance;
    }

}
