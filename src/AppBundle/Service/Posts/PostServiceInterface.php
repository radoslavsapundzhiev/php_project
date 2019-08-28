<?php
/**
 * Created by PhpStorm.
 * User: Radoslav Sapundzhiev
 * Date: 8/28/2019
 * Time: 10:26 PM
 */

namespace AppBundle\Service\Posts;


use AppBundle\Entity\Post;
use Doctrine\Common\Collections\ArrayCollection;

interface PostServiceInterface
{
    /**
     * @return ArrayCollection | Post[]
     */
    public function getAll();
    public function create(Post $post):bool;
    public function edit(Post $post):bool;
    public function delete(Post $post):bool;
    public function getOne(int $id): ?Post;

    /**
     * @return ArrayCollection | Post[]
     */
    public function getAllPostsByAuthor();
}