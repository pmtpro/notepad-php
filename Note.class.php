<?php

    class Note
    {
        private PDO $db;

        public int   $id   = 0;
        public mixed $data = null;

        public function __construct($noteId)
        {
            // connect database
            try {
                $this->db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE                  => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES         => false,
                    PDO::ATTR_DEFAULT_FETCH_MODE       => PDO::FETCH_OBJ,
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
                ]);
            } catch (PDOException $e) {
                echo '<h2>MySQL ERROR: ' . $e->getCode() . '</h2>';
                exit($e->getMessage());
            }

            // set node id and data
            if ($this->very($noteId)) {
                $this->id   = $this->decodeId($noteId);
                $this->data = $this->get();
            }
        }

        function _go($url)
        {
            header('Location: ' . $url);
            exit;
        }

        // very note id
        public function very($id): bool
        {
            // check lenght
            if (strlen($id) > 64) {
                return false;
            }

            // check name encode
            if ($this->decodeId($id) == 0) {
                return false;
            }

            return true;
        }

        function encodeId($name): string
        {
            return strrev(base64_encode($name));
        }

        function decodeId($name): int
        {
            return (int)base64_decode(strrev($name), true);
        }

        public function new()
        {
            $this->db->prepare("INSERT INTO `notes` SET `text` = ''")->execute();
            $noteId = $this->encodeId($this->db->lastInsertId());

            // redirect
            $this->_go(BASE_URL . '/' . $noteId);
        }

        public function get()
        {
            $stmt = $this->db->prepare("SELECT * FROM `notes` WHERE `id` = ?");
            $stmt->execute([$this->id]);

            return $stmt->fetch();
        }


        function update($text)
        {
            $stmt = $this->db->prepare("
                UPDATE `notes` SET
                `text`        = ?,
                `date_update` = ?
                WHERE `id`    = ?
            ");
            $stmt->execute([
                $text,
                date('Y-m-d H:i:s'),
                $this->id
            ]);
        }

        /** @noinspection PhpUnused */
        function delete()
        {
            $this->db->exec("DELETE FROM `notes` WHERE `id` = '$this->id'");
        }
    }
