<?php   // tests/Entity/ResultTest.php

namespace MiW16\Results\Tests\Entity;

use MiW16\Results\Entity\Result;
use MiW16\Results\Entity\User;

/**
 * Class ResultTest
 * @package MiW16\Results\Tests\Entity
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \MiW16\Results\Entity\User $user
     */
    protected $user;

    /**
     * @var \MiW16\Results\Entity\Result $result
     */
    protected $result;

    const USERNAME = 'uSeR ñ¿?Ñ';
    const POINTS = 2016;
    /**
     * @var \DateTime $time
     */
    private $time;


    /**
     * Sets up the fixture.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->user = new User();
        $this->user->setUsername(self::USERNAME);
        $this->time = new \DateTime('now');
        $this->result = new Result(
            self::POINTS,
            $this->user,
            $this->time
        );
    }

    /**
     * Implement testConstructor
     *
     * @covers \MiW16\Results\Entity\Result::__construct()
     * @covers \MiW16\Results\Entity\Result::getId()
     * @covers \MiW16\Results\Entity\Result::getResult()
     * @covers \MiW16\Results\Entity\Result::getUser()
     * @covers \MiW16\Results\Entity\Result::getTime()
     */
    public function testConstructor()
    {
        $time = new \DateTime('now');
        $this->result = new Result(0, $this->user, $time);
        self::assertEmpty($this->result->getId());
        self::assertEmpty($this->result->getResult());
        self::assertNotEmpty($this->result->getUser());
        self::assertEquals(
            $time,
            $this->result->getTime()
        );
    }

    /**
     * Implement testGet_Id().
     *
     * @covers \MiW16\Results\Entity\Result::getId
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function testGet_Id()
    {
        self::assertEmpty($this->result->getId());
    }

}
