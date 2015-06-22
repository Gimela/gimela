<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
session_start();
header('Content-type: text/html; charset=utf-8');
ini_set('memory_limit', '50M');
ini_set('post_max_size', '50M');
ini_set('upload_max_filesize', '50M');
ini_set('max_execution_time', 600);

require("./core/conf_gimela.php");
require("./core/sql/requetesPDO.php");
require('./core/functions.php');

//Page par defaut
if ((!isset($_SESSION['statut'] )))
	{
	$page = 'accueil';
	}
elseif (($_SESSION['statut'])==2)
	{
	$page='membre';
	}
elseif (($_SESSION['statut'])==4)
	{
	$page='gestionnaire';
	}
elseif (($_SESSION['statut'])==5)
	{
	$page='administrateur';
	}

//Si le $_GET['page'] est dans les keys du tableau $pagesOK
if(!empty($_GET['page']) && array_key_exists($_GET['page'], $pagesOK))
	{
    //Remplace la valeur par defaut par celle de l'URL
	$page = $_GET['page'];
	}
elseif(!empty($_GET['page'])) $page = $_SESSION['page_defaut'];
	
?>

<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" type="text/css" href="webroot/style/css/demo.css" />
        <link rel="stylesheet" type="text/css" href="webroot/style/css/style3.css" />
		<link rel="stylesheet" type="text/css" href="webroot/style/css/animate-custom.css" />
<title>Espace CVVFR</title>

<script type="text/javascript" src="webroot/js/jquery-1.11.3.js"></script> 
</head>
<body>

<a class="hiddenanchor" id="toregister"></a>
<a class="hiddenanchor" id="tologin"></a>
<div class="container">
	<div id="wrapper">
		<div id="login">
			<?php
			if(!empty($_SESSION)) include("./core/entete.php");		
			include($pagesOK[$page]); 
			?>
		</div>
	</div>
</div>

</body>
</html>