<?php

use Database\DataAccess\Implementations\PostDAOImpl;
use Helpers\DatabaseHelper;
use Helpers\ValidationHelper;
use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;
use Database\DataAccess\Implementations\ComputerPartDAOImpl;
use Types\ValueType;
use Models\ComputerPart;
use Models\Post;

return [
    '' => function (): HTTPRenderer {
        // 投稿のデータをランダムに渡す10件
        $postDAO = new PostDAOImpl;
        $posts = $postDAO->getAllThreads(0, 10);
        return new HTMLRenderer('component/home', ['posts' => $posts]);
    },
    'post-form' => function(): HTTPRenderer {
        return new HTMLRenderer('component/post-form', []);
    },
    'reply-form' => function(): HTTPRenderer {
        if (isset($_GET['post_id'])) {
            $postId = ValidationHelper::integer($_GET['post_id']);
            $postDAO = new PostDAOImpl;
            $post = $postDAO->getById($postId);
            return new HTMLRenderer('component/reply-form', ['post' => $post]);;
        }
        throw new Exception('Post id was not found.');
    },
    'thread' => function (): HTTPRenderer {
        if (isset($_GET['post_id'])) {
            $postId = ValidationHelper::integer($_GET['post_id']);
            $postDAO = new PostDAOImpl;
            $post = $postDAO->getById($postId);
            return new HTMLRenderer('component/thread', ['post' => $post]);
        }
        throw new Exception('Post id was not found.');
        // getメソッドでidを受け取る

    },
    'create' => function (): HTTPRenderer {
        // postリクエストで投稿を保存する
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Invalid request method!');
        }
        // データの検証 ファイルサイズ、テキストサイズ

        $subject = ValidationHelper::string($_POST['subject']);
        $content = ValidationHelper::string($_POST['content']);
        $replyToId = $_POST['post_id'] ?? null;
        // subject: 500文字以上は不可        
        if (strlen($subject) > 255) return new JSONRenderer(['status' => 'error', 'message' => 'subjectは255文字以下にしてください。']);
        // content: 500文字以上は不可
        if (strlen($content) > 500) return new JSONRenderer(['status' => 'error', 'message' => 'subjectは255文字以下にしてください。']);
        // contentは0文字不可
        if (strlen($content) <= 0) return new JSONRenderer(['status' => 'error', 'message' => 'コメントは空で投稿出来ません。']);
        // データのサイズと形式を検証する
        if (isset($_FILES['image'])) {
            if ($_FILES['image']['size'] > 1000000) return new JSONRenderer(['status' => 'error', 'message' => 'ファイルサイズは1MB以下にしてください。']);
            
            $validExtensions = ['png', 'jpeg', 'gif'];
            $mediaType = str_replace('image/', '', $_FILES['image']['type']);
            if (!in_array($mediaType, $validExtensions)) {
                return new JSONRenderer(['status' => 'error', 'message' => 'このファイル拡張子(' + $mediaType + ')は非対応です。']);
            }
        }


        // Postインスタンス作るなら引数必要
        $post = new Post(
            postId: null,
            replyToId: $replyToId,
            subject: $subject,
            content: $content,
            createdAt: null,
            updatedAt: null
        );
        // 投稿保存
        $postDAO = new PostDAOImpl;
        $post = $postDAO->create($post);
        if (!$post) return new JSONRenderer(['status' => 'error', 'message' => '投稿に失敗しました']);
        // filenameとpostIdを保存。画像も保存
        if (isset($_FILES['image']))$postDAO->saveImageFile($post, $_FILES['image']);


        // 成功なら$post_idを返して、フロント側でthreadページへ
        return new JSONRenderer(['status' => 'success', 'message' => '投稿しました。', 'post_id' => $post->getPostId()]);




    },
    'random/part' => function (): HTTPRenderer {
        $partDao = new ComputerPartDAOImpl();
        $part = $partDao->getRandom();

        if ($part === null) throw new Exception('No parts are available!');

        return new HTMLRenderer('component/computer-part-card', ['part' => $part]);
    },
    'parts' => function (): HTTPRenderer {
        // IDの検証
        $id = ValidationHelper::integer($_GET['id'] ?? null);

        $partDao = new ComputerPartDAOImpl();
        $part = $partDao->getById($id);

        if ($part === null) throw new Exception('Specified part was not found!');

        return new HTMLRenderer('component/computer-part-card', ['part' => $part]);
    },
    'update/part' => function (): HTMLRenderer {
        $part = null;
        $partDao = new ComputerPartDAOImpl();
        if (isset($_GET['id'])) {
            $id = ValidationHelper::integer($_GET['id']);
            $part = $partDao->getById($id);
        }
        return new HTMLRenderer('component/update-computer-part', ['part' => $part]);
    },
    'form/update/part' => function (): HTTPRenderer {
        try {
            // リクエストメソッドがPOSTかどうかをチェックします
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method!');
            }

            $required_fields = [
                'name' => ValueType::STRING,
                'type' => ValueType::STRING,
                'brand' => ValueType::STRING,
                'modelNumber' => ValueType::STRING,
                'releaseDate' => ValueType::DATE,
                'description' => ValueType::STRING,
                'performanceScore' => ValueType::INT,
                'marketPrice' => ValueType::FLOAT,
                'rsm' => ValueType::FLOAT,
                'powerConsumptionW' => ValueType::FLOAT,
                'lengthM' => ValueType::FLOAT,
                'widthM' => ValueType::FLOAT,
                'heightM' => ValueType::FLOAT,
                'lifespan' => ValueType::INT,
            ];

            $partDao = new ComputerPartDAOImpl();

            // 入力に対する単純なバリデーション。実際のシナリオでは、要件を満たす完全なバリデーションが必要になることがあります。
            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            if (isset($_POST['id'])) $validatedData['id'] = ValidationHelper::integer($_POST['id']);

            // 名前付き引数を持つ新しいComputerPartオブジェクトの作成＋アンパッキング
            $part = new ComputerPart(...$validatedData);

            error_log(json_encode($part->toArray(), JSON_PRETTY_PRINT));

            // 新しい部品情報でデータベースの更新を試みます。
            // 別の方法として、createOrUpdateを実行することもできます。
            if (isset($validatedData['id'])) $success = $partDao->update($part);
            else $success = $partDao->create($part);

            if (!$success) {
                throw new Exception('Database update failed!');
            }

            return new JSONRenderer(['status' => 'success', 'message' => 'Part updated successfully']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage()); // エラーログはPHPのログやstdoutから見ることができます。
            return new JSONRenderer(['status' => 'error', 'message' => 'Invalid data.']);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'An error occurred.']);
        }
    },
    'delete/part' => function (): HTTPRenderer {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            throw new Exception('Invalid request method!' . $_SERVER['REQUEST_METHOD']);
        }
        if (isset($_GET['id'])) {
            $id = ValidationHelper::integer($_GET['id']);
            // idのデータを消去する DBhelper?
            $partDao = new ComputerPartDAOImpl();
            $partDao->delete($id);
            if ($partDao) {
                return new JSONRenderer(['status' => 'success', 'message' => 'Part deleted successfully']);
            }
        }
        return new JSONRenderer(['status' => 'error', 'message' => 'An error occurred.']);
    },
    'parts/all' => function (): HTTPRenderer {
        $partDAO = new ComputerPartDAOImpl();
        $parts = $partDAO->getAll(0, 15);
        return new HTMLRenderer('component/computer-parts-all', ["parts" => $parts]);
    },
    'parts/type' => function (): HTTPRenderer {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            throw new Exception('Invalid request method!' . $_SERVER['REQUEST_METHOD']);
        }
        if (isset($_GET['type'])) {
            $type = ValidationHelper::string($_GET['type']);
            // idのデータを消去する DBhelper?
            $partDao = new ComputerPartDAOImpl();
            $parts = $partDao->getAllByType($type, 0, 15);
            if ($partDao) {
                return new HTMLRenderer('component/computer-parts-all', ["parts" => $parts]);
            }
        }
        return new HTMLRenderer('component/computer-parts-all', ["parts" => $parts]);
    },
    'api/random/part' => function (): HTTPRenderer {
        $part = DatabaseHelper::getRandomComputerPart();
        return new JSONRenderer(['part' => $part]);
    },
    'api/parts' => function () {
        $id = ValidationHelper::integer($_GET['id'] ?? null);
        $part = DatabaseHelper::getComputerPartById($id);
        return new JSONRenderer(['part' => $part]);
    },
    'types' => function () {
        $type = ValidationHelper::string($_GET['type'] ?? null);
        $page = ValidationHelper::integer($_GET['page'] ?? null);
        $perpage = ValidationHelper::integer($_GET['perpage'] ?? null);

        $part = DatabaseHelper::getComputerPartByType($type, $page, $perpage);
        return new JSONRenderer(['part' => $part]);
    },

    'random/computer' => function () {
        // コンピュータ生成は何の部品で構成？-> ランダムに4部品集めて返す
        $computer = DatabaseHelper::getRandomComputer();
        return new JSONRenderer(['computer' => $computer]);
    },
    'parts/newest' => function () {
        $page = ValidationHelper::integer($_GET['page'] ?? null);
        $perpage = ValidationHelper::integer($_GET['perpage'] ?? null);

        $part = DatabaseHelper::getNewestComputerPart($page, $perpage);
        return new JSONRenderer(['part' => $part]);
    },
    'parts/performance' => function () {
        $order = ValidationHelper::string($_GET['order'] ?? null);
        $type = ValidationHelper::string($_GET['type'] ?? null);

        $part = DatabaseHelper::getComputerPartByPerformance($order, $type);
        return new JSONRenderer(['part' => $part]);
    }
];
