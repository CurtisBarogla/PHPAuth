<?php
//StrictType
declare(strict_types = 1);

/*
 * Ness
 * Authentication component
 *
 * Author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
 
namespace Ness\Component\Authentication\Exception;

use Ness\Component\Authentication\User\AuthenticatedUserInterface;

/**
 * When an user cannot be authenticated
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticationFailedException extends \Exception
{
    
    /**
     * User that might be authenticated, but failed to be
     * 
     * @var AuthenticatedUserInterface
     */
    private $user;
    
    /**
     * Initialize exception
     * 
     * @param AuthenticatedUserInterface $user
     *   User that might be authenticated
     * @param string $message
     *   Exception message
     * @param int $code
     *   Exception code
     * @param \Throwable|null $previous
     *   Previous exception
     */
    public function __construct(AuthenticatedUserInterface $user, string $message, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->user = $user;
    }
    
    /**
     * Get the initialized authenticated user despite the fact that this user does not passed the authentication process with success.
     * For obvious reasons, MUST never be used outside of a debugging process
     * 
     * @return AuthenticatedUserInterface
     *   Authenticated user
     */
    public function getFailedAuthenticatedUser(): AuthenticatedUserInterface
    {
        return $this->user;
    }
    
}
