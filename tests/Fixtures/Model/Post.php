<?php
/**
 * This file is part of the doctrine-tester project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\DoctrineTester\Fixtures\Model;

/**
 * Class Post
 *
 * @author  Yannick Voyer (http://github.com/yvoyer)
 *
 * @package Star\Component\DoctrineTester\Fixtures\Model
 */
class Post
{
    const CLASS_NAME = __CLASS__;

    private $id;
    private $blog;
    private $title;

    public function __construct(Blog $blog, $title)
    {
        $this->blog = $blog;
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getId()
    {
        return $this->id;
    }
}
