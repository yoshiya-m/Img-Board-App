<?php

use Database\DataAccess\Implementations\PostDAOImpl;
use Helpers\Settings;

$postDAO = new PostDAOImpl;
$filename = $postDAO->getImgPathById($post->getPostId()) ?? '';
if ($filename !== '') {
    $imgPath = Settings::env('BASE_URL') . '/uploads/' . $filename;
} else {
    $imgPath = '';
}

$imgEle = <<<EOD
<img src="$imgPath" class="card-img-top img-fluid" alt="...">
EOD;

$replies = $postDAO->getReplies($post, 0, $limit);
$repliesCount = count($replies);

?>
<div id="main-thread" data-post-id="<?= $post->getPostId() ?>" class="card m-5">
    <div class="d-flex justify-content-center w-100">
        <?php if ($imgPath !== '') echo $imgEle ?>
    </div>
    <div class="card-body">
        <h5 class="card-title"><?= htmlspecialchars($post->getSubject()) ?></h5>
        <p class="card-text"><?= htmlspecialchars($post->getContent()) ?></p>
        <a href="<?= Settings::env('BASE_URL') . '/reply-form?post_id=' . (string)$post->getPostId() ?>" class="btn btn-primary">返信する</a>
        <a href="<?= Settings::env('BASE_URL') . '/thread?post_id=' . (string)$post->getPostId() ?>" class="btn btn-primary">全ての返信を見る <span>（<?= $repliesCount ?></span>件）</a>
    </div>
    <ul class="list-group list-group-flush">
        <?php


        

        include __DIR__ . '/reply-cards-all.php';

        ?>
    </ul>
</div>
<!-- <script src="/js/reply.js"></script> -->