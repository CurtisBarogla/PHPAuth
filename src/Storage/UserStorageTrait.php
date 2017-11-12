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
 * Trait for interacting with a user storage instance
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
trait UserStorageTrait
{
    
    /**
     * User storage
     * 
     * @var UserStorageInteface
     */
    protected $storage;
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Storage\UserStorageAwareInterface::getStorage()
     */
    public function getStorage(): UserStorageInteface
    {
        if(null === $this->storage)
            throw new LogicException("User storage is not setted");
        
        return $this->storage;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Storage\UserStorageAwareInterface::setStorage()
     */
    public function setStorage(UserStorageInteface $storage): void
    {
        $this->storage = $storage;
    }
    
}
