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

namespace Zoe\Component\Security\Exception\Acl;

/**
 * When a resource permission is not setted
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class InvalidPermissionException extends \InvalidArgumentException
{
    
    /**
     * Invalid resource permission name
     * 
     * @var string
     */
    private $invalidPermission;
    
    /**
     * Set invalid permission
     * 
     * @param string $invalidPermission
     *   Invalid permission
     */
    public function setInvalidPermission(string $invalidPermission): void
    {
        $this->invalidPermission = $invalidPermission;
    }
    
    /**
     * Get the setted invalid permission
     * 
     * @return string|null
     *   Invalid permission
     */
    public function getInvalidPermission(): ?string
    {
        return $this->invalidPermission;
    }
    
}