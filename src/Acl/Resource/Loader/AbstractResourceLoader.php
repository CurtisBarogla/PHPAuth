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

/**
 * Common to all resource loaders
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
abstract class AbstractResourceLoader implements ResourceLoaderInterface
{
    
    /**
     * Resource already loaded
     * 
     * @var ResourceLoaderInterface[]
     */
    private $loaded = [];
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\Loader\ResourceLoaderInterface::loadResource()
     */
    public function loadResource(string $name): ResourceInterface
    {
        if(isset($this->loaded[$name]))
            return $this->loaded[$name];
        else {
            if(null === $resource = $this->doLoadResource($name)) {
                throw new ResourceNotFoundException(\sprintf("This resource '%s' cannot be loaded",
                    $name));
            }
            
            $this->loaded[$name] = $resource;
            
            return $resource;
        }
    }
    
    /**
     * Responsible to load a resource
     * 
     * @param string $resource
     *   Resource to load
     * 
     * @return ResourceInterface|null
     *   Resource instance setted or null if the resource cannot be loaded
     */
    abstract protected function doLoadResource(string $resource): ?ResourceInterface;
    
}
