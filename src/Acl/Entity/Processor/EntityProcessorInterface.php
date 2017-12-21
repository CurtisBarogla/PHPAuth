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

use Zoe\Component\Security\Acl\Entity\EntityInterface;
use Zoe\Component\Security\Acl\AclUserInterface;

/**
 * Process an AclUser over entity which a processor has been attached
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface EntityProcessorInterface
{
    
    /**
     * Process an entity which the processor has been linked over the acl user.
     * Resource which the entity has been associated is linked at this moment and is accessible via the getResource entity accessor
     * 
     * @param EntityInterface $entity
     *   Entity processed
     * @param AclUserInterface $user
     *   AclUser processed
     */
    public function process(EntityInterface $entity, AclUserInterface $user): void;
    
    /**
     * Get the processor identifier
     * 
     * @return string
     *   Processor identifier
     */
    public function getIdentifier(): string;
    
}
