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

use Zoe\Component\Security\User\Contracts\StorableUserInterface;

/**
 * User destined to be stored
 * Storable user can be "jsonify".
 * Attributes setted which not implement JsonSerializable interface will loss their values during the process
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
final class StorableUser extends User implements StorableUserInterface, \JsonSerializable
{
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\StorableUserInterface::storable()
     */
    public function storable(): bool
    {
        return true;
    }
    
    /**
     * {@inheritdoc}
     * @see \JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize(): array
    {
        return [
            "name"          =>  $this->name,
            "root"          =>  $this->isRoot,
            "attributes"    =>  $this->attributes,
            "roles"         =>  $this->roles
        ];
    }

}
