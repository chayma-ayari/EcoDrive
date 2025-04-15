<?php
require_once('C:/xampp/htdocs/projet/config.php');
include 'C:/xampp/htdocs/projet/model/activite.php';

class ActiviteC
{
    public function create($activite)
    {
        $sql = "INSERT INTO activite (name, event) VALUES (:name, :event)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'name' => $activite->getName(),
                'event' => $activite->getEvent()
            ]);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function read($id)
    {
        $sql = "SELECT * FROM activite where event =". $id;
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    public function search($r)
    {
        $sql = "SELECT * FROM activite 
                WHERE id LIKE :term 
                OR name LIKE :term 
                OR event LIKE :term";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['term' => "%$r%"]);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function sort($column)
    {
        $allowed = ['id', 'name', 'event'];
        if (!in_array($column, $allowed)) {
            die('Erreur: colonne invalide');
        }

        $sql = "SELECT * FROM activite ORDER BY $column";
        $db = config::getConnexion();
        try {
            return $db->query($sql)->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function findOne($id)
    {
        $sql = "SELECT * FROM activite WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function delete()
    {
            $id = $_GET['deleteactivite'];
            $sql = "DELETE FROM `activite` WHERE `id` =". $id;
            $db = config::getConnexion();
            try {
                $query = $db->prepare($sql);
                $query->execute();
            } catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
        
    }

    public function update($activite, $id)
    {
        $sql = "UPDATE activite 
                SET name = :name
                WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'name' => $activite->getName(),
                'id' => $id
            ]);
            echo "<script type='text/javascript'>
            alert('Mise à jour avec succès');
            window.location.href = 'events.php';
          </script>";        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }
}
