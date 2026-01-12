<?php
require_once '../config.php';

class Points {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getPointsAndStreak() {
        $sql = "SELECT points, streak FROM points WHERE id=1";
        $result = $this->db->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return ['points' => $row['points'], 'streak' => $row['streak']];
        } else {
            return ['points' => 0, 'streak' => 1];
        }
    }

    public function savePointsAndStreak($points, $streak) {
        $points = intval($points);
        $streak = intval($streak);
        $sql = "INSERT INTO points (id, points, streak) VALUES (1, $points, $streak) ON DUPLICATE KEY UPDATE points=$points, streak=$streak";

        if ($this->db->query($sql) === TRUE) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => $this->db->getConnection()->error];
        }
    }

    public function __destruct() {
        $this->db->close();
    }
}
?>