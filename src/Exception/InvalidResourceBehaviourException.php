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

namespace Zoe\Component\Security\Exception;

use Zoe\Component\Security\Acl\Resource\ResourceInterface;

/**
 * InvalidResourceBehaviourException Security component
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class InvalidResourceBehaviourException extends InvalidArgumentException
{
    
    /**
     * Initialize exception
     * 
     * @param ResourceInterface $resource
     *   Resource instance which behaviour is invalid
     */
    public function __construct(ResourceInterface $resource)
    {
        parent::__construct(\sprintf("Behaviour given for resource '%s' is invalid. Use constant values defined into ResourceInterface",
            $resource->getName()));
    }
    
}
