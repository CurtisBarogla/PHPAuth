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

use Psr\SimpleCache\CacheInterface;
use Zoe\Component\Security\Acl\Resource\Resource;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;

/**
 * Wrap a loader over a PSR-16 Cache implementation
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class CacheWrapperResourceLoader extends AbstractResourceLoader
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
     * @see \Zoe\Component\Security\Acl\Resource\Loader\ResourceLoaderInterface::register()
     */
    public function register(): array
    {
        if(null === $resources = $this->cache->get(self::CACHE_RESOURCE_PREFIX."_Cached_Map_Names")) {
            $resources = $this->loader->register();
            
            $this->cache->set(self::CACHE_RESOURCE_PREFIX."_Cached_Map_Names", \json_encode($resources));
            
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
        $resources[] = self::CACHE_RESOURCE_PREFIX."_Cached_Map_Names";
        
        return $this->cache->deleteMultiple($resources);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\Loader\AbstractResourceLoader::doLoadResource()
     */
    protected function doLoadResource(string $resource): ?ResourceInterface
    {
        if(null === $cache = $this->cache->get(self::CACHE_RESOURCE_PREFIX."_{$resource}")) {
            $resourceInstance = $this->loader->loadResource($resource);
            
            $this->cache->set(self::CACHE_RESOURCE_PREFIX."_{$resource}", \json_encode($resourceInstance));
            
            return $resourceInstance;
        } else {
            return Resource::createResourceFromJson($cache);
        }
    }

}
