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

use Zoe\Component\Security\User\Contracts\AclUserInterface;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;

/**
 * Object bindable to the acl
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface AclBindableInterface
{
    
    /**
     * Actions done on the user before every call to isGranted
     * 
     * @param AclUserInterface $user
     *   User handled by the acl
     * @param ResourceInterface $resource
     *   Resource instance correspond to the given one via _getName() 
     */
    public function _onBind(AclUserInterface $user, ResourceInterface $resource): void;
    
    /**
     * Get the resource name linked to this object
     * 
     * @return string
     *   Resource name interacting with this object
     */
    public function _getName(): string;
    
}
