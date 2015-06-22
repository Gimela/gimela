<?php
/*-------------------------------
Kean de Souza - Projet 8


---------------------------------*/
if(empty($_SESSION)){
	header('refresh: 1; URL=index.php');
	exit('Veuillez vous identifiez');
	}
elseif($_SESSION['id_statut'] < STATUT_ACCES_GESTION)
	{
	header('refresh: 1; URL=index.php?page='.$_SESSION['page_defaut'].'');
	exit('Vous n\'avez pas les droit n&eacute;cessaires pour consulter ce fichier. Vous serez redirig&eacute;.');
	}
else
	{ 

	if (!isset($_GET['lien'])) {header('refresh: 1; URL=index.php?page=consultation_membre&lien'); exit();}
	
	$resultat=GetUsersNomPrenomIdClub();
	
	$tabCompte='<table border="1" align="center" style="font-size: 18px; text-align:center;" > <tr> <th>ID</th> <th>NOM</th> <th>Prenom</th> </tr>';
	
	foreach($resultat as $row){
		$tabCompte.='<tr> 
			<td> <a href="index.php?page=membre&amp;id='.$row['id_util'].'"> '.$row['id_club'].'</a></td>
			<td>'.$row['nom'].'</td>
			<td>'.$row['prenom'].'</td>
			</tr>';
		}
	$tabCompte.='</table>';
		
	echo ('<h1>Consultation</h1>
			<form method="post" action="#">
		
			<ul >
			   <li><a href="index.php?page=consultation_membre&amp;lien=avancee">Recherche avancée</a></li>
			   <li><a href="index.php?page=consultation_membre&amp;lien=membres">Liste des membres </a></li>
			   <li><input type="text" name="moteur" placeholder="Rechercher" style="width:38%;padding:10px"/>
			   <input type="submit" name="lancer_moteur" value="Rechercher" style="width:38%;padding:10px"/></li>
			   <p><a href="index.php?page=gestionnaire"><= Retour au menu</a> </p>
			</ul>
			</form>
			');	

	if (isset($_POST['lancer_moteur'])) {
		$requete = GetUserByNom($_POST['moteur']);
		
		if(!empty($requete)){
			
			$tabCompte='<table border="1" style="font-size: 18px; text-align:center;" align="center"><tr> <th>ID</th><th>NOM</th><th>Prenom</th> </tr>';
			
			foreach($requete as $row){
				$tabCompte.='<tr><td><a href="index.php?page=membre&amp;id='.$row['id_util'].'">'.$row['id_club'].'</a></td><td>'.$row['nom'].'</td><td>'.$row['prenom'].'</td></tr>';
				}
			$tabCompte.='</table>';
			}
		else $tabCompte ="Aucune information disponible";
		
		echo $tabCompte;	
			echo ('<p>  <a href="index.php?page=gestionnaire"> <= Revenir au menu </a></p>');
		}	
	elseif($_GET['lien']=="membres") echo $tabCompte;
		
	elseif($_GET['lien']=="avancee"){
		
		echo('<div id="avancee">
			<form method="post" action="#">
				<p><label for="sel_soldes"> <strong>Soldes</strong> </label>
				<select id="sel_soldes" name="soldes">
					<option  selected="selected"></option>
					<option value="credit">Créditeur</option>
					<option value="debit">Debiteur</option>
				</select></p>
				
				<p><label for="se_age"><strong>Age</strong> </label>
				<select id="se_age" name="age">
					<option selected="selected"></option>
					<option value="+">+ '.SEUIL_AGE.' ans</option>
					<option value="-">- '.SEUIL_AGE.' ans</option>
				</select></p>
				
				<p><label for="desinscrit" style="display: inline;"><strong>Voir les membres désinscrit</strong></label>
				 <input type="radio" id="desinscrit" name="desinscrit" value="Oui"/></p>
				
				<p> <input type="submit" name="consultation" style="width: 178px;" value="Lancer la recherche avancée"/></p>
				</form> </div>');
					
		if (isset($_POST['consultation']))
			{
			if (!empty($_POST['soldes'])) echo AfficherSoldeCompte($_POST['soldes']);
			if (!empty($_POST['age'])) echo AfficherMembreAge($_POST['age']);
			if (!empty($_POST['desinscrit'])) echo AfficherMembreDesinscrit();
			}
	echo ('<p>  <a href="index.php?page=gestionnaire"> <= Revenir au menu </a></p> </div>');		
	}
}
?>

<?php
function AfficherSoldeCompte($choix_user)
	{
	$res = InfoUsers();
		
	$tab ='<table border="1" align="center" style="font-size: 18px; text-align:center"> <tr><th>CVVFR</th><th>Nom</th> <th>Prénom</th> <th>Solde</th> </tr>';
	foreach ($res as $user)
		{	
		$somme=CalculeSoldeCompte($user['id_util']);
		switch ($choix_user)
			{
				case 'credit' : if ($somme >= 0) $tab.='<tr><td><a href="index.php?page=journal_des_mouvements&amp;id='.$user['id_util'].'">'.$user['id_club'].'</a> </td> <td>'.$user['nom'].'</td> <td>'.$user['prenom'].'</td> <td>'.$somme.'</td> </tr>' ;	break;
				
				case 'debit' : if ($somme < 0) 	$tab.='<tr><td><a href="index.php?page=journal_des_mouvements&amp;id='.$user['id_util'].'">'.$user['id_club'].'</a></td> <td>'.$user['nom'].'</td> <td>'.$user['prenom'].'</td> <td>'.$somme.'</td> </tr>' ; break;

				default : 	$tab.='<tr><td><a href="index.php?page=journal_des_mouvements&amp;id='.$user['id_util'].'>'.$user['id_club'].'"</a></td> <td>'.$user['nom'].'</td> <td>'.$user['prenom'].'</td> <td>'.$somme.'</td> </tr>' ;	break;
			}	
		}
	$tab.='</table>';
	return $tab;
	}
	
function AfficherMembreAge($signe)
	{
	$res = MembreSelonAgeSeuil($signe);
		
	$tab ='<table border="4" align="center" style="font-size: 18px;"> <tr> <th>CVVFR</th> <th>Nom</th> <th>Prénom</th> </tr>';
	foreach ($res as $user)
		{	
		$tab.='<tr><td><a href="index.php?page=membre&amp;id='.$user['id_util'].'">'.$user['id_club'].'</a></td> <td>'.$user['nom'].'</td> <td>'.$user['prenom'].'</td> </tr>' ;
		}	
	$tab.='</table>';
	return $tab;
	}

function AfficherMembreDesinscrit()
	{
		$res = MembreDesinscrit();
		
	$tab ='<table border="4" align="center" style="font-size: 18px;"> <tr> <th>CVVFR</th> <th>Nom</th> <th>Prénom</th> </tr>';
	foreach ($res as $user)
		{	
		$tab.='<tr><td><a href="index.php?page=membre&amp;id='.$user['id_util'].'">'.$user['id_club'].'</a></td> <td>'.$user['nom'].'</td> <td>'.$user['prenom'].'</td> </tr>' ;
		}	
	$tab.='</table>';
	return $tab;	
		
	}
	
?>