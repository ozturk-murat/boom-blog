<?php

namespace App\Controller\Admin;

use App\Entity\Blog;
use App\Repository\BlogRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/blog")
 */
class BlogController extends AbstractController
{

    /**
     * @Route("/", name="admin_blog")
     * @Template()
     * @param BlogRepository $blogRepository
     * @param CommentRepository $commentRepository
     * @return string[]
     */
    public function index(BlogRepository $blogRepository, CommentRepository $commentRepository)
    {
        $blogs = $blogRepository->findBy(['deletedAt'=>null]);

        $waitComments = [];
        foreach ($commentRepository->findBy(['approve'=>0,'deletedAt'=>null]) as $comment) {
            if (array_key_exists($comment->getBlog()->getId(), $waitComments)) {
                $waitComments[$comment->getBlog()->getId()]++;
            } else {
                $waitComments[$comment->getBlog()->getId()] = 1;
            }
        }

        return [
            "title" => "Blog",
            "blogs" => $blogs,
            'waitComments' => $waitComments
        ];

    }

    /**
     * @Route("/add", name="admin_blog_add")
     * @Template()
     * @param Request $request
     * @param CategoryRepository $categoryRepository
     * @param UserRepository $userRepository
     * @return string[]
     */
    public function add(Request $request, CategoryRepository $categoryRepository, UserRepository $userRepository)
    {
        if ($request->isMethod('POST')) {
            $em =  $this->getDoctrine()->getManager();

            $author = $this->getUser();
            $category = $categoryRepository->findOneBy(['id' => $request->get('category')]);

            $blog = new Blog();
            $blog->setTitle($request->get('title'));
            $blog->setAuthor($author);
            $blog->setCategory($category);
            $blog->setBody($request->get('body'));
            $blog->setImage($request->get('image'));
            $blog->setTags($request->get('tags'));
            $blog->setCratedAt(new \DateTime());
            $em->persist($blog);
            $em->flush();
            return $this->redirectToRoute("admin_blog");
        }

        $categories = $categoryRepository->findBy(['deletedAt' => null]);

        return[
            "title" => "Add Blog",
            "categories" => $categories
        ];
    }

    /**
     * @Route("/delete/{id}", name="admin_blog_delete")
     * @param $id
     * @param BlogRepository $blogRepository
     * @return string
     */
    public function delete($id, BlogRepository $blogRepository)
    {
        $blog = $blogRepository->find($id);
        if ($blog) {
            $em = $this->getDoctrine()->getManager();
            $blog->setDeletedAt(new \DateTime());
            $em->persist($blog);
            $em->flush();
        }
        return $this->redirectToRoute('admin_blog');
    }

    /**
     * @Route("/update/{id}", name="admin_blog_update")
     * @param Request $request
     * @param $id
     * @param BlogRepository $blogRepository
     * @param CategoryRepository $categoryRepository
     * @return RedirectResponse|Response
     */
    public function update(Request $request, $id, BlogRepository $blogRepository, CategoryRepository  $categoryRepository)
    {
        $blog = $blogRepository->find($id);

        if ($blog) {
            if ($request->isMethod('POST')) {
                $em =  $this->getDoctrine()->getManager();
                $category = $categoryRepository->findOneBy(['id' => $request->get('category')]);
                $blog->setTitle($request->get('title'));
                $blog->setCategory($category);
                $blog->setBody($request->get('body'));
                $blog->setImage($request->get('image'));
                $blog->setTags($request->get('tags'));
                $em->persist($blog);
                $em->flush();
            }

            $categories = $categoryRepository->findBy(['deletedAt'=>null]);
            return $this->render('admin/blog/update.html.twig', [
                'title' => 'Update',
                'blog' => $blog,
                'categories' => $categories
            ]);
        } else {
            return $this->redirectToRoute('admin_blog_add');
        }
    }

    /**
     * @Route("/{blogId}/comment/approve")
     * @param $blogId
     * @param BlogRepository $blogRepository
     * @param CommentRepository $commentRepository
     * @return array
     * @Template()
     */
    public function waitComments($blogId, BlogRepository $blogRepository, CommentRepository $commentRepository)
    {
        $comments = [];
        $blog = $blogRepository->find($blogId);
        if ($blog) {
            $comments = $commentRepository->findBy(['blog'=> $blog, 'approve'=>0,'deletedAt'=>null]);
        }

        return [
            'title' => 'Approve Comments',
            'comments' => $comments
        ];
    }

    /**
     * @Route("/comment/{commentId}/approve")
     * @param $commentId
     * @return RedirectResponse
     */
    public function approveComment($commentId, CommentRepository $commentRepository)
    {
        $comment = $commentRepository->find($commentId);
        /**@var Blog $blog*/
        $blog = $comment->getBlog();

        if ($comment) {
            $em = $this->getDoctrine()->getManager();
            $comment->setApprove(1);
            $em->persist($comment);
            $em->flush();
        }

        return $this->redirectToRoute('app_admin_blog_waitcomments', ['blogId'=> $blog->getId()]);
    }

    /**
     * @Route("/comment/{commentId}/decline")
     * @param $commentId
     * @param CommentRepository $commentRepository
     * @return RedirectResponse
     * @throws \Exception
     */
    public function declineComment($commentId, CommentRepository $commentRepository)
    {
        $comment = $commentRepository->find($commentId);
        /**@var Blog $blog*/
        $blog = $comment->getBlog();

        if ($comment) {
            $em = $this->getDoctrine()->getManager();
            $comment->setDeletedAt(new \DateTime());
            $em->persist($comment);
            $em->flush();
        }

        return $this->redirectToRoute('app_admin_blog_waitcomments', ['blogId' => $blog->getId()]);
    }

}
