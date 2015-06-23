<?php

if(isset($_SESSION['id']))
	{
	echo ('</p>Vous êtes déjà inscrit, veuillez attendre la redirection automatique</p>');	
	header('refresh: 1; URL=index.php?page='.$_SESSION['page_defaut'].'');
	}
else
{
	
include('/var/www/gimela/include/verification.php');

echo ('	<div class="animate form">
		<form method="post" action="#" > 
			<div id="form_inscription">
                                <h1> Inscription </h1> 
                                <p> 
                                    <label for="prenom" class="name" data-icon="u">Prénom</label>
                                    <input id="prenom" name="prenom" required="required" type="text" placeholder="ex.. Martin" />
                                </p>
								<p> 
                                    <label for="name" class="surname" data-icon="u">Nom</label>
                                    <input id="name" name="name" required="required" type="text" placeholder="ex.. Dupont" />
                                </p>
								<p> 
                                    <label for="pseudo" class="uname" data-icon="u">Pseudo</label>
                                    <input id="pseudo" name="pseudo" required="required" type="text" placeholder="ex. mdupont" />
                                </p>
								<p> 
                                    <label for="naissance" class="date_naissance" data-icon="u">Date de naissance</label>
                                    <input id="naissance" name="naissance" required="required" type="date" placeholder="sous la forme jj/mm/aaaa" min="1920-01-01" min="2050-01-01"/>
                                </p>
								<p> 
                                    <label for="profession" class="job" data-icon="">Profession</label>
                                    <input id="profession" name="profession" required="required" type="text" placeholder="ex. ingénieur en réalité augmenté"/>
                                </p>
								<p><label for="sexe" class="genre">Sexe :</label>
									Masculin <input name="sexe" required="required" type="radio"   value="Masculin" />
									Feminin <input  name="sexe" required="required" type="radio"  value="Feminin" /> 
								</p>
								<p> 
                                    <label for="address" class="adresse" data-icon="">Adresse</label>
                                    <input id="address" name="address" required="required" type="text"  />
                                </p>
								<p> 
                                    <label for="city" class="ville" data-icon="">Ville</label>
                                    <input id="city" name="city" required="required" type="text" />
                                </p>
								<p> 
                                    <label for="cp" class="zipcode" data-icon="">Code postale</label>
                                    <input id="cp" name="cp" required="required" type="text" />
                                </p>
								<p> 
                                    <label for="tel_fixe" class="fix" data-icon="">Tél. domicile</label>
                                    <input id="tel_fixe" name="tel_fixe" require="required"  type="tel" placeholder="0100000000" />
                                </p>
								<p> 
                                    <label for="tel_mobile" class="fix_1" data-icon="">Tél. portable</label>
                                    <input id="tel_mobile" name="tel_mobile"  type="tel" placeholder="0699999999" />
                                </p>
                                <p> 
                                    <label for="mail" class="youmail" data-icon="e" >Email</label>
                                    <input id="mail" name="mail" required="required" type="email" placeholder="benoit.dupont@mail.com"/> 
                                </p>
								<p> 
                                    <label for="mail1" class="youmail" data-icon="e" >Confirmation Email</label>
                                    <input id="mail1" name="mail1" required="required" type="email" placeholder="benoit.dupont@mail.com"/> 
                                </p>
                                <p> 
                                    <label for="passe" class="youpasswd" data-icon="p">Mot de passe </label>
                                    <input id="passe" name="passe" required="required" type="password" placeholder="mot de passe"/>
                                </p>
                                <p> 
                                    <label for="passe1" class="youpasswd" data-icon="p">Confirmer mot de passe</label>
                                    <input id="passe1" name="passe1" required="required" type="password" placeholder="confirmer votre mot de passe"/>
                                </p>
                                <p class="signin button"> 
									<input type="submit" value="Valider"/> 
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