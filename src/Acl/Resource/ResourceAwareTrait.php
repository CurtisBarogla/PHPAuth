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
     * {@inheritdoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceAwareInterface::getResource()
     */
    public function getResource(): ResourceInterface
    {
        return $this->resource;
    }
    
    /**
     * {@inheritdoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceAwareInterface::setResource()
     */
    public function setResource(ResourceInterface $resource): void
    {
        $this->resource = $resource; 
    }
    
}
