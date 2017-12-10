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
 * User attribute invalid or non-setted
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class InvalidUserAttributeException extends UserException
{
    
    /**
     * Invalid attribute
     * 
     * @var string
     */
    private $attribute;
    
    /**
     * Initialize exception
     * 
     * @param UserInterface $user
     *   User which error happen
     * @param string $attribute
     *   Invalid attribute name
     */
    public function __construct(UserInterface $user, string $attribute)
    {
        $this->attribute = $attribute;
        
        parent::__construct($user);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Exception\User\UserException::throwMessage()
     */
    protected function throwMessage(): string
    {
        return \sprintf("This attribute '%s' for user '%s' is invalid",
            $this->attribute,
            $this->user->getName());
    }
    
}
