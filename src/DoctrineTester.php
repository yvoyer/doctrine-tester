<?php
/**
 * This file is part of the doctrine-tester project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\DoctrineTester;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Star\Component\DoctrineTester\Exception\InvalidPathException;

/**
 * Class DoctrineTester
 *
 * @author  Yannick Voyer (http://github.com/yvoyer)
 *
 * @package Star\Component\DoctrineTester
 */
final class DoctrineTester
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var bool
     */
    private $reopenManagerWhenClosed = false;

    /**
     * @param array         $parameters
     * @param Configuration $configuration
     */
    private function __construct(array $parameters, Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->connection = DriverManager::getConnection($parameters);
    }

    /**
     * Reset the schema by clearing all the data and tables.
     */
    public function resetSchema()
    {
        $tool = new SchemaTool($this->getEntityManager());

        $tool->dropDatabase();
        $tool->createSchema(
            $this->getEntityManager()
                ->getMetadataFactory()
                ->getAllMetadata()
        );
    }

    /**
     * Add the object to the database.
     *
     * @param $object
     */
    public function persist($object)
    {
        $this->getEntityManager()->persist($object);
        $this->getEntityManager()->flush();
    }

    /**
     * Search for the object, the given object will always be a different reference from the $oldObject.
     *
     * @param object $oldObject The old object reference to base our search on
     * @param mixed  $id        The id to search for
     *
     * @return object
     */
    public function find($oldObject, $id)
    {
        $class = get_class($oldObject);
        $this->getEntityManager()->clear();

        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('obj')
            ->from($class, 'obj')
            ->andWhere('obj.id = :id');

        $qb->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param $object
     *
     * @return array
     */
    public function findAll($object)
    {
        $class = get_class($object);

        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('obj')
            ->from($class, 'obj');

        return $qb->getQuery()->execute();
    }

    /**
     * Enabling this option will re-open the entity manager when an
     * error occurred.
     */
    public function reopenManagerWhenClosed()
    {
        $this->reopenManagerWhenClosed = true;
    }

    /**
     * Disables the auto opening of the entity manager when closed.
     */
    public function keepManagerClosedOnError()
    {
        $this->reopenManagerWhenClosed = false;
    }

    /**
     * @param array $paths The paths for the xml configurations
     *
     * @throws InvalidPathException
     * @return DoctrineTester
     */
    public static function sqlite(array $paths)
    {
        if (empty($paths)) {
            throw new InvalidPathException('At least one valid path to the config files should be given.');
        }

        foreach ($paths as $path) {
            if (false === file_exists($path)) {
                throw new InvalidPathException("The path '{$path}' do not exists on the filesystem.");
            }
        }

        $parameters = array(
            'driver' => 'pdo_sqlite',
            'in_memory' => true,
        );
        $config = Setup::createXMLMetadataConfiguration($paths, true);

        return new self($parameters, $config);
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        if ((null === $this->entityManager) || (false === $this->entityManager->isOpen() && $this->reopenManagerWhenClosed)) {
            $this->entityManager = EntityManager::create($this->connection, $this->configuration);
        }

        return $this->entityManager;
    }
}
