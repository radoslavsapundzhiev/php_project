<?php
/**
 * Created by PhpStorm.
 * User: Radoslav Sapundzhiev
 * Date: 8/31/2019
 * Time: 8:52 PM
 */

namespace AppBundle\Service\Questions;


use AppBundle\Entity\Question;
use AppBundle\Repository\QuestionRepository;
use AppBundle\Service\Posts\PostServiceInterface;
use AppBundle\Service\Users\UserServiceInterface;

class QuestionService implements QuestionServiceInterface
{
    /**
     * @var UserServiceInterface
     */
    private $userService;
    /**
     * @var QuestionRepository
     */
    private $questionRepository;
    /**
     * @var PostServiceInterface
     */
    private $postService;

    public function __construct(
        UserServiceInterface $userService,
        QuestionRepository $questionRepository,
        PostServiceInterface $postService
    )
    {
        $this->userService = $userService;
        $this->questionRepository = $questionRepository;
        $this->postService = $postService;
    }

    /**
     * @param Question $question
     * @param int $postId
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     */
    public function create(Question $question, int $postId): bool
    {
        $question
            ->setAuthor($this->userService->currentUser())
            ->setPost($this->postService->getOne($postId));

        return $this->questionRepository->insert($question);
    }

    /**
     * @param int $postId
     * @return Question[]
     */
    public function getAllByPostId(int $postId)
    {
        $post = $this->postService->getOne($postId);

        return $this
            ->questionRepository
            ->findBy(['post' => $post], ['dateAdded' => 'DESC']);
    }

    /**
     * @param $id
     * @return Question|null|object
     */
    public function getOne(int $id): ?Question
    {
        return $this->questionRepository->find($id);
    }

    /**
     * @param Question $question
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     */
    public function delete(Question $question): bool
    {
        $this->questionRepository->remove($question);
    }
}