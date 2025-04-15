<?php
require_once('C:/xampp/htdocs/projet/config.php');
include 'C:/xampp/htdocs/projet/model/event.php';

class EventC
{
    public function create($event)
    {
        $sql = "INSERT INTO `event`(`name`, `description`, `capacity`, `location`, `date`, `time`) 
                VALUES (:name, :description, :capacity, :location, :date, :time)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'name' => $event->getName(),
                'description' => $event->getDescription(),
                'capacity' => $event->getCapacity(),
                'location' => $event->getLocation(),
                'date' => $event->getDate(),
                'time' => $event->getTime(),
            ]);
            header('Location:events.php');
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function read()
    {
        $sql = "SELECT * FROM event";
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    public function search($r)
    {
        $sql = "SELECT * FROM event 
                WHERE id LIKE '%$r%' 
                OR name LIKE '%$r%' 
                OR description LIKE '%$r%' 
                OR location LIKE '%$r%'";
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    public function sort($column)
    {
        $sql = "SELECT * FROM event ORDER BY $column";
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    public function findOne($id)
    {
        $sql = "SELECT * FROM event WHERE id = '$id'";
        $db = config::getConnexion();
        try {
            $result = $db->query($sql);
            return $result->fetch();
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    public function delete()
    {
        if (isset($_GET['deleteevent'])) {
            $id = $_GET['deleteevent'];
            $sql = "DELETE FROM event WHERE id = '$id'";
            $db = config::getConnexion();
            try {
                $query = $db->prepare($sql);
                $query->execute();
                header("Location:events.php");
            } catch (Exception $e) {
                die('Erreur:' . $e->getMessage());
            }
        }
    }

    public function update($event, $id)
    {
        $sql = "UPDATE `event` 
                SET `name` = :name, `description` = :description, `capacity` = :capacity, 
                    `location` = :location, `date` = :date, `time` = :time 
                WHERE `id` = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'name' => $event->getName(),
                'description' => $event->getDescription(),
                'capacity' => $event->getCapacity(),
                'location' => $event->getLocation(),
                'date' => $event->getDate(),
                'time' => $event->getTime(),
                'id' => $id,
            ]);
            header('Location:events.php');
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }
}
