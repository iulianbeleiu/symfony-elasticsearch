<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\PostComment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $dateNow = new \DateTimeImmutable();
        for ($i = 0; $i < 200; $i++) {
            $post = new Post();
            $post->setTitle($faker->text);
            $post->setAuthorName($faker->name);
            $post->setSlug($faker->slug);
            $post->setSummary($faker->text);
            $post->setPublishedAt($dateNow);

            $postComment = new PostComment();
            $postComment->setAuthorName($faker->name);
            $postComment->setContent($faker->realText);
            $postComment->setPost($post);

            $manager->persist($post);
        }

        $manager->flush();
    }
}
