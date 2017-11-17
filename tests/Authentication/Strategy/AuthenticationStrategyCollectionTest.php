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

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection;
use Zoe\Component\Security\User\Contracts\UserInterface;

/**
 * Fill AuthenticationStrategyCollection instance with AuthenticationStrategy instance
 * 
 * @param SecurityTestCase $case
 *   Test case which the collection is testes
 * @param AuthenticationStrategyCollection $collection
 *   Collection to fill
 * @param int $count
 *   Number of strategy to set
 * @param array $results
 *   Result for each
 */
function generateCollectionWithStrategies(
    SecurityTestCase $case, 
    AuthenticationStrategyCollection $collection, 
    int $count, 
    array $results,
    ?UserInterface& $user): void
{
    $user = $case->getMockedUser(MutableUserInterface::class, "foo");
    for ($i = 0; $i < $count; $i++) {
        $strategy = $case->getMockedAuthenticationStrategy($results[$i], $user);
        $collection->add($strategy);
    }
}

/**
 * AuthenticationStrategyCollection testcase
 * 
 * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticationStrategyCollectionTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection::add()
     */
    public function testAdd(): void
    {
        $mock = $this->getMockBuilder(AuthenticationStrategyInterface::class)->setMethods(["process"])->getMock();
        
        $collection = new AuthenticationStrategyCollection();
        
        $this->assertNull($collection->add($mock));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testProcess(): void
    {
        $user = null;
        $collection = new AuthenticationStrategyCollection();
        $results = [
            AuthenticationStrategyInterface::SUCCESS,
            AuthenticationStrategyInterface::SKIP,
            AuthenticationStrategyInterface::SKIP
        ];
        generateCollectionWithStrategies($this, $collection, 3, $results, $user);
        
        $this->assertSame(AuthenticationStrategyInterface::SUCCESS, $collection->process($user, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testProcessOnFail(): void
    {
        $user = null;
        $collection = new AuthenticationStrategyCollection();
        $results = [
            AuthenticationStrategyInterface::SUCCESS,
            AuthenticationStrategyInterface::SUCCESS,
            AuthenticationStrategyInterface::FAIL
        ];
        generateCollectionWithStrategies($this, $collection, 3, $results, $user);
        
        $this->assertSame(AuthenticationStrategyInterface::FAIL, $collection->process($user, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testProcessOnFailWithShuntOnSuccess(): void
    {
        $user = null;
        $collection = new AuthenticationStrategyCollection();
        $results = [
            AuthenticationStrategyInterface::FAIL,
            AuthenticationStrategyInterface::SKIP,
            AuthenticationStrategyInterface::SUCCESS,
            AuthenticationStrategyInterface::SHUNT_ON_SUCCESS
        ];
        generateCollectionWithStrategies($this, $collection, 4, $results, $user);
        
        $this->assertSame(AuthenticationStrategyInterface::SUCCESS, $collection->process($user, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testProcessWhenNoStrategySetted(): void
    {
        $user = $this->getMockedUser(MutableUserInterface::class, "foo");
        $collection = new AuthenticationStrategyCollection();
        
        $this->assertSame(AuthenticationStrategyInterface::FAIL, $collection->process($user, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testProcessWithOnlySkip(): void
    {
        $user = null;
        $collection = new AuthenticationStrategyCollection();
        $results = [
            AuthenticationStrategyInterface::SKIP,
            AuthenticationStrategyInterface::SKIP
        ];
        generateCollectionWithStrategies($this, $collection, 2, $results, $user);
        
        $this->assertSame(AuthenticationStrategyInterface::FAIL, $collection->process($user, $user));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testExceptionOnInvalidReturnValueStrategy(): void
    {
        $user = $this->getMockedUser(MutableUserInterface::class, "foo");
        $this->expectException(\UnexpectedValueException::class);
        
        $strategy = $this->getMockedAuthenticationStrategy(8, $user);
        
        $collection = new AuthenticationStrategyCollection();
        $collection->add($strategy);
        
        $collection->process($user, $user);
    }

}
