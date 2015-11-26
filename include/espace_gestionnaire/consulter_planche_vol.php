<?php
/*
KEAN DE SOUZA	
Consultation des planches de vol

*/
if(empty($_SESSION)){
	header('refresh: 1; URL=index.php');
	exit('Veuillez vous identifiez');
	}
elseif($_SESSION['id_statut'] < STATUT_ACCES_GESTION)
	{
	header('refresh: 1; URL=index.php?page='.$_SESSION['page_defaut'].'');
	exit('Vous n\'avez pas les droit nécessaires pour consulter ce fichier. Vous serez redirigé.');
	}
elseif($_SESSION['id_statut'] >= STATUT_ACCES_GESTION)
	{
	
	if(isset($_POST['consultation_planche']) && isset($_POST['select_date_vol']) OR (!empty($_GET['date_planche_vol'])) )
		{
		if (isset($_GET['date_planche_vol'])) 
			$date_planche = $_GET['date_planche_vol'];
		elseif(isset($_POST['select_date_vol'])) 
			$date_planche = $_POST['select_date_vol'];
		else FormulairePlancheVol;
		$tab = VoirPlancheVol($date_planche);
		FormulairePlancheVol();
		echo $tab;
		}
	else
		{
		FormulairePlancheVol();
	
		}
	}

function FormulairePlancheVol() {
	$planches_disponibles = SelectPlanchesVol();
	echo ('<form method="post" action="index.php?page=planche_vol">
		<h1> Consultation des planches de vol</h1>
			<p> Voir la planche de vol du : '.$planches_disponibles.'</p>
			<input type="submit" name="consultation_planche" value="Consulter" style="width:18% ;padding:5px"/>
			<p><a href="index.php?page=menu"> Retour au menu</a></p>
			</form>');	
	}
?>