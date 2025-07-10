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
            ';
        }
        else {
            echo "Bonjour, " . $_SESSION['user']['nom_user'] . " " . $_SESSION['user']['prenom_user'] . ". Vous êtes connectés";
            echo '
            <form method="POST">
                <input type="submit" name="deconnexion" value="Se déconnecter">
            </form>
            ';
        }


        if(isset($_POST['submitConnect'])){
            $identifiant = $_POST['identifiant'];
            $password = $_POST['password'];
            
            $sql = "SELECT * FROM `users` WHERE mail_user = '$identifiant'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if($results){
                if ($password == $results[0]['password_user']){
                    $_SESSION['user'] = [
                        "id_user" => $results[0]["id_user"],
                        "nom_user" => $results[0]["nom_user"],
                        "prenom_user" => $results[0]["prenom_user"],
                        "age_user" => $results[0]["age_user"],
                        "mail_user" => $results[0]["mail_user"],
                    ];
                    header("Location: index.php");
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
    ?>
</body>
</html>