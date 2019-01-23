<?php
    class vote{
        public $creator = null;
        public $list = array();
        public $class = null;
        public $username = null;
        
        public function __construct($username, $class = null){
            // if $class in database
            $this->username = $username;
            if (self::vote_exists($class)) {
                // read from database
                $this->class   = $class;
                $sql  = 'SELECT * FROM vote;';
                $stmt = PDOc::$link->query($sql);
                foreach ($stmt as $row) if ($row['class'] == $class) {
                    $json    = $row['json'];
                    $creator = $row['creator'];
                }
                $this->list    = json_decode($json);
                $this->creator = $creator;
            } else {
                $this->creator = $username;
                $this->class   = $class;
            }
            return true;
        }
        
        public function push(){
            if (in_array($this->username, $this->list)) {
                return 'error: Element exists.';
            } else {
                $this->list[] = $this->username;
                return 'Done.';
            }
        }
        
        public function pop(){
            if (in_array($this->username, $this->list)) {
                $key = array_search($this->username, $this->list);
                unset($this->list[$key]);
                return 'Done.';
            } else {
                return 'error: Element not exists.';
            }
        }
        
        public function json(){
            return json_encode($this->list);
        }
        
        public static function vote_exists($class){
            $sql  = "SELECT * FROM vote;";
            $stmt = PDOc::$link->query($sql);
            foreach ($stmt as $row) if ($row['class'] == $class) return true;
            return false;
        }
        
        public function save(){
            if ($this->vote_exists($this->class)) {
                $sql  = 'UPDATE vote SET json=? WHERE class=?;';
                $stmt = PDOc::$link->prepare($sql);
                $stmt->execute(array($this->json(), $this->class));
            } else {
                // create a vote
                $sql  = 'INSERT INTO vote(json,class,creator) VALUES(?,?,?);';
                $stmt = PDOc::$link->prepare($sql);
                $stmt->execute(array($this->json(), $this->class, $this->creator));
            }
        }
        
        public static function find_all_votes(){
            $arr  = array();
            $sql  = 'SELECT * FROM vote;';
            $stmt = PDOc::$link->query($sql);
            foreach ($stmt as $row) {
                $arr[] = $row['class'];
            }
            return $arr;
        }
        
       public function destroy(){
           if (self::vote_exists($this->class)) {
               $sql  = "DELETE FROM vote WHERE class=?;";
               $stmt = PDOc::$link->prepare($sql);
               $res  = $stmt->execute(array($this->class));
               return 'Done.';
           } else return 'error: Vote not exists.';
       }
    }
?>