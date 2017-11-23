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

use Zoe\Component\Security\Exception\RoleNotFoundException;

/**
 * Collection of roles
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class RoleCollection implements \JsonSerializable
{
    
    /**
     * Main roles
     * 
     * @var array
     */
    private $roles;
    
    /**
     * Sub-roles (if exists) for each main roles
     * 
     * @var array
     */
    private $inheritance;
    
    /**
     * Add a role into the collection
     * 
     * @param string $role
     *   Main role
     * @param array $childrens
     *   Childrens (must be registered)
     *   
     * @throws RoleNotFoundException
     *   When a children role is not registered
     */
    public function add(string $role, array $parents = []): void
    {
        $this->roles[$role] = $role;
        if(!empty($parents)) {
            $inheritance = [];
            foreach ($parents as $parent) {
                if(!isset($this->roles[$parent]))
                    throw new RoleNotFoundException(\sprintf("This parent role '%s' for role '%s' is not registered",
                        $parent,
                        $role));
                    
                if(isset($this->inheritance[$parent])) {
                    foreach ($this->inheritance[$parent] as $subrole) {
                        $inheritance[] = $subrole;
                    }
                }
            }

            $this->inheritance[$role] = \array_unique(\array_merge_recursive($parents, $inheritance));
        }
    }
    
    /**
     * Get a role with - if exists - his sub roles
     * 
     * @param string $role
     *   Role to get
     * 
     * @return array
     *   Array of roles
     *   
     * @throws RoleNotFoundException
     *   When the given role is not setted
     */
    public function getRole(string $role): array
    {
        if(!isset($this->roles[$role]))
            throw new RoleNotFoundException(\sprintf("This role '%s' is not setted",
                $role));
        
        if(!isset($this->inheritance[$role]))
            return [$this->roles[$role]];

        return \array_merge([$role], $this->inheritance[$role]);
    }
    
    /**
     * {@inheritdoc}
     * @see \JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize(): array
    {
        return [
            "roles"     =>  $this->roles,
            "childrens" =>  $this->inheritance
        ];
    }
    
    /**
     * Create a RoleCollection with his json representation
     * 
     * @param array|string $json
     *   Json RoleCollection representation
     * 
     * @return RoleCollection
     *   Role collection
     */
    public static function createRoleCollectionFromJson($json): RoleCollection
    {
        if(!\is_array($json))
            $json = \json_decode($json, true);
        
        $collection = new RoleCollection();
        $collection->roles = $json["roles"];
        $collection->inheritance = $json["childrens"];
        
        return $collection;
    }
    
}
