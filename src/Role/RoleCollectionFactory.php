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

namespace Zoe\Component\Security\Role;

/**
 * Create RoleCollection from external sources
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class RoleCollectionFactory
{
    
    /**
     * Initialize a role collection from his normalized array representation
     * 
     * @param array $roles
     *   Array role collection representation
     * 
     * @return RoleCollection
     *   Role collection initialized
     */
    public static function createRoleCollectionFromArray(array $roles): RoleCollection
    {
        $collection = new RoleCollection();
        foreach ($roles as $role => $parents) {
            if(\is_int($role)) {
                $collection->add($parents);    
            } else {
                $collection->add($role, $parents);
            }
        }
        
        return $collection;
    }
    
}
