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
 * General exception class for user
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
abstract class UserException extends \Exception
{
    
    /**
     * User which exception occured
     * 
     * @var UserInterface
     */
    protected $user;
    
    /**
     * Exception code
     * 
     * @var int
     */
    protected $exceptionCode = 0;
    
    /**
     * Previous throwable
     * 
     * @var \Throwable|null
     */
    protected $previous = null;
    
    /**
     * Initialize exception
     * 
     * @param UserInterface|null $user
     *   User which error happen
     */
    public function __construct(?UserInterface $user = null)
    {
        $this->user = $user;
        
        parent::__construct($this->throwMessage(), $this->exceptionCode, $this->previous);
    }
    
    /**
     * Set exception message
     * 
     * @return string
     *   Exception message
     */
    abstract protected function throwMessage(): string;
    
}
