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

namespace Zoe\Component\Security\Password;

/**
 * Use native php password_* functions.
 * In this implementation salt is ignored
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NativePasswordHashing implements PasswordHashingInterface
{
    
    /**
     * Algorithm used
     * 
     * @var int
     */
    private $algorithm;
    
    /**
     * Options used by the algorithm
     * 
     * @var array
     */
    private $options;
    
    /**
     * Current php hash algorithms
     * 
     * @var array
     */
    private $algorithms = [PASSWORD_BCRYPT];
    
    /**
     * Initialize hashing process
     * 
     * @param int|null $algorithm
     *   Algorithm used (see php constants). If setted to null, will use the one defined by php by default
     * @param array|null $options
     *   Options to apply. If setted to null, will set values defined by php by default.
     *   See http://php.net/manual/fr/function.password-hash.php for further information about options
     *   
     * @see http://php.net/manual/fr/function.password-hash.php
     */
    public function __construct(?int $algorithm = null, ?array $options = null)
    {
        if(\defined("PASSWORD_ARGON2I"))
            $this->algorithms[] = PASSWORD_ARGON2I;
        
        if(null !== $algorithm && !\in_array($algorithm, $this->algorithms))
            throw new \InvalidArgumentException("Algorithm given for NativePasswordHashing is invalid");
        
        $this->algorithm = $algorithm ?? PASSWORD_DEFAULT;
        $this->options = (null !== $options) 
                            ? \array_replace(
                                $this->getDefaultsOptions($this->algorithm), 
                                $options) 
                            : $this->getDefaultsOptions($this->algorithm);
        
        // make sure not salt are pass to bcrypt
        if($this->algorithm === PASSWORD_BCRYPT && isset($this->options["salt"]))
            unset($this->options["salt"]);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Password\PasswordHashingInterface::hash()
     */
    public function hash(string $password, ?string $salt = null): string
    {
        return \password_hash($password, $this->algorithm, $this->options);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Password\PasswordHashingInterface::check()
     */
    public function check(string $password, string $hashed, ?string $salt = null): bool
    {
        return \password_verify($password, $hashed);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Password\PasswordHashingInterface::rehash()
     */
    public function rehash(string $hashed, ?string $salt = null): bool
    {
        return \password_needs_rehash($hashed, $this->algorithm, $this->options);
    }
    
    /**
     * Define defaults options considering a given one
     * 
     * @param int $algorithm
     *   Algorithm to get defaults values
     * 
     * @return array
     *   Defaults values
     */
    private function getDefaultsOptions(int $algorithm): array
    {
        switch ($algorithm) {
            case PASSWORD_BCRYPT:
                return [
                    "cost"          =>  PASSWORD_BCRYPT_DEFAULT_COST,
                ];
            case PASSWORD_ARGON2I:
                return [
                    "memory_cost"   =>  PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                    "time_cost"     =>  PASSWORD_ARGON2_DEFAULT_TIME_COST,
                    "threads"       =>  PASSWORD_ARGON2_DEFAULT_THREADS
                ];
        }
    }
    
}
