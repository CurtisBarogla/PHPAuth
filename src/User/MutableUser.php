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

namespace Zoe\Component\Security\User;

use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\Exception\InvalidUserAttributeException;

/**
 * Basic mutable user implementation
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MutableUser extends User implements MutableUserInterface
{
        
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\MutableUserInterface::addRole()
     */
    public function addRole(string $role): MutableUserInterface
    {
        $this->roles[$role] = $role;
        
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\MutableUserInterface::addAttribute()
     */
    public function addAttribute(string $attribute, $value): MutableUserInterface
    {
        $this->attributes[$attribute] = $value;
        
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\MutableUserInterface::deleteAttribute()
     */
    public function deleteAttribute(string $attribute): MutableUserInterface
    {
        if(!isset($this->attributes[$attribute]))
            throw new InvalidUserAttributeException($this, $attribute);
        
        unset($this->attributes[$attribute]);
        
        return $this;
    }

}