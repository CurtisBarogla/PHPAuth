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

namespace Zoe\Component\Security\Acl\Entity\Processor;

use Zoe\Component\Security\User\Contracts\AclUserInterface;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Acl\Entity\Entity;

/**
 * Entity builder.
 * Basically, just build (for now) the acl user over raw entity registered into a resource
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface EntityProcessorInterface
{
    
    /**
     * Process the resource entity over the user
     * 
     * @param AclUserInterface $user
     *   Acl user
     * @param ResourceInterface $resource
     *   Resource handled by the entity
     * @param Entity $entity
     *   Entity processed
     */
    public function processUser(AclUserInterface $user, ResourceInterface $resource, Entity $entity): void;
    
    /**
     * Get the name of the entity type
     * 
     * @return string
     *   Entity name
     */
    public function getName(): string;
    
    /**
     * Check if the processor has been executed
     * 
     * @return bool
     *   True if the process has been executed. False otherwise
     */
    public function isProcess(): bool;
    
}
