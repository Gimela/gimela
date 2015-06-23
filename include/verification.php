<?php
/*
-----------------------------------------------------------------
Christeddy Milapie
vérification.php
Crée le 24/03/15
Dernière modification le : 23/06/15 par Kean de Souza
-----------------------------------------------------------------

	Objectif : Verifier mail non inscrit dans la base et password puis intégrer le nouvel utilisateur dans la base

Modification 23/06/15	
	- Ajout des champs obligatoires : nom, prenom, pseudo, mdp, email

*/

// Verification des input sont pleins
if(!empty($_POST['pseudo']))
{
if ($_POST['name'] && $_POST['mail'] && $_POST['mail1'] && $_POST['passe'] && $_POST['passe1'])
	{
	// Verification des mails et mot de passes identiques
	if($_POST['mail']== $_POST['mail1']&&$_POST['passe']==$_POST['passe1']) 
	{
		$reqVerifUserbyMail->bindValue(':mail',$_POST['mail'], PDO::PARAM_STR);
		$reqVerifUserbyMail->execute();
		
		if($reqVerifUserbyMail->fetch() == TRUE) // Si on trouve un resultat
			{
			echo '<p> Cet adresse e-mail est déjà utilisée ! Attendez la redirection automatique. </p>';
			header('Refresh: 3; URL="index.php?page=inscription');
			}
		else
		{
			// bindValue peut retourner une valeur fausse. --> Exceptions ?? 
			
			//Caster la date de naissance en DATE SQL
			$date=strtotime($_POST['naissance']);
			$newdate=date('Y/m/d', $date);
			
			if(!isset($_POST['sexe'])) $sexe=NULL; else $sexe = $_POST['sexe'];
			
			$reqNewUser->bindValue(':pseudo',$_POST['pseudo'] ,PDO::PARAM_STR);
			$reqNewUser->bindValue(':nom',$_POST['name'] ,PDO::PARAM_STR);	
			$reqNewUser->bindValue(':prenom',$_POST['prenom'] ,PDO::PARAM_STR);	
			$reqNewUser->bindValue(':profession', $_POST['profession'], PDO::PARAM_STR);
			$reqNewUser->bindValue(':sexe',$sexe ,PDO::PARAM_NULL);
			$reqNewUser->bindValue(':date_naissance',$newdate ,PDO::PARAM_STR);
			$reqNewUser->bindValue(':adresse',$_POST['address'], PDO::PARAM_STR);
			$reqNewUser->bindValue(':cp',$_POST['cp'] ,PDO::PARAM_STR);
			$reqNewUser->bindValue(':ville',$_POST['city'] ,PDO::PARAM_STR);
			$reqNewUser->bindValue(':tel_fixe',$_POST['tel_fixe'] ,PDO::PARAM_STR);
			$reqNewUser->bindValue(':tel_mobile',$_POST['tel_mobile'] ,PDO::PARAM_STR);
			$reqNewUser->bindValue(':mail',$_POST['mail'] ,PDO::PARAM_STR);	
			$reqNewUser->bindValue(':password', md5($_POST['passe']), PDO::PARAM_STR);
			
			
			
			// Si l'insertion se passe bien
			if ( ($reqNewUser->execute()) == TRUE )
			{
				$reqid = $bdd->prepare('SELECT id_util from compte_utilisateur WHERE mail =:mail');
				$reqid->bindValue(':mail',$_POST['mail'] ,PDO::PARAM_STR);	
				$reqid->execute();
				$resid	= $reqid->fetch();
				$reqid->CloseCursor();
				AjoutOpeSystem($resid['id_util'], 'compte_utilisateur', 'pseudo', $_POST['pseudo']);
				AjoutOpeSystem($resid['id_util'], 'compte_utilisateur', 'nom', $_POST['name']);
				AjoutOpeSystem($resid['id_util'], 'compte_utilisateur', 'prenom', $_POST['prenom']);
				AjoutOpeSystem($resid['id_util'], 'compte_utilisateur', 'profession', $_POST['profession']);
				AjoutOpeSystem($resid['id_util'], 'compte_utilisateur', 'sexe', $sexe);
				AjoutOpeSystem($resid['id_util'], 'compte_utilisateur', 'date_naissance', $newdate);
				AjoutOpeSystem($resid['id_util'], 'compte_utilisateur', 'adresse', $_POST['address']);
				AjoutOpeSystem($resid['id_util'], 'compte_utilisateur', 'cp', $_POST['cp']);
				AjoutOpeSystem($resid['id_util'], 'compte_utilisateur', 'ville', $_POST['city']);
				AjoutOpeSystem($resid['id_util'], 'compte_utilisateur', 'tel_fixe', $_POST['tel_fixe']);
				AjoutOpeSystem($resid['id_util'], 'compte_utilisateur', 'tel_mobile', $_POST['tel_mobile']);
				AjoutOpeSystem($resid['id_util'], 'compte_utilisateur', 'mail', $_POST['mail']);
				AjoutOpeSystem($resid['id_util'], 'compte_utilisateur', 'password', '*****');
				
			echo '
				<p> Vous êtes désormais inscrit, essayez de vous connecter. Attendez la redirection automatique. </p>';
			header('refresh: 3; URL=index.php?page=accueil');	
			}
		else
			{
			echo '<<p> Une erreur inopinée a emppêche votre inscription, veuillez réessayez. Attendez la redirection automatique. </p>';
			header('refresh: 3; URL=index.php?page=inscription');
			}
	}
	}
	else{ 
		echo '<p> La saisie de votre mot de passe ou de votre adresse mail est erroné ! </p>';
	}
}
else{
	echo '<p> Veuillez remplir tous les champs ! </p>';
	header('Refresh: 3; URL=index.php?page=inscription');
	}
}
?>