<?php

if(isset($_SESSION['id']))
	{
	echo ('</p>Vous êtes déjà inscrit, veuillez attendre la redirection automatique</p>');	
	header('refresh: 1; URL=index.php?page='.$_SESSION['page_defaut'].'');
	}
else
{
	
include('/include/verification.php');

/*
$_SESSION['nom_err'] = $_POST['name'];
$_SESSION['prenom_err'] =  $_POST['prenom'];
$_SESSION['pseudo_err'] =  $_POST['pseudo'];
$_SESSION['naissance_err'] =  $_POST['naissance'];
$_SESSION['prof_err'] =  $_POST['profession'];
$_SESSION['adr_err'] =  $_POST['address'];
$_SESSION['ville_err'] =  $_POST['city'];
$_SESSION['cp_err'] =  $_POST['cp'];
$_SESSION['tel_dom_err'] =  $_POST['tel_fixe'];
$_SESSION['tel_port_err'] =  $_POST['tel_mob'];
$_SESSION['email_err'] =  $_POST['mail'];
*/

$prenom_err = (isset($_SESSION['prenom_err'])) ? htmlspecialchars($_SESSION['prenom_err']) : NULL;
$nom_err = (isset($_SESSION['nom_err'])) ? htmlspecialchars($_SESSION['nom_err']) : NULL;
$pseudo_err = (isset($_SESSION['pseudo_err'])) ? htmlspecialchars($_SESSION['pseudo_err']) : NULL;
$naissance_err = (isset($_SESSION['naissance_err'])) ? htmlspecialchars($_SESSION['naissance_err']) : NULL;
$prof_err = (isset($_SESSION['prof_err'])) ? htmlspecialchars($_SESSION['prof_err']) : NULL;
$adr_err = (isset($_SESSION['adr_err'])) ? htmlspecialchars($_SESSION['adr_err']) : NULL;
$ville_err = (isset($_SESSION['ville_err'])) ? htmlspecialchars($_SESSION['ville_err']) : NULL;
$cp_err = (isset($_SESSION['cp_err'])) ? htmlspecialchars($_SESSION['cp_err']) : NULL;
$tel_dom_err = (isset($_SESSION['tel_dom_err'])) ? htmlspecialchars($_SESSION['tel_dom_err']) : NULL;
$tel_port_err = (isset($_SESSION['tel_port_err'])) ? htmlspecialchars($_SESSION['tel_port_err']) : NULL;
$email_err = (isset($_SESSION['email_err'])) ? htmlspecialchars($_SESSION['email_err']) : NULL;
	
echo ('	<div class="animate form">
		<form method="post" action="#" > 
			<div id="form_inscription">
                                <h1> Inscription </h1> 
                                <p> 
                                    <label for="prenom" class="name" data-icon="u">Prénom *</label>
                                    <input id="prenom" name="prenom" required="required" type="text" value="'.$prenom_err.'" placeholder="ex.. Martin" />
                                </p>
								<p> 
                                    <label for="name" class="surname" data-icon="u">Nom *</label>
                                    <input id="name" name="name" required="required" type="text" value="'.$nom_err.'" placeholder="ex.. Dupont" />
                                </p>
								<p> 
                                    <label for="pseudo" class="uname" data-icon="u">Pseudo *</label>
                                    <input id="pseudo" name="pseudo" required="required" type="text" value="'.$pseudo_err.'" placeholder="ex. mdupont" />
                                </p>
								<p> 
                                    <label for="naissance" class="date_naissance" data-icon="u">Date de naissance</label>
                                    <input id="naissance" name="naissance" type="date" value="'.$naissance_err.'" placeholder="sous la forme jj/mm/aaaa" min="1920-01-01" min="2050-01-01"/>
                                </p>
								<p> 
                                    <label for="profession" class="job" data-icon="">Profession</label>
                                    <input id="profession" name="profession" type="text" value="'.$prof_err.'" placeholder="ex. ingénieur en réalité augmenté"/>
                                </p>
								<p><label for="sexe" class="genre">Sexe :</label>
									Masculin <input name="sexe" type="radio"   value="Masculin" />
									Feminin <input  name="sexe" type="radio"  value="Feminin" /> 
								</p>
								<p> 
                                    <label for="address" class="adresse" data-icon="">Adresse</label>
                                    <input id="address" name="address" value="'.$adr_err.'" type="text"  />
                                </p>
								<p> 
                                    <label for="city" class="ville" data-icon="">Ville</label>
                                    <input id="city" name="city" type="text" value="'.$ville_err.'" />
                                </p>
								<p> 
                                    <label for="cp" class="zipcode" data-icon="">Code postal</label>
                                    <input id="cp" name="cp" type="text" value="'.$ville_err.'" />
                                </p>
								<p> 
                                    <label for="tel_fixe" class="fix" data-icon="">Tél. domicile</label>
                                    <input id="tel_fixe" name="tel_fixe" type="tel" value="'.$tel_dom_err.'" placeholder="0100000000" />
                                </p>
								<p> 
                                    <label for="tel_mobile" class="fix_1" data-icon="">Tél. portable</label>
                                    <input id="tel_mobile" name="tel_mobile" value="'.$tel_port_err.'" type="tel" placeholder="0699999999" />
                                </p>
                                <p> 
                                    <label for="mail" class="youmail" data-icon="e" >Email *</label>
                                    <input id="mail" name="mail" required="required" value="'.$email_err.'" type="email" placeholder="benoit.dupont@mail.com"/> 
                                </p>
								<p> 
                                    <label for="mail1" class="youmail" data-icon="e" >Confirmation Email *</label>
                                    <input id="mail1" name="mail1" required="required" type="email" placeholder="benoit.dupont@mail.com"/> 
                                </p>
                                <p> 
                                    <label for="passe" class="youpasswd" data-icon="p">Mot de passe *</label>
                                    <input id="passe" name="passe" required="required" type="password" placeholder="mot de passe"/>
                                </p>
                                <p> 
                                    <label for="passe1" class="youpasswd" data-icon="p">Confirmer mot de passe *</label>
                                    <input id="passe1" name="passe1" required="required" type="password" placeholder="confirmer votre mot de passe"/>
                                </p>
								<p><i> * Champs requis</i> </p>
                                <p class="signin button"> 
									<input type="submit" name="inscription_form" value="Valider"/> 
								</p>
                                <p class="change_link">  
									<label for="inscrit" style="display: inline;"> Déjà inscrit ? </label>
									<a href="index.php?page=accueil" id="inscrit" class="to_register">  Authentification </a>
								</p>
                            </form>
					</div>
			</div>
');
}
?>