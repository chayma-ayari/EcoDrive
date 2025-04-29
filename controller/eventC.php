<?php
require_once('C:/xampp/htdocs/projet/config.php');
include 'C:/xampp/htdocs/projet/model/event.php';

class EventC
{
    // ajouter + mailing
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
            $to_email = "ehamdi414@gmail.com";
            $subject = "📅 Nouveau Événement Créé";

            // HTML body
            $body = '
            <!DOCTYPE html>
            <html>
            <head>
            <meta charset="UTF-8">
            </head>
            <body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 30px;">
            <div style="max-width:600px;margin:auto;background:#fff;padding:20px;border-radius:10px;box-shadow:0 0 10px rgba(0,0,0,0.1);">
                <h2 style="color:#2b7a78;">🎉 Un Nouvel Événement a été Créé !</h2>
                <p>Bonjour,</p>
                <p>Nous sommes ravis de vous informer qu&apos;un nouvel événement a été ajouté à notre plateforme.</p>
                <p>Ne manquez pas l&apos;occasion de participer et de vivre une expérience unique !</p>
                <a href="http://localhost/projet/view/front/event.php" style="display:inline-block;margin-top:20px;padding:10px 20px;background-color:#3aafa9;color:#fff;text-decoration:none;border-radius:5px;">Voir l&apos;événement</a>
                <p style="margin-top:30px;font-size:12px;color:#888;">Cet e-mail a été envoyé automatiquement, merci de ne pas y répondre.</p>
            </div>
            </body>
            </html>
            ';

            // Proper headers
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: Evenements <evenements@yourdomain.com>\r\n";

            // Send mail
            if (mail($to_email, $subject, $body, $headers)) {
                echo "Email envoyé avec succès.";
            } else {
                echo "Échec de l'envoi de l'email.";
            }
            header('Location:events.php');
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    // afficher
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

    // chercher
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

    // trie
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

    // trouver un seul event avec son id 
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

    // supprimer
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

    // modifier
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
