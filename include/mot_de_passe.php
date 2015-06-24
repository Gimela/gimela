<?php
/*------------------------------
Christeddy Milapie
Objectif : Génération d'un nouveau mot de passe

Modifié le 24/06/15 par Kean de Souza
	- Ajout de cryptage du mot de passe
	- Mise a jour du mdp dans la bdd-
	- Génération auto d'un nouveau mdp

-------------------------------*/
echo('
	<form method="post" action"#">
	<i>veuillez saisir votre adresse mail:</i><br/>
	<input type="text" name="mail" require="required"/>
	<input type="submit" name"valider" value="envoyer" style="position:relative; width:12%; padding:10px; left:41%"/>
	</form>
	<p><i><a href="index.php?page=accueil">Retour à l\'accueil</a></i></p>
	');
	 if(isset($_POST['mail'])&&!empty($_POST['mail'])) 
		{
		$reqVerifUserbyMail->bindValue(':mail',$_POST['mail'], PDO::PARAM_STR);
		$reqVerifUserbyMail->execute();
		if($reqVerifUserbyMail->fetch() == TRUE) // Si on trouve un resultat
			{
			
			$recupIdMdp=$bdd->prepare("SELECT id_util, pseudo, password FROM compte_utilisateur WHERE mail=:mail");
			$recupIdMdp->execute(array(':mail'=>$_POST['mail']));
			$row=$recupIdMdp->fetch(PDO::FETCH_NAMED);
			
			$mdp_rand = generer_mot_de_passe();
			echo $mdp_rand;
			$mdp_md = md5($mdp_rand);
			
			$verification_update = UpdateMDP ($row['id_util'], $mdp_md);
					
			if ($verification_update)
				{
				$identifiant=' Bonjour '.$row['pseudo'].'\n
				Vous avez fait une demande de mot de passe , cependant pour des raisons de sécurité,\n
				celui-ci a été généré automatiquement : '.$mdp_rand.' \n
				Vous pourrez le changer une fois connecter sur votre espace membre.';		
				
				$destinataire = $_POST['mail'];
				$expediteur ='gimela@localhoost.com';
				$copie = 'gimela@localhoost.com';
				$copie_cachee = 'gimela@localhoost.com';
				$objet='Indentifiant GIMELA';
				$headers  = 'MIME-Version: 1.0' . "\n"; // Version MIME
				$headers .= 'Reply-To: '.$expediteur."\n"; // Mail de reponse
				$headers .= 'From: "Nom_de_expediteur"<'.$expediteur.'>'."\n"; // Expediteur
				$headers .= 'Delivered-to: '.$destinataire."\n"; // Destinataire
				$headers .= 'Cc: '.$copie."\n"; // Copie Cc
				$headers .= 'Bcc: '.$copie_cachee."\n\n"; // Copie cachée Bcc
				$message  = $identifiant;			
				
				if (mail($destinataire, $objet, $message, $headers)) // Envoi du message
					{
					echo '<p> Un mail vous a été envoyé avec un nouveau mot de passe</p> ';
					}
				else{
					echo'<p> Cet adresse e-mail n\'est pas enregistré </p>';
					}		
				}
				else echo '<p> Echec lors de la génération d\'un nouveau mot de passe, veuillez recommencer </p>';
			}
			else echo '<p> Erreur de saisie de l\'adresse ou inexsistante dans la base </p>';
		}	
		else echo '<p> Veuillez entrer une adresse e-mail </p>';
?>

<?php

function generer_mot_de_passe($nb_caractere = 5) {
        $mot_de_passe = "";
       
        $chaine = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $longeur_chaine = strlen($chaine);
       
        for($i = 1; $i <= $nb_caractere; $i++)
        {
            $place_aleatoire = mt_rand(0,($longeur_chaine-1));
            $mot_de_passe .= $chaine[$place_aleatoire];
        }
        return $mot_de_passe;  
}

?>