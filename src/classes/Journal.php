<?php
require_once '../config.php';

class Journal {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllJournals() {
        $sql = "SELECT entry, date FROM journals ORDER BY date DESC";
        $result = $this->db->query($sql);

        $journals = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $journals[] = $row;
            }
        }
        return $journals;
    }

    public function saveJournal($entry) {
        $entry = $this->db->real_escape_string($entry);
        $sql = "INSERT INTO journals (entry) VALUES ('$entry')";

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