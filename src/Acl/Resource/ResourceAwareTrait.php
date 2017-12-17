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

/**
 * Shorcut to make a component aware of a resource
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
trait ResourceAwareTrait
{
    
    /**
     * Resource to make aware
     * 
     * @var ResourceInterface
     */
    protected $resource;
    
    /**
     * Get the register resource
     * 
     * @return ResourceInterface
     *   Resource registered
     */
    public function getResource(): ResourceInterface
    {
        return $this->resource;
    }
    
    /**
     * Register the register
     * 
     * @param ResourceInterface $resource
     *   Resource to register
     */
    public function setResource(ResourceInterface $resource): void
    {
        $this->resource = $resource; 
    }
    
}
