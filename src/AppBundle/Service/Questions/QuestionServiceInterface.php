<?php
/**
 * Created by PhpStorm.
 * User: Radoslav Sapundzhiev
 * Date: 8/31/2019
 * Time: 8:46 PM
 */

namespace AppBundle\Service\Questions;


use AppBundle\Entity\Question;

interface QuestionServiceInterface
{
    public function create(Question $question, int $postId):bool;

    /**
     * @param int $postId
     * @return Question[]
     */
    public function getAllByPostId(int $postId);
    public function getOne(int $id): ?Question;
    public function delete(Question $question);
}