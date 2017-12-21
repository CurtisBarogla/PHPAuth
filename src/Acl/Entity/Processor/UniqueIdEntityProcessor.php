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

use Zoe\Component\Security\Acl\AclUserInterface;
use Zoe\Component\Security\Acl\Entity\EntityInterface;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;

/**
 * Process an entity over a unique identifier defined into a user
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
abstract class UniqueIdEntityProcessor implements EntityProcessorInterface
{
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Entity\Processor\EntityProcessorInterface::process()
     */
    public function process(EntityInterface $entity, AclUserInterface $user): void
    {
        if($entity->isEmpty() || !$entity->has($this->getUniqueId($user)))
            return;
        
        $resource = $entity->getResource();
        $permissions = $entity->get($this->getUniqueId($user));
        
        switch ($resource->getBehaviour()) {
            case ResourceInterface::WHITELIST:
                $user->grant($resource, $permissions);
                break;
            case ResourceInterface::BLACKLIST:
                $user->deny($resource, $permissions);
                break;
        }
    }
    
    /**
     * Declare the unique identifier value from an entity to process.
     * 
     * @return string
     *   Unique id
     */
    abstract protected function getUniqueId(AclUserInterface $user): string;
    
}
