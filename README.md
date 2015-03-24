doctrine-tester
===============

Utilitary doctrine tester

Usage
-----

The tester is just a wrapper around Doctrine Orm project, to help in testing.

    $tester = DoctrineTester::sqlite();
    $configPath = __DIR__ . '/Fixtures/config';
    $tester = DoctrineTester::sqlite(array($configPath));
    $tester->resetSchema();
    
    $entity = new SomeEntity();
    $tester->persist($entity);
    $newObject = $tester->find($entity, $entity->getId());
    $tester->findAll($entity);
    
