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
use Psr\SimpleCache\CacheInterface;
use Zoe\Component\Security\Acl\Resource\Resource;

/**
 * Wrap a loader over a PSR-16 Cache implementation
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class CacheWrapperResourceLoader implements ResourceLoaderInterface
{
    
    /**
     * Resource loader to wrap
     * 
     * @var ResourceLoaderInterface
     */
    private $loader;
    
    /**
     * PSR-16 Implementation
     * 
     * @var CacheInterface
     */
    private $cache;
    
    /**
     * Resources hitted from cache
     * 
     * @var ResourceInterface[]
     */
    private $loaded = [];
    
    /**
     * Prefix used to register cached resource
     * 
     * @var string
     */
    public const CACHE_RESOURCE_PREFIX = "Acl_Cache_Resource";
    
    /**
     * Initialize loader
     * 
     * @param ResourceLoaderInterface $loader
     *   Resource loader to wrap
     * @param CacheInterface $cache
     *   PSR-16 Cache implementation
     */
    public function __construct(ResourceLoaderInterface $loader, CacheInterface $cache)
    {
        $this->loader = $loader;
        $this->cache = $cache;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\Loader\ResourceLoaderInterface::loadResource()
     */
    public function loadResource(string $name): ResourceInterface
    {
        if(null === $resource = $this->cache->get(self::CACHE_RESOURCE_PREFIX."_{$name}")) {
            $resource = $this->loader->loadResource($name);
            
            $this->cache->set(self::CACHE_RESOURCE_PREFIX."_{$name}", \json_encode($resource));
            
            return $resource;
        } else {
            return (!isset($this->loaded[$name])) 
                        ? $this->loaded[$name] = Resource::createResourceFromJson($resource) 
                        : $this->loaded[$name];
        }
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\Loader\ResourceLoaderInterface::register()
     */
    public function register(): array
    {
        if(null === $resources = $this->cache->get(self::CACHE_RESOURCE_PREFIX."_Names")) {
            $resources = $this->loader->register();
            
            $this->cache->set(self::CACHE_RESOURCE_PREFIX."_Names", \json_encode($resources));
            
            return $resources;
        } else {
            return \json_decode($resources, true);
        }
    }
    
    /**
     * Clear all resources cached
     * 
     * @return bool
     *   True if all resources declared via registered are cleared. 
     *   Can return false if a registered resource is not cached
     */
    public function clear(): bool
    {
        $resources = \array_map(function(string $name): string {
            return self::CACHE_RESOURCE_PREFIX."_{$name}";
        }, $this->loader->register());
        $resources[] = self::CACHE_RESOURCE_PREFIX."_Names";
        
        return $this->cache->deleteMultiple($resources);
    }

}
