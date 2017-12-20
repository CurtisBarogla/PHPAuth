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
 * Wrap a resource loader to use cache mechanisms over an already loaded resource 
 * 
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class CacheWrapperResourceLoader implements ResourceLoaderInterface
{
    
    /**
     * Resource loader wrapped
     * 
     * @var ResourceLoaderInterface
     */
    private $loader;
    
    /**
     * PSR-16 Cache
     * 
     * @var CacheInterface
     */
    private $cache;
    
    /**
     * Cache key prefix
     * 
     * @var string
     */
    private $cachePrefix;
    
    /**
     * Initialize loader
     * 
     * @param ResourceLoaderInterface $loader
     *   Resource loader to wrap
     * @param CacheInterface $cache
     *   PSR-16 Cache implementation
     * @param string|null $cachePrefix
     *   Cache key prefix ( PREFIX_{resource_name} ). Can be null
     */
    public function __construct(ResourceLoaderInterface $loader, CacheInterface $cache, ?string $cachePrefix)
    {
        $this->loader = $loader;
        $this->cache = $cache;
        $this->cachePrefix = $cachePrefix;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\Loader\ResourceLoaderInterface::loadResource()
     */
    public function loadResource(string $name): ResourceInterface
    {
        if(null === $resource = $this->cache->get($this->cachePrefix."_{$name}")) {
            $resource = $this->loader->loadResource($name);
            
            $this->cache->set($this->cachePrefix."_{$name}", \json_encode($resource));
            
            return $resource;
        } else {
            return Resource::restoreFromJson($resource);
        }
    }

}
