<?php
use Database\DataAccess\Implementations\PostDAOImpl;
// ながれ 返信するボタン->下２つを表示 -> 投稿時にpost_idも送る = create-threadでpost_idを送るかを判断 $isReplyで分岐する
// threadと新規投稿の組み合わせ
// post_idを渡したい formに
// 返信する押したときに投稿から取得する

// 返信の時2つcreateしてる？
include __DIR__ . '/thread.php';
include __DIR__ . '/post-form.php';


