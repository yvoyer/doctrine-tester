<?php
/**
 * This file is part of the doctrine-tester project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\DoctrineTester\Fixtures\Model;

/**
 * Class Blog
 *
 * @author  Yannick Voyer (http://github.com/yvoyer)
 *
 * @package Star\Component\DoctrineTester\Fixtures\Model
 */
final class Blog
{
    const CLASS_NAME = __CLASS__;

    private $id;

    private $name;

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    public function getId()
    {
        return $this->id;
    }
}
