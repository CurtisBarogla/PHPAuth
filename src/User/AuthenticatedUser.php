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

/**
 * Authenticated user is considered valid by the authentication process.
 * At this state, user is considered immutable, except for attributes, and credentials are unsetted
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticatedUser extends User implements AuthenticatedUserInterface, \JsonSerializable
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
    public function authenticatedAt(): \DateTime
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
     * Restore an AuthenticatedUser from his json representation
     * 
     * @param string|array $json
     *   Array or string json representation of the user to restore
     * 
     * @return AuthenticatedUserInterface
     *   Restored user
     */
    public static function restoreFromJson($json): AuthenticatedUserInterface
    {
        if(!\is_array($json))
            $json = \json_decode($json, true);

        $authenticationTime = (new \DateTime())
                                    ->setTimestamp($json["timestamp"])
                                    ->setTimezone(new \DateTimeZone($json["timezone"]));
        
        return new AuthenticatedUser($json["name"], $authenticationTime, $json["root"], $json["attributes"], $json["roles"]);
    }

}
