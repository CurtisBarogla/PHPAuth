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

namespace Zoe\Component\Security\Storage;

use Zoe\Component\Security\Exception\LogicException;

/**
 * Interacting with a UserStorage
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface UserStorageAwareInterface
{
    
    /**
     * Get the setted UserStorage
     * 
     * @return UserStorageInteface
     *   User storage instance
     * @throws LogicException
     *   When the storage is not setted
     */
    public function getStorage(): UserStorageInteface;
    
    /**
     * Set the UserStorage
     * 
     * @param UserStorageInteface $storage
     *   User storage instance
     */
    public function setStorage(UserStorageInteface $storage): void;
    
}
