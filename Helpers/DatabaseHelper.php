<?php

namespace Helpers;

use Database\MySQLWrapper;
use Exception;

class DatabaseHelper
{
    public static function getRandomComputerPart(): array{
        $db = new MySQLWrapper();

        $stmt = $db->prepare("SELECT * FROM computer_parts ORDER BY RAND() LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $part = $result->fetch_assoc();

        if (!$part) throw new Exception('Could not find a single part in database');

        return $part;
    }

    public static function getComputerPartById(int $id): array{
        $db = new MySQLWrapper();

        $stmt = $db->prepare("SELECT * FROM computer_parts WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $part = $result->fetch_assoc();

        if (!$part) throw new Exception('Could not find a single part in database');

        return $part;
    }

    public static function getComputerPartByType(string $type, int $page, int $perpage): array{
        // page, perpageから返す部品の数とid?を決める
        $db = new MySQLWrapper();

        $stmt = $db->prepare("SELECT * FROM computer_parts WHERE type = ? LIMIT ? OFFSET ?");
        $offset = ($page -1) * $perpage;
        $stmt->bind_param("sii", $type, $perpage, $offset);
        $stmt->execute();

        $result = $stmt->get_result();
        $parts = $result->fetch_all();
        // 1page目なら perpage * (page - 1)番目からperpage分取得する

        if (!$parts) throw new Exception('Could not find parts in database');

        return $parts;
    }

    public static function getRandomComputer(): array{
        // 4部品ランダムで抽出して返す
        $db = new MySQLWrapper();
        $stmt = $db->prepare("SELECT id FROM computer_parts");
        $stmt->execute();
        $result = $stmt->get_result();
        $ids = $result->fetch_all();
        if (!$ids) throw new Exception('Could not find a single part in database');
        $computer = [];
        $PARTS_QUANTITY = 4;

        for ($i = 0; $i < $PARTS_QUANTITY; $i++){
            $id = $ids[array_rand($ids)][0];
            $part = self::getComputerPartById($id);
            array_push($computer, $part);
        }
        return $computer;

    }

    public static function getNewestComputerPart(int $page, int $perpage): array{
        // 最新のcomputer partを取得
        $db = new MySQLWrapper();

        $stmt = $db->prepare("SELECT * FROM computer_parts ORDER BY release_date desc LIMIT ? OFFSET ?");
        $offset = ($page -1) * $perpage;
        $stmt->bind_param("ii",  $perpage, $offset);
        $stmt->execute();

        $result = $stmt->get_result();
        $part = $result->fetch_all();

        if (!$part) throw new Exception('Could not find a single part in database');
        return $part;
    }

    public static function getComputerPartByPerformance(string $order, string $type): array{
        $db = new MySQLWrapper();

        $validOrders = ['asc', 'ASC', 'desc', 'DESC'];
        if (!in_array($order, $validOrders)) throw new Exception('Order is not valid');


        $sql = "SELECT * FROM computer_parts WHERE type = ? ORDER BY performance_score $order LIMIT 50";
        $stmt = $db->prepare($sql);

        $stmt->bind_param("s", $type);
        $stmt->execute();

        $result = $stmt->get_result();
        $parts = $result->fetch_all();
        if (!$parts) throw new Exception('Could not find a single part in database');

        return $parts;
    }
}