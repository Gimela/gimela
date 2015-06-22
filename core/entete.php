<?php
/*
Christeddy Milapie
Entete de déconnexion

Modifé le 12/06/15 par Kean de Souza
*/
$reqUserStatutById->execute(array(':id'=>$_SESSION["id"]));
$resentete = $reqUserStatutById->fetch(); // Récuperer les infos concernant l'utilisateur
$reqUserStatutById->CloseCursor();

if(isset($_POST['deconnexion'])){
echo 'Au revoir'.$_SESSION["pseudo"]; 
		session_destroy();
		header("Location: index.php?page=accueil");
		exit;	
	}
	
echo('

<form method="post" action="" enctype="multipart/form-data">
	<div id="entete_session">
<p> Session de <i style="color: rgb(50, 158, 177); font-weight: bold;"> '.$_SESSION['pseudo'].' </i> 
--- '.NOM_CLUB.' : <i style=" color: rgb(50, 158, 177); font-weight: bold;">  '.$resentete['id_club'].' </i> 
--- Statut : <a href="index.php?page='.$_SESSION['page_defaut'].'"> '.$resentete['nom_statut'].'</a> 
<input type="submit" name="deconnexion" value="Déconnexion" style="width:17%; padding: 10px 5px 10px 5px"/> </p>		
	</div>
</form>
');
?>