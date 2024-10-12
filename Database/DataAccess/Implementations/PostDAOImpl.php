<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\PostDAO;
use Database\DatabaseManager;
use Exception;
use Models\Post;

class PostDAOImpl implements PostDAO
{
    public function create(Post $post): bool|Post
    {
        if($post->getPostId() !== null) throw new \Exception('Cannot create a post with an existing ID. id: ' . $post->getPostId());
        return $this->createOrUpdate($post);
    }
    public function getById(int $postId): ?Post 
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $post = $mysqli->prepareAndFetchAll("SELECT * FROM posts WHERE post_id = ?",'i',[$postId])[0]??null;

        return $post === null ? null : $this->resultToPost($post);
    }
    public function update(Post $post): bool
    {
        if($post->getPostId() === null) throw new \Exception('Post specified has no ID.');

        $current = $this->getById($post->getPostId());
        if($current === null) throw new \Exception(sprintf("Post %s does not exist.", $post->getPostId()));

        return $this->createOrUpdate($post);
    }
    public function delete(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        return $mysqli->prepareAndExecute("DELETE FROM posts WHERE id = ?", 'i', [$id]);
    }
    public function createOrUpdate(Post $post): bool|Post
    {
        
        $mysqli = DatabaseManager::getMysqliConnection();

        $query =
        <<<SQL
            INSERT INTO posts (post_id, reply_to_id, subject, content)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            reply_to_id = VALUES(reply_to_id),
            subject = VALUES(subject),
            content = VALUES(content);
        SQL;

        $result = $mysqli->prepareAndExecute(
            $query,
            'iiss',
            [
                $post->getPostId(), // on null ID, mysql will use auto-increment.
                $post->getReplyToId(),
                $post->getSubject(),
                $post->getContent()
            ],
        );

        if(!$result) return false;

        // insert_id returns the last inserted ID.
        if($post->getPostId() === null){
            $post = self::getById($mysqli->insert_id);
            return $post;
        }

        return true;
    }

    public function getRandom(): ?post
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $post = $mysqli->prepareAndFetchAll("SELECT * FROM posts ORDER BY RAND() LIMIT 1",'',[])[0]??null;

        return $post === null ? null : $this->resultToPost($post);
    }

    public function getAllThreads(int $offset, int $limit): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM posts WHERE reply_to_id IS NULL ORDER BY updated_at desc LIMIT ?, ?";

        $results = $mysqli->prepareAndFetchAll($query, 'ii', [$offset, $limit]);

        return $results === null ? [] : $this->resultsToPosts($results);
    }
    public function getReplies(Post $postData, int $offset, int $limit): array
    {

        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM posts WHERE reply_to_id = ? ORDER BY updated_at desc LIMIT ?, ?";

        $results = $mysqli->prepareAndFetchAll($query, 'iii', [$postData->getPostId(),$offset, $limit]);

        return $results === null ? [] : $this->resultsToPosts($results);
    }

    private function resultToPost(array $data): Post{
        return new Post(
            postId: $data['post_id'],
            replyToId: $data['reply_to_id'],
            subject: $data['subject'],
            content: $data['content'],
            createdAt: $data['created_at'],
            updatedAt: $data['updated_at']
        );
    }
    private function resultsToPosts(array $results): array{
        $posts = [];

        foreach($results as $result){
            $posts[] = $this->resultToPost($result);
        }

        return $posts;
    }

    
    public function saveImageFile(Post $post, array $file): bool {
        // 画像を保存
        
        
        $fileExtension = str_replace('image/', '', $file['type']);
        // post_id と created_at 属性からhashで作成
        $filename = hash('md5', (string)$post->getPostId() . $post->getCreatedAt()) . "." . $fileExtension;
        $tmpName = $file['tmp_name'];
        $destination = 'uploads/' . $filename;
        if(!move_uploaded_file($tmpName, $destination)) throw new Exception('failed to save uploaded file.');

        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "INSERT INTO images(post_id, filepath) VALUES(?, ?)";

        $results = $mysqli->prepareAndExecute($query, 'is', [$post->getPostId(), $filename]);

        return $results;
    }

    public function getImgPathById($postId): ?string {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM images WHERE post_id = ?";

        $results = $mysqli->prepareAndFetchAll($query, 'i', [$postId]);

        return $results[0]['filepath'] ?? null;
    }

}