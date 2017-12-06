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

namespace Zoe\Component\Security\Encoder;

use Zoe\Component\Security\Exception\InvalidArgumentException;
use Zoe\Component\Security\Exception\RuntimeException;

/**
 * Use the native password_* php function to encode and compare password 
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NativePasswordEncoder implements PasswordEncoderInterface
{
    
    /**
     * Cost password_hash
     * 
     * @var int
     */
    private $cost;
    
    /**
     * Algorithm used for password hashing
     * 
     * @var int
     */
    private $algorithm;
    
    /**
     * Current valid algorithms for password_hash
     * 
     * @var array
     */
    private const VALID_ALGORITHMS = [PASSWORD_DEFAULT, PASSWORD_BCRYPT];
    
    /**
     * Initialize the encoder
     * 
     * @param int $cost
     *   Cost used for generating password
     * @param int $algorithm
     *   Algorithm used for password hashing
     * 
     * @throws InvalidArgumentException
     *   When the given algorithm is invalid
     */
    public function __construct(int $cost = PASSWORD_BCRYPT_DEFAULT_COST, int $algorithm = PASSWORD_DEFAULT)
    {
        if(!\in_array($algorithm, self::VALID_ALGORITHMS))
            throw new InvalidArgumentException("Given algorithm is invalid");
        
        if($cost <= 2)
            throw new InvalidArgumentException(\sprintf("Cost MUST be > 2. '%d' given",
                $cost));
        
        $this->cost = $cost;
        $this->algorithm = $algorithm;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Encoder\PasswordEncoderInterface::encode()
     */
    public function encode(string $password): string
    {
        if(false === $hashed = \password_hash($password, $this->algorithm, ["cost" => $this->cost]))
            // should never happen
            throw new RuntimeException("Cannot hash password");
        
        return $hashed;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Encoder\PasswordEncoderInterface::compare()
     */
    public function compare(string $comparedPassword, string $encodedPassword): bool
    {
        return \password_verify($comparedPassword, $encodedPassword);
    }
    
    /**
     * Calculate the "best" cost for generating and comparing password with password_* functions
     * 
     * @see http://php.net/manual/fr/function.password-hash.php
     * 
     * @param float $ms
     *   Time target (in ms)
     * @param int $algorithm
     *   Algorithm used for hashing
     *   
     * @return int
     *   Best cost
     */
    public static function getBestCost(float $ms = 0.05, int $algorithm = PASSWORD_DEFAULT): int
    {
        $cost = 3;
        do {
            $cost++;
            $start = microtime(true);
            password_hash("foo", $algorithm, ["cost" => $cost]);
            $end = microtime(true);
        } while (($end - $start) < $ms);
        
        return $cost;
    }

    
}
