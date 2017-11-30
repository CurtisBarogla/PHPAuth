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

namespace ZoeTest\Component\Security\Mock;

use Zoe\Component\Security\Encoder\PasswordEncoderInterface;
use PHPUnit_Framework_MockObject_Matcher_Invocation as PhpUnitCallMethod;

class PasswordEncoderMock extends Mock
{
    
    /**
     * Initialize mocked password encoder
     * 
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     */
    public function __construct(?\ReflectionClass& $reflection = null)
    {
        if($reflection === null)
            $reflection = new \ReflectionClass(PasswordEncoderInterface::class);
        
        $method = $this->reflection_extractMethods($reflection);
        
        $this->mock = $this->getMockBuilder(PasswordEncoderInterface::class)->setMethods($method)->getMock();
    }
    
    /**
     * Initialize a new mocked password encoder
     *
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     *
     * @return PasswordEncoderInterface
     *   New mocked password encoder
     */
    public static function initMock(?\ReflectionClass& $reflection = null): PasswordEncoderMock
    {
        return new PasswordEncoderMock($reflection);
    }
    
    /**
     * Finalize this mocked password encoder
     *
     * @return PasswordEncoderInterface
     *   Mocked password encoder
     */
    public function finalizeMock(): PasswordEncoderInterface
    {
        return $this->mock;
    }
    
    /**
     * Mock encode()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $value
     *   Value to encode
     * @param string $return
     *   Encoded version of the given value returned
     *
     * @return self
     *   Fluent
     */
    public function mockEncode(PhpUnitCallMethod $count, string $value, string $return): self
    {
        $mock = function(string $method) use ($value, $return, $count): void {
            $this->mock->expects($count)->method($method)->with($value)->will($this->returnValue($return));   
        };
        
        return $this->executeMock("encode", $mock, null);
    }
    
    /**
     * Mock compare()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $clear
     *   Value to compare
     * @param string $encoded
     *   Value compared
     * @param bool $result
     *   Result of the comparaison
     *
     * @return self
     *   Fluent
     */
    public function mockCompare(PhpUnitCallMethod $count, string $clear, string $encoded, bool $result): self
    {
        $mock = function(string $method) use ($clear, $encoded, $result, $count): void {
            $this->mock->expects($count)->method($method)->with($clear, $encoded)->will($this->returnValue($result));   
        };
        
        return $this->executeMock("compare", $mock, null);
    }
    
    /**
     * {@inheritDoc}
     * @see \ZoeTest\Component\Security\Mock\Mock::getMessageForExceptionIfMocked()
     */
    protected function getMessageForExceptionIfMocked(string $method): string
    {
        return \sprintf("This method '%s' has been already mocked for this mocked password encoder",
            $method);
    }
    
}
