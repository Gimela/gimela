
<?php
if(isset($_SESSION['id']))
	{
	echo ('</p>Vous êtes déjà connecté, veuillez attendre la redirection automatique</p>');	
	header('refresh: 1; URL=index.php?page='.$_SESSION['page_defaut'].'');
	}
else
	{
echo (' <div id="form_authentification">
			<form  method="post" action="index.php?page=auth"> 
				<h1>GIMELA</h1> 
				<p> 
					<label for="username" class="uname" data-icon="u" > Identifiant </label>
					<input id="username" name="pseudo" required="required" type="text" />
				</p>
				<p> 
					<label for="password" class="youpasswd" data-icon="p" > Mot de passe </label>
					<input id="password"  name="passe" required="required" type="password" /> 
				</p>
			  
				<p class="login button"> 
					<input type="submit" name="auth" value="Login" /> 
				</p>
			</form>
				<p class="change_link">
					<label for="inscript"> Nouveau membre ? </label>
					<a href="index.php?page=inscription" id="inscript" name="inscription" class="to_register" style="margin: 5px;">Inscription</a><br/>
					<label for="enregistrer"> Mot de passe oublié ? </label>
					<a href="index.php?page=motdepasseperdu" id="enregistrer" class="to_register">Mot de passe</a> <br/>
				</p>
		</div>
	');
	}
?>
