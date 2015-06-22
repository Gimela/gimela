<?php
echo('
	
	<form method="post" action"#">
	<i>veuillez saisir votre adresse mail:</i><br/>
	<input type="text" name="mail" require="required"/>
	<input type="submit" name"valider" value="envoyer" style="position:relative; width:12%; padding:10px; left:41%"/>
	</form>

	');
	 if(isset($_POST['mail'])&&!empty($_POST['mail'])) 
	{
		$reqVerifUserbyMail->bindValue(':mail',$_POST['mail'], PDO::PARAM_STR);
		$reqVerifUserbyMail->execute();
		
		if($reqVerifUserbyMail->fetch() == TRUE) // Si on trouve un resultat
			//requete
			$resu=$recupIdMdp=$bdd->prepare("select pseudo,password from compte_utilisateur where mail=:mail");
			$resu=$recupIdMdp->execute(array(':mail'=>$_POST['mail']));
			$resu=$recupIdMdp->fetchAll(PDO::FETCH_NAMED);
			
			foreach($resu as $row){
				
			$identifiant='<tr><td>'.$row['pseudo'].'</td></tr>';
			$mdp='<tr><td>'.$row['password'].'</td></tr>';		
			}
			
			
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
			$message  = 'identifiant:'.$identifiant.' mot de passe:'.$mdp.'';			
			
			if (mail($destinataire, $objet, $message, $headers)) // Envoi du message
			{
			echo '<p> un mail a été envoyé avec vos identifiants</p> ';
			}
			else{
			echo'<p> Cet adresse e-mail n\'est pas enregistré </p>';
			}
	}
?>