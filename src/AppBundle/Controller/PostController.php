<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Form\PostType;
use AppBundle\Service\Posts\PostServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends Controller
{
    /**
     * @var PostServiceInterface
     */
    private $postService;

    public function __construct(PostServiceInterface $postService)
    {
        $this->postService = $postService;
    }

    /**
     * @Route("/create", name="post_create", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request){
        return $this->render('posts/create.html.twig',
            ['form' => $this->createForm(PostType::class)->createView()]
        );
    }

    /**
     * @Route("/create", methods={"POST"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createProcess(Request $request){
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        $this->uploadFile($form, $post);


        $this->postService->create($post);

        $this->addFlash("info", "Created post successfully");

        return $this->redirectToRoute("my_posts");
    }

    /**
     * @Route("/edit/{id}", name="post_edit", methods={"GET"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit($id){
        $post = $this->postService->getOne($id);

        if(null === $post){
            return $this->redirectToRoute("project_index");
        }

        if(!$this->isAuthorOrAdmin($post)){
            return $this->redirectToRoute("project_index");
        }

        return $this->render('posts/edit.html.twig',
            [
                'form' => $this->createForm(PostType::class)->createView(),
                'post' => $post
            ]);
    }

    /**
     * @Route("/edit/{id}", methods={"POST"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editProcess(Request $request, $id){
        $post = $this->postService->getOne($id);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        $this->uploadFile($form, $post);

        $this->postService->edit($post);

        $this->addFlash("info", "Edited post successfully!");

        return $this->redirectToRoute("my_posts");
    }

    /**
     * @Route("/delete/{id}", name="post_delete", methods={"GET"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(int $id){
        $post = $this->postService->getOne($id);

        if(null === $post){
            return $this->redirectToRoute("project_index");
        }

        if(!$this->isAuthorOrAdmin($post)){
            return $this->redirectToRoute("project_index");
        }

        return $this->render('posts/delete.html.twig',
            [
                'form' => $this->createForm(PostType::class)->createView(),
                'post' => $post
            ]);
    }

    /**
     * @Route("/delete/{id}", methods={"POST"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteProcess(Request $request, int $id){
        $post = $this->postService->getOne($id);

        $form = $this->createForm(PostType::class, $post);
        $form->remove('image');
        $form->handleRequest($request);

        $this->postService->delete($post);
        $this->addFlash("info", "Deleted post successfully!");
        return $this->redirectToRoute("my_posts");
    }

    /**
     * @Route("/posts/my_posts", name="my_posts")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllPostsByUser(){
        $posts = $this->postService->getAllPostsByAuthor();

        return $this->render(
            'posts/myPosts.html.twig',
            [
                'posts' => $posts
            ]
        );
    }

    /**
     * @param Post $post
     * @return bool
     */
    public function isAuthorOrAdmin(Post $post){
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if(!$currentUser->isAuthor($post) && !$currentUser->isAdmin()){
            return false;
        }

        return true;
    }

    /**
     * @Route("/all", name="post_all")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function allPosts(){
        $posts = $this
            ->postService
            ->getAll();

        return $this->render('posts/all.html.twig',
            ['posts' => $posts]);
    }

    /**
     * @Route("/post/{id}", name="post_view")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(int $id){
        $post = $this->postService->getOne($id);

        if(null === $post){
            $this->redirectToRoute("project_index");
        }

        $post->setViewCount($post->getViewCount() + 1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

        $comments = $this
            ->getDoctrine()
            ->getRepository(Message::class)
            ->findBy(['post' => $post], ['dateAdded' => 'DESC']);

        return $this->render('posts/view.html.twig',
                [
                    'post' => $post,
                    'comments' => $comments
                ]
            );

    }

    /**
     * @param FormInterface $form
     * @param Post $post
     */
    private function uploadFile(FormInterface $form, Post $post)
    {
        /** @var UploadedFile $file */
        $file = $form['image']->getData();
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        if ($file) {
            $file->move(
                $this->getParameter('posts_directory'),
                $fileName
            );

            $post->setImage($fileName);
        }
    }
}
