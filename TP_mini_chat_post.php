<?php
try
{
	// Connexion a la base de données, et affichage des erreurs s'il y en a
	$bdd = new PDO('mysql:host=localhost;dbname=oc_tp1_mini_chat;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}
catch(Exception $e)
{
	// S'il y a une erreur, on arrête tout
        die('Erreur : '.$e->getMessage());
}
?>

<?php
$pseudo = $_POST['pseudo'];
$message = $_POST['message'];

$req = $bdd->prepare('INSERT INTO chat(date_heure, pseudo, message) VALUES(NOW(), :pseudo, :message)');
$req->execute(array(
    'pseudo' => $pseudo,
    'message' => $message,
    ));

// Redirection vers la page php du formulaire
header('Location: TP_mini_chat.php');
?>