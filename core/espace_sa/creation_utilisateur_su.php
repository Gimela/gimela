<?php
/*
Kean de Souza et Christeddy Milapie
15/06/2015
Objectif : 
	- Créer un membre avec un pseudo et un statut
Modification le 23/06/15/
	- Erreur lors de l'execution SQL, celle-ci n'avait pas lieu.
Modif 29/06/15
	- Ajout nom, prenom et date lors de l'inscription d'un utilisateur
Modif 06/07/15
	- Ajout verification mdp et message erreur JS
*/
if(empty($_SESSION)){
	header('refresh: 1; URL=index.php');
	exit('Veuillez vous identifiez');
	}
elseif($_SESSION['id_statut'] < STATUT_ACCES_SA)
	{
	header('refresh: 1; URL=index.php?page='.$_SESSION['page_defaut'].'');
	exit('Vous n\'avez pas les droit nécessaires pour consulter ce fichier. Vous serez redirigé.');
	}
elseif($_SESSION['id_statut'] >= STATUT_ACCES_SA)
	{
	if (isset($_POST['valider'])){
		if ( $_POST['password'] == $_POST['password2'] )
			{
			$bdd=ConnectBddGestionnaire();
			$req = $bdd->prepare('INSERT INTO compte_utilisateur (id_club, date_inscription, nom, prenom, pseudo, password, id_statut) VALUES (:club,NOW(), :nom, :prenom, :pseudo, :password, :select_stat ) ');
			$req->bindValue(':club',$_POST['club'] ,PDO::PARAM_INT);
			$req->bindValue(':nom',$_POST['nom'] ,PDO::PARAM_INT);
			$req->bindValue(':prenom',$_POST['prenom'] ,PDO::PARAM_INT);
			$req->bindValue(':pseudo',$_POST['pseudo'] ,PDO::PARAM_STR);
			$req->bindValue(':password',md5($_POST['password']),PDO::PARAM_STR);
			$req->bindValue(':select_stat',$_POST['select_stat'] ,PDO::PARAM_STR);
			
			try { $req->execute(); }
			catch (PDOException $e)
				{
					$req = FALSE;
				if (($e->getCode()) == 23000)
					{
					$utilisateur_existant = "Pseudo ou ID club déjà utilisé";
					MessageAlert($utilisateur_existant);
					}
				else echo 'Message SQL : '.$e->getMessage();
				
				header('URL=index.php?page=creation_utilisateur');
				}	
		
			if( $req == FALSE ) {
				$nouveau_membre = "Une erreur lors de l'insertion dans la base a été rencontrée";
				MessageAlert($nouveau_membre) ;	 	 
				header('URL=index.php?page=creation_utilisateur');
				} 
			else { 
				$nouveau_membre = "Le nouvel utilisateur a bien été crée";
				MessageAlert($nouveau_membre) ;	 
				header('URL=index.php?page=creation_utilisateur');
				}
			}
		else 
			{
			$mdp_incorrect = "Les mots de passe ne sont pas indentiques";
			MessageAlert($mdp_incorrect);	
			header('URL=index.php?page=creation_utilisateur');
			}
		}
		
		$selectS=SelectStatut();
		echo ('
		<form method="post" id="myform"  action="#">
			<h1> Création d\'un membre </h1>
			<p> ID CLUB: <input type="text" required="required" name="club"/></p>
			<p> Pseudo : <input type="text" required="required" name="pseudo"/></p>
			<p> Nom de famille : <input type="text" required="required" name="nom"/></p>
			<p> Prénom : <input type="text" required="required" name="prenom"/></p>
			<p> Mot de passe : <input type="password" required="required" name="password"/></p>
			<p> Vérification du MDP: <input type="password" required="required" name="password2"/></p>
			<p>Statut : '.$selectS.'</p>
			<input type="submit" name="valider"/>
		</form>
		<p><i><a href="index.php?page=kjazh42tgh41">Retour au menu</a></i></p> ');

	}

?>