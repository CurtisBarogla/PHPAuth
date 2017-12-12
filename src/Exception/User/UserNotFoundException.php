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

namespace Zoe\Component\Security\Exception\User;

use Zoe\Component\Security\User\UserInterface;

/**
 * User cannot be loaded by UserLoaderInterface implementation or founded into a UserStorageInterface implementation
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserNotFoundException extends UserException
{
    
    /**
     * Exception thrown by a user loader
     * 
     * @var int
     */
    public const LOADER_ERROR_CODE = 1;
    
    /**
     * Exception thrown by a user store
     * 
     * @var int
     */
    public const STORAGE_ERROR_CODE = 2;
    
    /**
     * Genenal user error
     * 
     * @var int
     */
    public const GENERAL_ERROR_CODE = 3;
    
    /**
     * Initialize exception
     * 
     * @param UserInterface $user
     *   User which error happen
     * @param int $code
     *   Exception code (one of the const defined into UserNotFoundException class)
     */
    public function __construct(?UserInterface $user, int $code)
    {
        $this->exceptionCode = $code;
        parent::__construct($user);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Exception\User\UserException::throwMessage()
     */
    protected function throwMessage(): string
    {
        if(null !== $this->user)
            $name = $this->user->getName();
        switch ($this->exceptionCode) {
            case self::LOADER_ERROR_CODE:
                return \sprintf("This user '%s' cannot be loaded",
                    $name);
                break;
            case self::STORAGE_ERROR_CODE:
                if(!isset($name))
                    $message = "No user has been found into the store";
                else 
                    $message = \sprintf("This user '%s' has been not found into the store",
                        $name);
                return $message;
                break;
            default:
                return "User not found";       
        }
    }
    
}
