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

/**
 * Process the user attributing permissions over its name
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UsernameEntityProcessor extends UniqueIdEntityProcessor
{
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Entity\Processor\EntityProcessorInterface::getIdentifier()
     */
    public function getIdentifier(): string
    {
        return "UsernameProcessor";
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Entity\Processor\UniqueIdEntityProcessor::getUniqueId()
     */
    protected function getUniqueId(AclUserInterface $user): string
    {
        return $user->getName();
    }

}
