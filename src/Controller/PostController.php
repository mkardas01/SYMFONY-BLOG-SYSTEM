<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Pusher\Pusher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', requirements: ['_locale' => 'en|pl'])]
class PostController extends AbstractController
{
    #[Route('/{_locale}', name: 'posts.index', methods: ['GET'])]
    public function index(Request $request, ManagerRegistry $doctrine, string $_locale = 'en'): Response{

        $posts = $doctrine->getRepository(Post::class)
                ->findAllPosts($request->query->getInt('page',1));

        return $this -> render('post/index.html.twig',[
            'posts' => $posts
        ]);
    }

    #[Route('/{_locale}/post/new/', name: 'posts.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Pusher $pusher): Response{

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $post = new Post();

        $post -> setTitle('Write a blog post');
        $post -> setContent('Post content');
        $post -> setUser($this->getUser());
        $post -> setCreatedAt(new \DateTimeImmutable('now'));

        $form = $this -> createForm(PostType::class, $post);
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){
            //$form -> getData();

            $entityManager -> persist($post);
            $entityManager ->flush();

            $pusher->trigger('my-channel', 'new-post-event',
                'New post: <a href="'.$this->generateUrl('posts.show',["id" => $post->getId()]).'">'.$post->getTitle().'</a>');

            return $this -> redirectToRoute('posts.index');
        }

        return $this -> render('post/new.html.twig',[
            'form' => $form
        ]);
    }

    #[Route('/{_locale}/post/{id}/', name: 'posts.show', methods: ['GET'])]
    public function show(Post $post, EntityManagerInterface $entityManager): Response
    {
        $isFollowing = $entityManager->getRepository(User::class)->isFollowing($this->getUser(), $post->getUser()) ?? false;

        $isLiked = $entityManager->getRepository(Post::class)->isLiked($this->getUser(), $post->getId()) ?? false;

        $isDisliked = $entityManager->getRepository(Post::class)->isDisliked($this->getUser(), $post->getId()) ?? false;

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'isFollowing' => $isFollowing,
            'isLiked' => $isLiked,
            'isDisliked' => $isDisliked,
        ]);
    }

    #[Route('/{_locale}/post/{id}/edit', name: 'posts.edit', methods: ['GET', 'POST'])]
    public function edit(Post $post, Request $request, ManagerRegistry $doctrine): Response{

        $this->denyAccessUnlessGranted('POST_EDIT', $post);


        $post->setUpdatedAt(new \DateTimeImmutable('now'));

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $post = $form -> getData();
            $entityManager = $doctrine -> getManager();
            $entityManager ->persist($post);
            $entityManager ->flush();

            return $this -> redirectToRoute('posts.index');
        }

        return $this -> render('post/edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/post/{id}/delete', name: 'posts.delete', methods: ['POST'])]
    public function delete(ManagerRegistry $doctrine, Post $post): Response{

        $this->denyAccessUnlessGranted('POST_DELETE', $post);

        $entityManager = $doctrine -> getManager();

        $entityManager ->remove($post);

        $entityManager ->flush();

        return  $this ->redirectToRoute('posts.index');
    }

    #[Route('/{_locale}/post/user/{id}', name: 'posts.user', methods: ['GET'])]
    public function user(Request $request, $id, ManagerRegistry $doctrine): Response{

        $posts = $doctrine->getRepository(Post::class)->
        findAllUserPosts($request->query->getInt('page',1), $id);

        return $this -> render('post/index.html.twig',[
            'posts'=>$posts,
            'user' => $posts[0]?->getUser()->getName()
        ]);
    }

    #[Route('/{_locale}/toggleFollow/{user}', name: 'toggleFollow', methods: ['GET'])]
    public function toggleFollow(EntityManagerInterface $entityManager, User $user, Request $request): Response{

        # $user is and author user

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $isFollowing = $entityManager -> getRepository(User::class) -> isFollowing($this->getUser(), $user) ?? false;

        if ($isFollowing){
            $this->getUser()->removeFollowing($user);
        }else{
            $this->getUser()->addFollowing($user);
        }

        $entityManager->flush();
        $route = $request ->headers -> get('referer');

        return $this->redirect($route);
    }
}
