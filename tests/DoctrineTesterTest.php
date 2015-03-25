<?php
/**
 * This file is part of the doctrine-tester project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\DoctrineTester;

use Star\Component\DoctrineTester\Fixtures\Model\Blog;

/**
 * Class DoctrineTesterTest
 *
 * @author  Yannick Voyer (http://github.com/yvoyer)
 *
 * @package Star\Component\DoctrineTester
 */
final class DoctrineTesterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DoctrineTester
     */
    private $tester;

    public function setUp()
    {
        $configPath = __DIR__ . '/Fixtures/config';
        $this->tester = DoctrineTester::sqlite(array($configPath));
        $this->tester->resetSchema();
    }

    public function test_it_should_create_the_manager()
    {
        $object = new Blog('blog');
        $this->tester->persist($object);

        $newObject = $this->tester->find($object, $object->getId());
        $this->assertNotSame($object, $newObject, 'The object should be different instances');
        $this->assertNotNull($object->getId());
    }

    public function test_it_should_refresh_the_entity_manager_when_exception_triggered()
    {
        $object = new Blog();
        $this->tester->reopenManagerWhenClosed();
        try {
            $this->tester->persist($object);
            $this->fail('A doctrine exception should be triggered.');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Doctrine\DBAL\DBALException', $e);
        }

        $object = new Blog('blog');
        $this->tester->persist($object);
        $this->assertCount(1, $this->tester->findAll($object));
    }

    public function test_it_should_throw_exception_when_integrity_constraint_found()
    {
        $this->tester->keepManagerClosedOnError();
        $this->setExpectedException('Doctrine\ORM\ORMException', 'The EntityManager is closed.');

        $object = new Blog();
        try {
            $this->tester->persist($object);
            $this->fail('A doctrine exception should be triggered.');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Doctrine\DBAL\DBALException', $e);
        }

        $object = new Blog('blog');
        $this->tester->persist($object);
    }

    /**
     * @expectedException        \Star\Component\DoctrineTester\Exception\InvalidPathException
     * @expectedExceptionMessage The path 'do-not-exists' do not exists on the filesystem.
     */
    public function test_it_should_throw_exception_when_a_path_is_not_found()
    {
        DoctrineTester::sqlite(array('do-not-exists'));
    }

    /**
     * @expectedException        \Star\Component\DoctrineTester\Exception\InvalidPathException
     * @expectedExceptionMessage At least one valid path to the config files should be given.
     */
    public function test_it_should_throw_exception_when_no_path_is_given()
    {
        DoctrineTester::sqlite(array());
    }
}
