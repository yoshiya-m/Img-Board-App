<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;
use Helpers\Settings;
use Database\MySQLWrapper;

class SearchBook extends AbstractCommand
{
    // 使用するコマンド名を設定
    protected static ?string $alias = 'book-search';

    // 引数を割り当て
    public static function getArguments(): array
    {
        return [
            (new Argument('isbn'))->description('Search by isbn')->required(false)->allowAsShort(true),
            (new Argument('title'))->description('Search by title')->required(false)->allowAsShort(true),
        ];
    }
    // データベース消去を実行する
    public function execute(): int
    {
        // データベース消去の引数を代入
        $isbn = $this->getArgumentValue('isbn');
        if ($isbn) {

            $this->searchByIsbn();
            return 0;
        }

        $title = $this->getArgumentValue('title');
        if ($title) {
            $this->searchByTitle();
            return 0;
        }

        return 0;
    }

    // isbnで検索
    private function searchByIsbn(): void
    {
        // 既に検索済みのisbnの場合、データベースから返す
        $mysqli = new MySQLWrapper();
        $isbn = $this->getArgumentValue("isbn");

        $this->log("Searching by isbn: $isbn......" . PHP_EOL);
        $stmt = $mysqli->prepare("
            SELECT * FROM Book
            WHERE id = ?;
        ");
        $stmt->bind_param("s", $isbn);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows <= 0) {
            // APIで検索してデータベースに保存
            $url = "https://openlibrary.org/isbn/" .  urlencode($isbn) . ".json";
            $ch = curl_init(); // cURLセッションを初期化
            curl_setopt($ch, CURLOPT_URL, $url); // URLをセット
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 結果を文字列として取得
            curl_setopt($ch, CURLOPT_HEADER, true); // ヘッダーを含める
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // リダイレクトをフォローする

            $response = curl_exec($ch); // リクエストを実行
            // echo "URL: " . $url . PHP_EOL;
            // echo "response: " . $response . PHP_EOL;
            
            if ($response === false) {
                $this->log( 'cURL Error: ' . curl_error($ch)); // エラーメッセージを表示
            } else {
                $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $header = substr($response, 0, $headerSize);
                $body = substr($response, $headerSize);
            
                // echo "Headers:\n" . $header; // ヘッダーを表示
                // echo "Body:\n" . $body; // ボディを表示
        
                // $this->log(curl_error($ch));
                $data = json_decode($body, true); // JSONデータを配列にデコード
                if (json_last_error() !== JSON_ERROR_NONE) {
                    echo 'JSON Decode Error: ' . json_last_error_msg() . PHP_EOL; // デコードエラーを表示
                    return;
                } else {
                    
                    // print_r($data); // データを表示
                    // データをデータベースに保存する
                    $title = $data["title"];
                    $this->log("Successfully Found a book!\n isbn: $isbn\ntitle: $title");

                    $stmt = $mysqli->prepare("
                        INSERT INTO Book (id, title)
                        VALUES (?, ?);
                    ");

                    $stmt->bind_param("ss", $isbn, $title);
                    if ($stmt->execute()) {
                        echo "New record created successfully";
                    } else {
                        echo "Error: " . $stmt->error; // エラーメッセージを表示
                    }

                }
            }

            curl_close($ch);

        } else {
            // this->log("")
            $title = $result->fetch_assoc()['title'];
            $this->log("Data found in database \nisbn: $isbn\ntitle: $title");
            

        }



        $this->log("Successfully Searched!");
    }

    // titleで検索
    private function searchByTitle(): void
    {
     // 既に検索済みのisbnの場合、データベースから返す
     $mysqli = new MySQLWrapper();
     $title = $this->getArgumentValue("title");

     $this->log("Searching by title: title......" . PHP_EOL);
     $stmt = $mysqli->prepare("
         SELECT * FROM Book
         WHERE title = ?;
     ");
     $stmt->bind_param("s", $title);
     $stmt->execute();
     $result = $stmt->get_result();

     if ($result->num_rows <= 0) {
         // APIで検索してデータベースに保存
         $url = "https://openlibrary.org/search.json?title=" . urlencode($title) . "&fields=title,isbn";
         $ch = curl_init(); // cURLセッションを初期化
         curl_setopt($ch, CURLOPT_URL, $url); // URLをセット
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 結果を文字列として取得
         curl_setopt($ch, CURLOPT_HEADER, true); // ヘッダーを含める
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // リダイレクトをフォローする

         $response = curl_exec($ch); // リクエストを実行
         // echo "URL: " . $url . PHP_EOL;
         // echo "response: " . $response . PHP_EOL;
         
         if ($response === false) {
             $this->log( 'cURL Error: ' . curl_error($ch)); // エラーメッセージを表示
         } else {
             $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
             $header = substr($response, 0, $headerSize);
             $body = substr($response, $headerSize);
         
             // echo "Headers:\n" . $header; // ヘッダーを表示
            //  echo "Body:\n" . $body; // ボディを表示
            // echo $url;
     
             // $this->log(curl_error($ch));
             $data = json_decode($body, true); // JSONデータを配列にデコード
             if (json_last_error() !== JSON_ERROR_NONE) {
                 echo 'JSON Decode Error: ' . json_last_error_msg() . PHP_EOL; // デコードエラーを表示
                 return;
             } else {
                 
                 // print_r($data); // データを表示
                 // データをデータベースに保存する
                 $isbn = $data["docs"][0]["isbn"][0];
                 $this->log("Successfully Found a book!\nisbn: $isbn\ntitle: $title");

                 $stmt = $mysqli->prepare("
                     INSERT INTO Book (id, title)
                     VALUES (?, ?);
                 ");

                 $stmt->bind_param("ss", $isbn, $title);
                 if ($stmt->execute()) {
                     echo "New record created successfully";
                 } else {
                     echo "Error: " . $stmt->error; // エラーメッセージを表示
                 }

             }
         }

         curl_close($ch);

     } else {
         // this->log("")
        //  $isbn = $result->fetch_assoc()[0];
        $isbn = $result->fetch_all()[0][0];
        $this->log("Data found in database \nisbn: $isbn\ntitle: $title");
         

     }



     $this->log("Successfully Searched!");
    }
}
