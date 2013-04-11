<script type="text/javascript">
<!--
jQuery(document).ready(function(){
	// binds form submission and fields to the validation engine
	jQuery("#registerForm").validationEngine();
});

function checkSubmit(){
	
	if( $('#resultIdentifiant').val() == 'alreadyUse'){
		return false;
	}
	
	if( $('#resultEmail').val() == 'alreadyUse'){
		return false;
	}
	
	return true;
}

function checkIdentifiant(){
	var result = '';
    var xhr = getXMLHttpRequest();
	xhr.open("GET", '{getLink("utilisateur/check_identifiant/'+document.getElementById('identifiant').value+'?nohtml")}', true);
	xhr.onreadystatechange = function () {
        if(xhr.readyState == 4){
            result = xhr.responseText;
            if(result == 'alreadyUse'){
                 document.getElementById('identifiantVerif').innerHTML = '<span style="color:#FF0000;"><img src="{$config.url}{$config.url_dir}web/images/noSmall.png" /> <strong>{$lang.Identifiant_deja_utilise}</strong></span>';
				 document.getElementById('resultIdentifiant').value = 'alreadyUse';			
            }else{
                 document.getElementById('identifiantVerif').innerHTML = '<img src="{$config.url}{$config.url_dir}web/images/okSmall.png" />';
				 document.getElementById('resultIdentifiant').value = '';
            }
        }
	}
	xhr.send(null);
}

function checkEmail(){
	var result = '';
    var xhr = getXMLHttpRequest();
	xhr.open("GET", '{getLink("utilisateur/check_email/'+document.getElementById('email').value+'?nohtml")}', true);
	xhr.onreadystatechange = function () {
        if(xhr.readyState == 4){
            result = xhr.responseText;
            if(result == 'alreadyUse'){
                 document.getElementById('emailVerif').innerHTML = '<span style="color:#FF0000;"><img src="{$config.url}{$config.url_dir}web/images/noSmall.png" /> <strong>{$lang.Email_deja_utilise}</strong></span>';
				 document.getElementById('resultEmail').value = 'alreadyUse';			
            }else{
                 document.getElementById('emailVerif').innerHTML = '<img src="{$config.url}{$config.url_dir}web/images/okSmall.png" />';
				 document.getElementById('resultEmail').value = '';
            }
        }
	}
	xhr.send(null);
}
//-->
</script>
{strip}
<h2>{$lang.Enregistrement}</h2>
<br/>
<form class="form" method="post" action="#" onsubmit="return checkSubmit();" id="registerForm">
	<dl>
		<dt><label for="identifiant">{$lang.Identifiant} :</label></dt>
		<dd><input type="text" name="user[identifiant]" {if isset($smarty.post.user.identifiant)}value=""{/if} id="identifiant" class="validate[required]" onchange="checkIdentifiant();" /><span id="identifiantVerif"></span></dd>
	</dl>
	<dl>
		<dt><label for="email">{$lang.Email} :</label></dt>
		<dd><input type="email" name="user[email]" id="email" placeholder="{$lang.Votre_email}" class="validate[required,custom[email]]" onchange="checkEmail();" autocomplete="off" /><span id="emailVerif"></span></dd>
	</dl>
	<dl>
		<dt><label for="password">{$lang.Mot_de_passe} :</label></dt>
		<dd><input type="password" name="user[password]" id="password" class="validate[required]" /></dd>
	</dl>
	<dl>
		<dt><label for="password2">{$lang.Confirmation} :</label></dt>
		<dd><input type="password" name="user[password2]" id="password2" class="validate[required,equals[password]]" /></dd>
	</dl>
	<div class="form_submit" style="text-align:center;">
		<input type="hidden" id="resultIdentifiant" value="" />
		<input type="hidden" id="resultEmail" value="" />
		<input type="submit" name="send" value="{$lang.Enregistrer}" />
	</div>
</form>
{/strip}