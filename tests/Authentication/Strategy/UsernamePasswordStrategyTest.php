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

namespace ZoeTest\Component\Security\Authentication\Strategy;

use PHPUnit\Framework\TestCase;
use Zoe\Component\Internal\ReflectionTrait;
use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface;
use Zoe\Component\Security\Authentication\Strategy\UsernamePasswordStrategy;
use Zoe\Component\Security\Encoder\PasswordEncoderInterface;
use Zoe\Component\Security\User\UserInterface;

/**
 * UsernamePasswordStrategy testcase
 * 
 * @see \Zoe\Component\Security\Authentication\Strategy\UsernamePasswordStrategy
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UsernamePasswordStrategyTest extends TestCase
{
    
    use ReflectionTrait;
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\UsernamePasswordStrategy
     */
    public function testInterface(): void
    {
        $strategy = new UsernamePasswordStrategy($this->getMockedEncoder("foo", "bar", false));
        
        $this->assertInstanceOf(AuthenticationStrategyInterface::class, $strategy);
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\UsernamePasswordStrategy::process()
     */
    public function testProcess(): void
    {
        $mock = $this->getMockedEncoder("foo", "foo", true);

        $strategy = new UsernamePasswordStrategy($mock);
        
        $this->assertTrue($strategy->process($this->getMockedUser("foo"), $this->getMockedUser("foo")));
        
        $mock = $this->getMockedEncoder("foo", "bar", false);

        $strategy = new UsernamePasswordStrategy($mock);
        
        $this->assertFalse($strategy->process($this->getMockedUser("bar"), $this->getMockedUser("foo")));
        
    }
    
    /**
     * Get a mock of a password encoder
     * 
     * @param string $comparedPassword
     *   Clear password
     * @param string $encodedPassword
     *   "Encoded" password
     * @param bool $result
     *   Comparaison result
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     *   Mocked PasswordEncoderInterface
     */
    private function getMockedEncoder(
        string $comparedPassword, 
        string $encodedPassword, 
        bool $result): \PHPUnit_Framework_MockObject_MockObject
    {
        $mock = $this->getMockBuilder(PasswordEncoderInterface::class)->setMethods(["encode", "compare"])->getMock();
        $mock->method("compare")->with($comparedPassword, $encodedPassword)->will($this->returnValue($result));
        
        return $mock;
    }
    
    /**
     * Get a mocked user
     * 
     * @param string $password
     *   Password to get from getPassword method
     *   
     * @return \PHPUnit_Framework_MockObject_MockObject
     *   Mocked UserInterface
     */
    private function getMockedUser(string $password): \PHPUnit_Framework_MockObject_MockObject
    {
        $reflection = new \ReflectionClass(UserInterface::class);
        $methods = $this->reflection_extractMethods($reflection);
        
        $mock = $this->getMockBuilder(UserInterface::class)->setMethods($methods)->getMock();
        $mock->method("getPassword")->will($this->returnValue($password));
        
        return $mock;
    }
    
}
