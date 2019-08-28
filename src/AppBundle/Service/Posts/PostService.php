<?php
/**
 * Created by PhpStorm.
 * User: Radoslav Sapundzhiev
 * Date: 8/28/2019
 * Time: 10:40 PM
 */

namespace AppBundle\Service\Posts;


use AppBundle\Entity\Post;
use Doctrine\Common\Collections\ArrayCollection;

class PostService implements PostServiceInterface
{
    private $postRepository;
    private $userService;
    /**
     * @return ArrayCollection | Post[]
     */
    public function getAll()
    {
        // TODO: Implement getAll() method.
    }

    public function create(Post $post): bool
    {
        // TODO: Implement create() method.
    }

    public function edit(Post $post): bool
    {
        // TODO: Implement edit() method.
    }

    public function delete(Post $post): bool
    {
        // TODO: Implement delete() method.
    }

    public function getOne(int $id): ?Post
    {
        // TODO: Implement getOne() method.
    }

    /**
     * @return ArrayCollection | Post[]
     */
    public function getAllPostsByAuthor()
    {
        // TODO: Implement getAllPostsByAuthor() method.
    }
}