<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>TP du mini chat!</title>
</head>
<body>
	<form action="TP_mini_chat_post.php" method="post">
		<label>Pseudo : </label>
		<input type="text" name="pseudo" ><br/>

		<label>Message : </label>
		<textarea name="message" cols="30" rows="3"></textarea><br/>

		<input type="submit" value="Envoyer">
	</form>

<?php
try
{
	// Connexion a la base de données
	$bdd = new PDO('mysql:host=localhost;dbname=oc_tp1_mini_chat;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}
catch(Exception $e)
{
        die('Erreur : '.$e->getMessage());
}
?>

<?php
$chat = $bdd->query('SELECT ID, Pseudo, Message, DATE_FORMAT(date_heure, \'%d/%m/%Y à %Hh%imin%ss\') AS date_post FROM chat ORDER BY ID DESC LIMIT 10');

while ($donnees = $chat->fetch())
{
?>
 <p><?php echo ($donnees['date_post']) ?> <strong><?php echo htmlspecialchars($donnees['Pseudo']);?></strong> <?php echo htmlspecialchars($donnees['Message']);?>
</p>
<?php
}

// On libère le curseur
$chat->closeCursor();

?>

</body>
</html>