<?php
// Christeddy Milapie


if(empty($_SESSION)){
	header('refresh: 1; URL=index.php?page=accueil');
	exit('Veuillez vous identifiez');
	}
else{
	echo('<script type="text/javascript">$(document).ready(function() { document.title = \'Modification des tarifs CVVFR\';});</script>
	<h1>Modifier un tarif</h1>
	<form method="post" action="#" >');
	
	if ((isset($_POST['validation'])) && (!empty($_POST['valeur']))){
	
	if($_POST['age'] == "1"){
		$reqMiseJourTarifs-> execute(array(':val'=> $_POST['valeur'],':id'=> $_GET['id']));
		$resu = $reqModTarifs ->execute (array(':id'=>$_GET["id"]));
		$resu = $reqModTarifs ->fetch();
		}
	elseif($_POST['age']=="2"){
		$reqMiseJourTarifs2-> execute(array(':val'=> $_POST['valeur'],':id'=> $_GET['id']));
		$resu = $reqModTarifs ->execute (array(':id'=>$_GET["id"]));
		$resu = $reqModTarifs ->fetch();
		}
	else
		echo '<p>Choisir une catégorie d\'âge à modfier !</p>';
	}
	
	$resu = $reqModTarifs ->execute (array(':id'=>$_GET["id"]));
	$resu = $reqModTarifs ->fetch();
	
	echo('
		<p>Description: <strong>'.$resu["description"].'</strong></p>
		<p>Prix:</p>
		-  '.SEUIL_AGE.' ans:  <strong>'.$resu["tarif_jeune"].' €</strong><br/>
		+ '.SEUIL_AGE.' ans:  <strong>'.$resu["tarif_adulte"].' €</strong><br/><br/>
		<input type="submit" name="mod_tarif" value="Modifer les tarifs" style="width:15%;padding:10px" /> <br/><br/>');
	
	if (isset($_POST['mod_tarif'])){
		
		echo ('
		Veuillez saisir la nouvelle valeur <br/>
		<input type = "radio" name = "age" value="1"/> - '.SEUIL_AGE.' ans
		<input type = "radio" name = "age" value="2"/> + '.SEUIL_AGE.' ans<br/><br/>
		<input type = "text" name ="valeur" style="width:15%;padding:10px"/>
		<p><input type = "submit" name ="validation" value="ok" style="width:15%;padding:10px;" /> </p>
		');
		}

	 
	 echo '</form><p><a href="index.php?page=administrateur"><=Retourner au menu</a></p>
	 <p><a href="index.php?page=gestion_tarif"><=Retourner à la page de modification des tarifs</a></p>';
	}
?>