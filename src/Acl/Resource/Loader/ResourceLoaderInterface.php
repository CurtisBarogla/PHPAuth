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
 * Responsible to load a resource from various formats
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface ResourceLoaderInterface
{
    
    /**
     * Load a resource by its name
     * 
     * @param string $name
     *   Resource name
     * 
     * @return ResourceInterface
     *   Resource instance with all informations setted
     *   
     * @throws ResourceNotFoundException
     *   When the resource cannot be loaded
     */
    public function loadResource(string $name): ResourceInterface;
    
    /**
     * Get names of all resources loadables by this loader
     * 
     * @return array
     *   Name of all resources
     */
    public function register(): array;
    
}
