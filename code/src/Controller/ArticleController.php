<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/article/{id<\d+>}', name: 'article_view')]
    public function view(Article $article): Response
    {
        return $this->render('pages/view.html.twig', [
            'article' => $article,
        ]);
    }
    #[Route('/article/edit/{id<\d+>}', name: 'article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $base_path = $this->getParameter('kernel.project_dir').'/public/';

            $uploadedFile = $form['imageFile']->getData();
            if($uploadedFile){
                if(file_exists($base_path.$article->getImage())){ //in case we have unsplash image which is not stored in this dir
                    unlink($base_path.$article->getImage());
                }

                $path = $base_path.'uploads/article_image';
                $original_filename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $new_filename = Urlizer::urlize($original_filename).'-'.uniqid().'.'.$uploadedFile->guessExtension();

                $uploadedFile->move(
                    $path,
                    $new_filename
                );

                $article->setImage('/uploads/article_image/'.$new_filename);
            }

            $article->setUpdatedAt(new DateTimeImmutable()); // or doctrine-extensions-bundle with Trait TimestampableEntity
            $entityManager->flush();

            $this->addFlash('success', 'Successfully updated article!');

            return $this->redirectToRoute('home');
        }

        return $this->renderForm('article/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
