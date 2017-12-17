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

use Zoe\Component\Security\Common\JsonSerializable;

/**
 * Authenticated user is considered valid by the authentication process.
 * At this state, user is considered immutable, except for attributes, and credentials are unsetted
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticatedUser extends User implements AuthenticatedUserInterface
{
    
    /**
     * Time which the user has been authenticated
     * 
     * @var \DateTime
     */
    private $authenticationTime;
    
    /**
     * Initialize an authenticated user.
     *
     * @param string $name
     *   User name
     * @param \DateTime $authenticationTime
     *   Time which the user has been authenticated
     * @param bool $root
     *   Root user
     * @param array $attributes
     *   Defaults user's attributes
     * @param string[] $roles
     *   Defaults user's roles
     */
    public function __construct(string $name, \DateTime $authenticationTime, bool $root = false, array $attributes = [], array $roles = []) 
    {
        $this->authenticationTime = $authenticationTime;
        parent::__construct($name, $root, $attributes, $roles);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\AuthenticatedUserInterface::authenticatedAt()
     */
    public function authenticatedAt(): \DateTimeInterface
    {
        return $this->authenticationTime;
    }

    /**
     * {@inheritDoc}
     * @see \JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize(): array
    {
        return [
            "name"          =>  $this->name,
            "attributes"    =>  $this->attributes,
            "roles"         =>  $this->roles,
            "root"          =>  $this->root,
            "timestamp"     =>  $this->authenticationTime->getTimestamp(),
            "timezone"      =>  $this->authenticationTime->getTimezone()->getName()
        ];
    }
    
    /**
     * @return AuthenticatedUser
     *   Restored AuthenticatedUser
     * 
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Common\JsonSerializable
     */
    public static function restoreFromJson($json): AuthenticatedUser
    {
        if(!\is_array($json))
            $json = \json_decode($json, true);

        $authenticationTime = (new \DateTime())
                                    ->setTimestamp($json["timestamp"])
                                    ->setTimezone(new \DateTimeZone($json["timezone"]));
        
        return new AuthenticatedUser($json["name"], $authenticationTime, $json["root"], $json["attributes"], $json["roles"]);
    }

}
