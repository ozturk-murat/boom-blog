<?php

namespace App\Controller\App;

use App\Entity\Comment;
use App\Repository\BlogRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog")
 * Class BlogController
 * @package App\Controller\App
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/", name="blog")
     * @Template()
     * @param BlogRepository $blogRepository
     * @return array
     */
    public function index(BlogRepository $blogRepository)
    {
        $blogs = $blogRepository->findBy(['deletedAt'=>null]);
        return[
            "title" => "Blog",
            "user" => [
                "name" => "Murat",
                "surname" => "Ozturk",
                "username" => "muratozturk"
            ],
            "blogs" => $blogs
        ];
    }

    /**
     * @Route("/{id}")
     * @param $id
     * @param BlogRepository $blogRepository
     * @param CommentRepository $commentRepository
     * @Template()
     */
    public function detail($id, BlogRepository $blogRepository, CommentRepository $commentRepository)
    {
        $blog = $blogRepository->find($id);
        $blogsSeconds = $blogRepository->findBy(['deletedAt'=>null]);
        $comment = $commentRepository->findBy(['blog'=> $blog, 'approve'=>1,'deletedAt'=>null]);

        return [
            'blog' => $blog,
            'blogSeconds' => $blogsSeconds,
            'comments' => $comment
        ];
    }

    /**
     * @Route("/{blogId}/comment", methods={"post"})
     * @param Request $request
     * @param $blogId
     * @param BlogRepository $blogRepository
     * @param UserRepository $userRepository
     */
    public function addComment(Request $request, $blogId, BlogRepository $blogRepository, UserRepository $userRepository)
    {
        $user = $userRepository->find(1);
        $blog = $blogRepository->find($blogId);

        if ($blog) {
            $em = $this->getDoctrine()->getManager();
            $comment = new Comment();
            $comment->setBlog($blog);
            $comment->setBody($request->get('body'));
            $comment->setCratedAt(new \DateTime());
            $comment->setUser($user);
            $comment->setApprove(0);
            $em->persist($comment);
            $em->flush();
        }

        return $this->redirectToRoute('app_app_blog_detail', ['id'=>$blogId]);
    }

}
