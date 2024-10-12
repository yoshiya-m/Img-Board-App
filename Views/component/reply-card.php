<?php

use Database\DataAccess\Implementations\PostDAOImpl;
use Helpers\Settings;

$postDAO = new PostDAOImpl;
$filename = $postDAO->getImgPathById($reply->getPostId()) ?? '';
if ($filename !== '') {
    $imgPath = Settings::env('BASE_URL') . '/uploads/' . $filename;
} else {
    $imgPath = '';
}

$imgEle = <<<EOD
<img src="$imgPath" class="card-img-top img-fluid" alt="...">
EOD;

?>

<li class="list-group-item">
    <div class="card">
        <div class="d-flex justify-content-center w-100">
            <?php if ($imgPath !== '') echo $imgEle ?>
        </div>
        <div class="card-body">
            <h5 class="card-title"></h5>
            <h6 class="card-subtitle mb-2 text-body-secondary"><?= htmlspecialchars($reply->getSubject()) ?></h6>
            <p class="card-text"><?= htmlspecialchars($reply->getContent()) ?></p>
        </div>
    </div>
</li>