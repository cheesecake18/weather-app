<?php
require_once '../config.php';

class Searches {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getRecentSearches() {
        $sql = "SELECT city FROM searches ORDER BY date DESC LIMIT 5";
        $result = $this->db->query($sql);

        $searches = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $searches[] = $row['city'];
            }
        }
        return $searches;
    }

    public function saveSearch($city) {
        $city = $this->db->real_escape_string($city);

        // Remove duplicates, keep latest
        $this->db->query("DELETE FROM searches WHERE city='$city'");
        $sql = "INSERT INTO searches (city) VALUES ('$city')";

        if ($this->db->query($sql) === TRUE) {
            // Keep only last 5
            $this->db->query("DELETE FROM searches WHERE id NOT IN (SELECT id FROM (SELECT id FROM searches ORDER BY date DESC LIMIT 5) tmp)");
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