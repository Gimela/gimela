<?php

if(empty($_SESSION)){
	header('refresh: 1; URL=index.php');
	exit('Veuillez vous identifiez');
	}
elseif($_SESSION['id_statut'] < STATUT_ACCES_ADMINISTRATION )
	{
	header('refresh: 10; URL=index.php?page='.$_SESSION['page_defaut'].'');
	exit('Vous n\'avez pas le droit de consulter ce fichier. Vous serez rediriger.');
	}
elseif($_SESSION['id_statut'] >= STATUT_ACCES_ADMINISTRATION )
	{
	echo ('
	<title> Suppression de membre</title>
	</head>
	<body>
	<h1>Supprimer un membre</h1>
	<p><i>La suppression  d\' un membre à partir de cette page est définitive, celà inclut que les données seront inutilisables mais présentes.</i></p>
	<p><i>Si vous le souhaitez, vous pouvez désinscrire le membre en lui donnant le statut de désinscrit dans la page de modification des statuts des membres</i></p>
	');
	
	$reqaffichagedesmembres=$bdd->prepare('SELECT *FROM compte_utilisateur ');
	$resu=$reqaffichagedesmembres->execute();
	$resu=$reqaffichagedesmembres->fetchAll(PDO::FETCH_NAMED);
	$tabCompte='<TH>ID</TH><TH>NOM</TH><TH>Prenom</TH>';

		foreach($resu as $row){
			$tabCompte.='<tr><td><a href="index.php?page=supprimer_membre&id='.$row['id_club'].'"/a>'.$row['id_club'].'</td><td>'.$row['nom'].'</td><td>'.$row['prenom'].'</td></tr>';
				}
    
	echo('
			<table align="center" border="4">
			'.$tabCompte.'
			</table>
				<a href="index.php?page=administrateur"><=Retourner au menu</a>
			');
	}
?>
