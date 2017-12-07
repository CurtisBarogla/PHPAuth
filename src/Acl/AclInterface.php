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

namespace Zoe\Component\Security\Acl;

use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Exception\ResourceNotFoundException;
use Zoe\Component\Security\User\Contracts\AclUserInterface;
use Zoe\Component\Security\Acl\Entity\Processor\EntityProcessorInterface;
use Zoe\Component\Security\Exception\RuntimeException;

/**
 * Main entry to the acl component
 * Manage user's permissions over resources
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface AclInterface
{
    
    /**
     * Add an entity processor for handling resource entities
     * 
     * @param EntityProcessorInterface $processor
     *   Entity process implementation
     */
    public function addEntityProcessor(EntityProcessorInterface $processor): void;
    
    /**
     * Execute all entity's processors over registered one
     * 
     * @param AclUserInterface $user
     *   User to process
     * 
     * @throws RuntimeException
     *   When a processor registered into an entity is not registered into the acl
     */
    public function executeProcessables(AclUserInterface $user): void;
    
    /**
     * Get a resource
     * 
     * @param string $resource
     *   Resource name
     * 
     * @return ResourceInterface
     *   Resource with permissions and entities setted
     *   
     * @throws ResourceNotFoundException
     *   When the given resource is not found
     */
    public function getResource(string $resource): ResourceInterface;
    
    /**
     * Check if a user has the permissions to do actions over a resource
     * 
     * @param AclUserInterface $user
     *   Acl user
     * @param string $resource
     *   Resource name
     * @param array $permissions
     *   Permissions to check
     * 
     * @return bool
     *   True if the user is granted over all permissions given and resource is setted. False otherwise
     */
    public function isGranted(AclUserInterface $user, string $resource, array $permissions): bool;
    
    /**
     * Register a bindable object 
     * 
     * @param AclBindableInterface $bindable
     *   Bindable object
     */
    public function bind(AclBindableInterface $bindable): void;
    
}
