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
 * Make a component aware of a resource
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface ResourceAwareInterface
{

    /**
     * Get the resource
     * 
     * @return ResourceInterface
     *   Resource
     */
    public function getResource(): ResourceInterface;
    
    /**
     * Set the resource
     * 
     * @param ResourceInterface $resource
     *   Resource
     */
    public function setResource(ResourceInterface $resource): void;
    
}
