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

/**
 * Responsible to load resources from external sources
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface ResourceLoaderInterface
{
    
    /**
     * Load a resource
     * 
     * @param string $name
     *   Resource name to load
     * 
     * @return ResourceInterface
     *   Resource loaded
     *   
     * @throws ResourceNotFoundException
     *   When the resource cannot be loaded
     */
    public function loadResource(string $name): ResourceInterface;
    
}
