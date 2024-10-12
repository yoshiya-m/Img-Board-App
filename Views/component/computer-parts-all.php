
<h1>sample</h1>
<?php
foreach ($parts as $part) {
    ob_start();
    include __DIR__ . '/computer-part-card.php';
    $html = ob_get_clean();
    echo $html;

}
