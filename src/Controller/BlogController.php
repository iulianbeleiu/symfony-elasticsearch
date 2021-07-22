<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Query\MultiMatch;
use JoliCode\Elastically\Client;
use JoliCode\Elastically\Messenger\IndexationRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function search(Client $client, Request $request, PostRepository $postRepository): Response
    {
        $query = $request->query->get('q', '');
        $limit = $request->query->get('l', 10);

        $searchQuery = new MultiMatch();
        $searchQuery->setFields([
            'title^5',
            'title.autocomplete',
            'comments.content',
            'comments.authorName',
        ]);

        $searchQuery->setQuery($query);
        $searchQuery->setType(MultiMatch::TYPE_MOST_FIELDS);

        $foundPosts = $client->getIndex('post')->search($searchQuery);
        $results = [];
        foreach ($foundPosts->getResults() as $result) {
            /** @var Post $post */
            $post = $result->getModel();
            $results[] = [
                'title' => htmlspecialchars($post->title, ENT_COMPAT | ENT_HTML5),
                'date' => $post->publishedAt->format('M d, Y'),
                'author' => htmlspecialchars($post->authorName, ENT_COMPAT | ENT_HTML5),
                'summary' => htmlspecialchars($post->summary, ENT_COMPAT | ENT_HTML5),
            ];
        }

        return $this->render('blog/index.html.twig', [
            'blogs' => $results,
        ]);
    }

    /**
     * @Route("/blog/new")
     */
    public function newPost(
        EntityManagerInterface $entityManager,
        MessageBusInterface $bus
    ) {
        $post = new \App\Entity\Post();
        $post->setTitle('testing');
        $post->setAuthorName('testing');
        $post->setSummary('testing');
        $post->setPublishedAt(new \DateTimeImmutable());
        $post->setSlug('testing');

        $entityManager->persist($post);
        $entityManager->flush();

        $bus->dispatch(new IndexationRequest(\App\Model\Post::class, $post->getId()));

        return new Response('created', Response::HTTP_CREATED);
    }
}
