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

namespace Zoe\Component\Security\User\Loader;

use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\User\Contracts\UserInterface;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;

/**
 * Try to load a user over a set of UserLoaderInterface
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserLoaderCollection implements UserLoaderInterface
{
    
    /**
     * Collection identifier
     * 
     * @var string
     */
    private $identifier;
    
    /**
     * Registered user loaders
     * 
     * @var UserLoaderInterface[]
     */
    private $loaders = [];
    
    /**
     * Initialize the collection
     * 
     * @param string $identifier
     *   Identifier for the collection
     */
    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }
    
    /**
     * Add a loader to the collection
     * 
     * @param UserLoaderInterface $loader
     *   User loader
     */
    public function add(UserLoaderInterface $loader): void
    {
        $this->loaders[] = $loader; 
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Loader\UserLoaderInterface::loadUser()
     */
    public function loadUser(UserInterface $user): MutableUserInterface
    {
        $loadersTried = [];
        foreach ($this->loaders as $identifier => $loader) {
            try {
                return $loader->loadUser($user); 
            } catch (UserNotFoundException $e) {
                $loadersTried[] = $loader->identify();
                continue;
            }
        }
        
        if(!empty($this->loaders))
            $message = \sprintf("This user '%s' does not exist for the given loaders '%s'",
                $user->getName(),
                \implode(", ", $loadersTried));
        else 
            $message = \sprintf("No loader has been registered into the collection '%s'",
                $this->identifier);
        
        throw new UserNotFoundException($message);
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Loader\UserLoaderInterface::identify()
     */
    public function identify(): string
    {
        return $this->identifier;
    }

}
