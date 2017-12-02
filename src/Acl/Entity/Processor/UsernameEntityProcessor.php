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

use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\User\Contracts\AclUserInterface;
use Zoe\Component\Security\Acl\Entity\Entity;

/**
 * Process entity over username
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UsernameEntityProcessor extends AbstractEntityProcessor
{
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Entity\Processor\EntityProcessorInterface::buildUser()
     */
    public function processUser(AclUserInterface $user, ResourceInterface $resource, Entity $entity): void
    {
        if($entity->isEmpty() || !$entity->has($user->getName())) {
            $this->process = true;
            
            return;
        }
        
        switch ($resource->getBehaviour()) {
            case ResourceInterface::BLACKLIST_BEHAVIOUR:
                $user->deny($resource, $entity->get($user->getName()));
                break;
            case ResourceInterface::WHITELIST_BEHAVIOUR:
                $user->grant($resource, $entity->get($user->getName()));
                break;
        }
        
        $this->process = true;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Entity\Processor\EntityProcessorInterface::getName()
     */
    public function getName(): string
    {
        return "UsernameProcessor";
    }
    
}
