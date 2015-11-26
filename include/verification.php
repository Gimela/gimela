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

if (isset($_POST['inscription_form'])) {
//Recuperer les champs d'inscriptions
$_SESSION['nom_err'] = $_POST['name'];
$_SESSION['prenom_err'] =  $_POST['prenom'];
$_SESSION['pseudo_err'] =  $_POST['pseudo'];
$_SESSION['naissance_err'] =  $_POST['naissance'];
$_SESSION['prof_err'] =  $_POST['profession'];
$_SESSION['adr_err'] =  $_POST['address'];
$_SESSION['ville_err'] =  $_POST['city'];
$_SESSION['cp_err'] =  $_POST['cp'];
$_SESSION['tel_dom_err'] =  $_POST['tel_fixe'];
$_SESSION['tel_port_err'] =  $_POST['tel_mobile'];
$_SESSION['email_err'] =  $_POST['mail'];

// Verification de reseignements des inputs
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
			$message='Cet adresse e-mail est déjà utilisée !';
			header('Refresh: 0; URL="index.php?page=inscription');
			}
		else
			{
			$reqVerifPseudo->bindValue(':pseudo',$_POST['pseudo'], PDO::PARAM_STR);
			$reqVerifPseudo->execute();
			if ($reqVerifPseudo -> fetch() == TRUE)
				{
				$message='Ce pseudo est déjà utilisée !';	
				header('Refresh: 0; URL="index.php?page=inscription');
				}
			else
				{ 
					
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
						
					$message='Vous êtes désormais inscrit, veuillez contacter un gestionnaire de SIGMA qui validera votre inscription !';
					header('refresh: 0; URL=index.php?page=accueil');	
					}
				else
					{
					$message='Une erreur inopinée a empêche votre inscription, veuillez réessayez';
					header('refresh: 0; URL=index.php?page=inscription');
					}
				}
			}
	}
	else{ 
		$message='La saisie de votre mot de passe ou de votre adresse mail est erroné !';
		header('refresh: 0; URL=index.php?page=inscription');
	}
}
else{
	$message='Veuillez remplir tous les champs !';
	header('Refresh: 0; URL=index.php?page=inscription');
	}
}

MessageAlert($message);
}
?>