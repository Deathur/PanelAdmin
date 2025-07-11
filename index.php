<?php  
session_start();

$host = 'localhost';
$dbname = 'bddpaneladmin';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    // Active les erreurs PDO en exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connexion réussie !";
}
catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <?php
        
        if(!isset($_SESSION['user'])){
            echo '
            <form method="POST">
                <label>E-mail</label>
                <br>
                <input type="text" name="identifiant">
                <br>
                <label>Mot de passe</label>
                <br>
                <input type="password" name="password">
                <br>
                <br>
                <input type="submit" name="submitConnect" value="Se connecter">
            </form>
            <p><a href="?page=createAccount">Créer un compte</a><p>
            ';
            
        }
        else {
            echo "Bonjour, " . $_SESSION['user']['nom_user'] . " " . $_SESSION['user']['prenom_user'] . ". Vous êtes connectés";
            echo '
            <form method="POST">
                <input type="submit" name="deconnexion" value="Se déconnecter">
            </form>
            ';
            echo '<hr>';
            $sqlUser = "SELECT * FROM users WHERE mail_user = '" . $_SESSION['user']['mail_user'] . "'";
            $stmtUser = $pdo->prepare($sqlUser);
            $stmtUser->execute();
            $resultsUser = $stmtUser->fetchAll(PDO::FETCH_ASSOC);
            $idASupprimer = $_SESSION['user']['id_user'];
            echo "<form method='POST'>";
            echo "Nom: " . $_SESSION['user']['nom_user'] . "<br>Prenom: " . $_SESSION['user']['prenom_user'] . "<br>Age: " . $_SESSION['user']['age_user'] . "<br>Mail: " . $_SESSION['user']['mail_user'];
            echo '<br><a href="index.php?id=' . $idASupprimer . '">Modifier</a>';
            echo "</form>";
            if(isset($_POST['submitDelete'])){
                $idToDelete = $_POST['idDelete'];
                $sqlDelete = "DELETE FROM `users` WHERE id_user = '$idToDelete'";
                $stmt = $pdo->prepare($sqlDelete);
                $stmt->execute();
            }
            echo "<hr>";
        }


        if(isset($_POST['submitConnect'])){
            $identifiant = $_POST['identifiant'];
            $password = $_POST['password'];
            


            $sql = "SELECT * FROM `users` WHERE mail_user = '$identifiant'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if($results){
                if (password_verify($password, $results[0]['password_user'])){
                    $_SESSION['user'] = [
                        "id_user" => htmlspecialchars($results[0]["id_user"]),
                        "nom_user" => htmlspecialchars($results[0]["nom_user"]),
                        "prenom_user" => htmlspecialchars($results[0]["prenom_user"]),
                        "age_user" => htmlspecialchars($results[0]["age_user"]),
                        "mail_user" => htmlspecialchars($results[0]["mail_user"]),
                    ];
                    header("Location: index.php");
                    $id = $_GET['id'];
                    $sqlId = "SELECT * FROM users WHERE id_user = '$id'";
                    $stmtId = $pdo->prepare($sqlId);
                    $stmtId->execute();
            
            $resultsId = $stmtId->fetchAll(PDO::FETCH_ASSOC);
                }
                else{
                    echo "Mot de passe incorrect";
                }

            }
            else {
                echo "Utilisateur inconnu";
            }
        }
        
        if(isset($_POST['deconnexion'])){
            session_destroy();
            header("Location: index.php");
        }
    
        if(isset($_POST['submitDelete'])){
            $idToDelete = $_POST['idDelete'];
            $sqlDelete = "DELETE FROM `users` WHERE id_user = '$idToDelete'";
            $stmt = $pdo->prepare($sqlDelete);
            $stmt->execute();
        }

        if(isset($_GET['id'])){
            
            $id = $_GET['id'];
            $sqlId = "SELECT * FROM users WHERE id_user = '$id'";

            $stmtId = $pdo->prepare($sqlId);
            $stmtId->execute();
            
            $resultsId = $stmtId->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<form method="POST">
            <label for="">Nom</label>
            <input type="text" name="nomUpdate" value="' . htmlspecialchars($resultsId[0]['nom_user']) . '">
            <br>
            <label for="">Prénom</label>
            <input type="text" name="prenomUpdate" value="' . htmlspecialchars($resultsId[0]['prenom_user']) . '">
            <br>
            <label for="">Age</label>
            <input type="text" name="ageUpdate" value="' . htmlspecialchars($resultsId[0]['age_user']) . '">
            <br>
            <label for="">Mail</label>
            <input type="text" name="mailUpdate" value="' . htmlspecialchars($resultsId[0]['mail_user']) . '">
            <br>
            <label for="">Mot de passe</label>
            <input type="text" name="passwordUpdate" value="">
            <br>
            <input type="submit" name="submitUpdate" Value="Mettre à jour la BDD">
            </form>';
        }
        
        if (isset($_POST['submitUpdate'])){

            $idUpdate = $_SESSION['user']['id_user'];
            $nomUpdate = $_POST['nomUpdate'];
            $prenomUpdate = $_POST['prenomUpdate'];
            $ageUpdate = $_POST['ageUpdate'];
            $mailUpdate = $_POST['mailUpdate'];
            $passwordUpdate = $_POST['passwordUpdate'];

            $hachedPasswordUpdate = password_hash($passwordUpdate, PASSWORD_DEFAULT);
            

            $sqlUpdate = "UPDATE `users` SET `nom_user`='$nomUpdate',`prenom_user`='$prenomUpdate',`age_user`='$ageUpdate',`mail_user`='$mailUpdate',`password_user`='$hachedPasswordUpdate' WHERE `id_user` = '$idUpdate'";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->execute();
            header("Location: index.php");
        }
        
        if (isset($_GET['page']) && ($_GET['page'] == 'createAccount')){
            echo '
            <form method="POST">
                <label for="">Nom</label>
                <input type="text" name="nomCreate">
                <label for="">Prenom</label>
                <input type="text" name="prenomCreate">
                <label for="">Age</label>
                <input type="text" name="ageCreate">
                <label for="">Mail</label>
                <input type="text" name="mailCreate">
                <label for="">Mot de passe</label>
                <input type="text" name="passwordCreate">
                <input type="submit" name="submitCreate" value="Créer mon compte">
            </form>
            ';
        }

        if (isset($_POST['submitCreate'])){
            $nomCreate = $_POST['nomCreate'];
            $prenomCreate = $_POST['prenomCreate'];
            $ageCreate = $_POST['ageCreate'];
            $mailCreate = $_POST['mailCreate'];
            $passwordCreate = $_POST['passwordCreate'];

            $hachedPasswordCreate = password_hash($passwordCreate, PASSWORD_DEFAULT);

            $sqlCreate = "INSERT INTO `users`(`nom_user`, `prenom_user`, `age_user`, `mail_user`, `password_user`) VALUES ('$nomCreate','$prenomCreate','$ageCreate','$mailCreate','$hachedPasswordCreate')";
            $stmtCreate = $pdo->prepare($sqlCreate);
            $stmtCreate->execute();
        }
        
    ?>
    
</body>
</html>