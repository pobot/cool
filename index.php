<?php

/*
==============================================================================

	Copyright (c) 2013 Marc Augier

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: m.augier@me.com
==============================================================================
*/

/*
UserCake Version: 2.0.2
http://usercake.com
*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
//Prevent the user visiting the logged in page if he/she is already logged in
if(isUserLoggedIn()) { header("Location: account.php"); die(); }

require_once('inc/ldap_var.inc.php');
require_once('inc/authldap.php');
require_once("inc/functions.php");

//Forms posted
if(!empty($_POST))
{
	$errors = array();
	$username = sanitize(trim($_POST["username"]));
	$password = trim($_POST["password"]);
	
	//Perform some validation
	//Feel free to edit / change as required
	if($username == "")
	{
		$errors[] = lang("ACCOUNT_SPECIFY_USERNAME");
	}
	if($password == "")
	{
		$errors[] = lang("ACCOUNT_SPECIFY_PASSWORD");
	}

	$res = ldap_authentication_check($username, $password);
	// res=-1 -> the user does not exist in the ldap database
	// res=1 -> invalid password (user does exist)

	if ($res==1) //WRONG PASSWORD
	{
		$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
	}
	if ($res==-1) //WRONG USERNAME
	{
		$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
	}
	if ($res==0) //LOGIN & PASSWORD OK - SUCCES
	{
		// echo "<h1>LDAP OK for $username</h1>";
		if(!usernameExists($username))
		{
			//First connexion, user needs to be added to SKEM Bank DB
			// After being created :
			// Change permision_id in sk_user_permission_matches to 
			// 3 for admin
			// 2 for professor
			// 1 is default for students
			$successes[] = lang("ACCOUNT_NEW_USER_WELCOME");
			$userInfo = ldap_find_user_info ($username);
//			print_r($userInfo);

			//Construct a user object
			$username = $userInfo['username'];
			$displayname = $userInfo['firstname']." ".$userInfo['lastname'];
			$password = 'LDAPUSER';
			$email = $userInfo['email'];
			
			$user = new User($username,$displayname,$password,$email);
		
			//Checking this flag tells us whether there were any errors such as possible data duplication occured
			if(!$user->status)
			{
				if($user->username_taken) $errors[] = lang("ACCOUNT_USERNAME_IN_USE",array($username));
				if($user->displayname_taken) $errors[] = lang("ACCOUNT_DISPLAYNAME_IN_USE",array($displayname));
				if($user->email_taken) 	  $errors[] = lang("ACCOUNT_EMAIL_IN_USE",array($email));		
			}
			else
			{
				//Attempt to add the user to the database, carry out finishing  tasks like emailing the user (if required)
				if(!$user->userCakeAddUser())
				{
					if($user->mail_failure) $errors[] = lang("MAIL_ERROR");
					if($user->sql_failure)  $errors[] = lang("SQL_ERROR");
				}
			}
			if(count($errors) == 0) {
				$successes[] = $user->success;
//				echo "<p>New user created";
			}
		}
		// Now login procedure 

		$userdetails = fetchUserDetails($username);
		//See if the user's account is activated
		if($userdetails["active"]==0)
		{
			$errors[] = lang("ACCOUNT_INACTIVE");
		}
		else
		{					
			//Construct a new logged in user object
			//Transfer some db data to the session object
			$loggedInUser = new loggedInUser();
			$loggedInUser->email = $userdetails["email"];
			$loggedInUser->user_id = $userdetails["id"];
			$loggedInUser->hash_pw = $userdetails["password"];
			$loggedInUser->title = $userdetails["title"];
			$loggedInUser->displayname = $userdetails["display_name"];
			$loggedInUser->username = $userdetails["user_name"];
			
			$loggedInUser->campus = $userdetails["campus"];
			$loggedInUser->team = $userdetails["team"];
			$loggedInUser->role = $userdetails["role"];
					
			//Update last sign in
			$loggedInUser->updateLastSignIn();
			$_SESSION["userCakeUser"] = $loggedInUser;
					
			//Redirect to user account page
			header("Location: account.php");
			die();
		}
	}
}


require_once("inc/functions.php");

openPage("Welcome");

echo "
<p>Bienvenue sur la banque en ligne et le marketplace du cours Performance Durable.<p>";

title("home","Merci de vous connecter");

echo resultBlock($errors,$successes);

echo "
<form name='login' action='".$_SERVER['PHP_SELF']."' method='post'>
<div class='form_settings'>
<span>Username:</span>
<input type='text' name='username' />
</p>
<p>
<span>Password:</span>
<input type='password' name='password' />
<p style='padding-top: 15px'>
<input type='submit' value='Login' class='submit' />
</div>
</form>
</div>";

closePage();

?>