<?php
/**
 * Created by PhpStorm.
 * User: Radoslav Sapundzhiev
 * Date: 8/28/2019
 * Time: 10:40 PM
 */

namespace AppBundle\Service\Posts;


use AppBundle\Entity\Post;
use AppBundle\Repository\PostRepository;
use AppBundle\Service\Users\UserServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;

class PostService implements PostServiceInterface
{
    private $postRepository;
    private $userService;

    /**
     * PostService constructor.
     * @param PostRepository $postRepository
     * @param UserServiceInterface $userService
     */
    public function __construct(PostRepository $postRepository,
                                UserServiceInterface $userService)
    {
        $this->postRepository = $postRepository;
        $this->userService = $userService;
    }

    /**
     * @return ArrayCollection | Post[]
     */
    public function getAll()
    {
        return $this->postRepository->findAll();
    }

    public function create(Post $post): bool
    {
        $author = $this->userService->currentUser();
        $post->setAuthor($author);
        $post->setViewCount(0);

        return $this->postRepository->insert($post);
    }

    public function edit(Post $post): bool
    {
        return $this->postRepository->update($post);
    }

    public function delete(Post $post): bool
    {
        return $this->postRepository->remove($post);
    }

    /**
     * @param int $id
     * @return Post|null|object
     */
    public function getOne(int $id): ?Post
    {
        return $this->postRepository->find($id);
    }

    /**
     * @return ArrayCollection | Post[]
     */
    public function getAllPostsByAuthor()
    {
        return $this
            ->postRepository
            ->findBy(
                [],
                [
                    'dateAdded' => 'DESC'
                ]
            );
    }
}