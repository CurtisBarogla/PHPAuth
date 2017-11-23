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
 * InvalidResourcePermissionException Security component
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class InvalidResourcePermissionException extends InvalidArgumentException
{
    
    /**
     * Initialize exception
     * 
     * @param ResourceInterface $resource
     *   Resource which invalid permission is given
     * @param string $invalidPermission
     *   Invalid permission name
     */
    public function __construct(ResourceInterface $resource, string $invalidPermission)
    {
        parent::__construct(\sprintf("This permission '%s' for the resource '%s' is not registered",
            $invalidPermission,
            $resource->getName()));
    }
    
}
