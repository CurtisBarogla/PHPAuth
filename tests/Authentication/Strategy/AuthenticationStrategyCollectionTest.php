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
use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection;

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
        $user = $this->getMockedUser("foo", "bar");
        $strat = $this->getMockedAuthenticateStrategy($user, $user);
        $collection = new AuthenticationStrategyCollection();
        
        $this->assertNull($collection->add($strat));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testProcessWithOnlySkip(): void
    {
        $skip = AuthenticationStrategyInterface::SKIP;
        $results = [$skip, $skip, $skip];
        
        $this->doTestCollection(3, $results, AuthenticationStrategyInterface::FAIL);
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testProcessWithFailAndShuntOnSucess(): void
    {
        $results = [
            AuthenticationStrategyInterface::FAIL, 
            AuthenticationStrategyInterface::SKIP, 
            AuthenticationStrategyInterface::SHUNT_ON_SUCCESS];
        
        $this->doTestCollection(3, $results, AuthenticationStrategyInterface::SUCCESS);
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testProcessWithEmptyCollection(): void
    {
        $collection = new AuthenticationStrategyCollection();
        
        $user = $this->getMockedUser("foo", "bar");
        
        $this->assertSame(AuthenticationStrategyInterface::FAIL, $collection->process($user, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testProcessWithMeltingPot(): void
    {
        $results = [
            AuthenticationStrategyInterface::SUCCESS,
            AuthenticationStrategyInterface::SKIP,
            AuthenticationStrategyInterface::SUCCESS,
            AuthenticationStrategyInterface::FAIL
        ];
        
        $this->doTestCollection(4, $results, AuthenticationStrategyInterface::FAIL);
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testExceptionOnInvalidReturnValueStrategy(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        
        $results = [
            AuthenticationStrategyInterface::SUCCESS,
            42
        ];
        
        $this->doTestCollection(2, $results, AuthenticationStrategyInterface::FAIL);
    }
    
    /**
     * Test process over a collection
     * 
     * @param int $countMockedStrategies
     *   Number of strategies to mock and set into the collection to test
     * @param array $results
     *   Result return by each strategy
     * @param int $collectionResult
     *   Result expected given the results for the given mocked strategies
     */
    private function doTestCollection(int $countMockedStrategies, array $results, int $collectionResult): void
    {
        $user = $this->getMockedUser("foo", "bar");
        
        $strategies = [];
        $collection = new AuthenticationStrategyCollection();
        for ($i = 0; $i < $countMockedStrategies; $i++) {
            $strategy = $this->getMockedAuthenticateStrategy($user, $user, $results[$i]);
            $collection->add($strategy);
        }
        
        $this->assertSame($collectionResult, $collection->process($user, $user));
    }
    
}
