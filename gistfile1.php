<?php
/**
 * This file is property of crakmedia (http://crakmedia.com)
 *
 * PHP Version 5.4
 *
 * @copyright 2015 Crakmedia
 */

namespace Crak\Component\CustomField;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;

/**
 * Trait DoctrineTester
 *
 * @package  Crak\Component\CustomField
 * @author   Yannick Voyer <yvoyer@crakmedia.com>
 */
final class DoctrineTester
{
    /**
     * @var EntityManager
     */
    private static $entityManager;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private static $connection;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param array         $parameters
     * @param Configuration $configuration
     */
    private function __construct(array $parameters, Configuration $configuration)
    {
        $this->parameters = $parameters;
        $this->configuration = $configuration;
    }

    /**
     * @return EntityManager
     */
    private function getEntityManagerForClass()
    {
        if ((null === self::$entityManager) || (false === self::$entityManager->isOpen())) {
            self::$entityManager = EntityManager::create($this->parameters, $this->configuration);
        }

        if (null === self::$connection) {
            self::$connection = self::$entityManager->getConnection();
        }

        if (null === self::$connection || null === self::$entityManager) {
            die("The connection or entityManager were not configured correctly.\n\n\n\n");
        }

        return self::$entityManager;
    }

    /**
     * Reset the schema by clearing all the data and tables.
     */
    public function resetSchema()
    {
        $tool = new SchemaTool($this->getEntityManager());

        $tool->dropDatabase();
        $tool->createSchema($this->getEntityManager()->getMetadataFactory()->getAllMetadata());
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

        $repos = $this->getEntityManager()->getRepository($class);

        return $repos->find($id);
    }

    /**
     * Returns the entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return self::getEntityManagerForClass();
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    private function getConnection()
    {
        return $this->getEntityManager()->getConnection();
    }

    /**
     * @param array $path The paths for the xml configurations
     *
     * @return DoctrineTester
     */
    public static function sqlite(array $path = array())
    {
        $parameters = array(
            'driver' => 'pdo_sqlite',
            'in_memory' => true,
        );
        $config = Setup::createXMLMetadataConfiguration($path, true);

        return new self($parameters, $config);
    }
}
