<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Question;
use AppBundle\Form\QuestionType;
use AppBundle\Service\Posts\PostServiceInterface;
use AppBundle\Service\Questions\QuestionServiceInterface;
use AppBundle\Service\Users\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends Controller
{
    private $questionService;
    private $postService;
    private $userService;

    public function __construct(
        QuestionServiceInterface $questionService,
        PostServiceInterface $postService,
        UserServiceInterface $userService
    )
    {
        $this->questionService = $questionService;
        $this->postService = $postService;
        $this->userService = $userService;
    }

    /**
     * @Route("/question/create/{id}", name="question_create", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, $id){
        $post = $this->postService->getOne($id);

        return $this->render("questions/create.html.twig",
           [
               'form' => $this->createForm(QuestionType::class)->createView(),
               'post' => $post
           ]
        );
    }

    /**
     * @Route("/question/create/{id}", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createProcess(Request $request, $id){
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        $this->addFlash("info", "Question created successfully!");
        $this->questionService->create($question, $id);
        return $this->redirectToRoute("post_view", ['id' => $id]);
    }

    /**
     * @Route("/post/{id}/questions", name="questions_view", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllByPost($id){
        $questions = $this->questionService->getAllByPostId($id);

        return $this->render("questions/all.html.twig",
            [
                'questions' => $questions
            ]
        );
    }

    /**
     * @Route("/question/{id}", name="question_view", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view($id){
        $question = $this->questionService->getOne($id);
        $post = $this->postService->getOne($question->getPost()->getId());
        return $this->render("questions/view.html.twig",
            [
                'question' => $question,
                'post' => $post
            ]
        );
    }

    /**
     * @Route("/question/delete/{id}", name="question_delete", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(int $id){
        $question = $this->questionService->getOne($id);
        return $this->render("questions/delete.html.twig",
            [
                'form' => $this->createForm(QuestionType::class)->createView(),
                'question' => $question
            ]
        );
    }

    /**
     * @Route("/question/delete/{id}", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteProcess(Request $request,int $id){
        $question = $this->questionService->getOne($id);
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        $this->questionService->delete($question);
        $this->addFlash("info", "Deleted question successfully!");
        return $this->redirectToRoute("post_all");
    }
}
