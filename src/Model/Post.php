<?php

namespace App\Model;

use DateTime;

class Post
{
    public $title;
    public $summary;
    public $authorName;
    public $slug;

    /**
     * @var array<PostComment>
     */
    public $comments = [];

    /**
     * @var DateTime|null
     */
    public $publishedAt;
}
