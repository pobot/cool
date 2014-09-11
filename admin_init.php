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

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("inc/functions.php");

openPage("Initialisation");

echo "
Just so you know, your title at the moment is $loggedInUser->title, and that can be changed in the admin panel. You registered this account on " . date("M d, Y", $loggedInUser->signupTimeStamp()) . ".";

echo "<h2>RAZ tables</h2>";
	$result = mysqli_query($mysqli, "DELETE FROM account");
	$result = mysqli_query($mysqli, "ALTER TABLE  `account` AUTO_INCREMENT = 1");

        $result = mysqli_query($mysqli, "delete from sk_users where id >1");
	$result = mysqli_query($mysqli, "ALTER TABLE  `sk_users` AUTO_INCREMENT = 2");
        
	$result = mysqli_query($mysqli, "DELETE FROM sk_user_permission_matches WHERE USER_ID != 1");
	$result = mysqli_query($mysqli, "DELETE FROM market");
	
echo "<h2>creation des users 'Professeurs'</h2>";


addProfessor("mel", "M&eacute;lanie", "melanie.ciussi@skema.edu");
addProfessor("dom", "Dominique", "dominique.vian@skema.edu");
addProfessor("xtophe", "Christophe", "christophe.sempels@skema.edu");
addProfessor("pierre", "Pierre", "pierre.daniel@skema.edu");
addProfessor("laurence", "Laurence", "laurence.berlie@skema.edu");
addProfessor("marc", "Marc", "marc.augier@skema.edu");
addProfessor("sophie", "Sophie", "sophie.charles@skema.edu");
        

echo "<h2>creation des users 'Etudiants'</h2>";
for ($i = 1; $i < 201; $i++)
{
	echo "<br/>creation de Groupe$i";
	$result = mysqli_query($mysqli, "INSERT INTO `sk_users` (`id`, `user_name`, `display_name`, `password`, `email`, `activation_token`, `last_activation_request`, `lost_password_request`, `active`, `title`, `sign_up_stamp`, `last_sign_in_stamp`) VALUES (NULL, 'groupe$i', 'Groupe $i', '9051a509f95691159c7ed617fd884f29af9213d747b13b6c7860fff6fb40cb24d', 'user$i@skema.edu', 'b3f4ed2c42cc370d457f9caa201617a8', 1377894239, 0, 1, 'Student', 1377894239, 1377898821);");
        $result = mysqli_query($mysqli,"SELECT id FROM `sk_users` WHERE user_name = 'Groupe$i';");
        list($idNew)  = mysqli_fetch_row($result);
	$result = mysqli_query($mysqli, "INSERT INTO `sk_user_permission_matches` (`id`, `user_id`, `permission_id`) VALUES (NULL, '$idNew', '1');");
        
        $timeStamp = date("Y-m-d H:i:s");
	$result = mysqli_query($mysqli, "INSERT INTO `account` (`id`, `account1`, `account2`, `debit`, `credit`, `description`, timestamp) VALUES (NULL, '$idNew', NULL, NULL, '10000', 'Solde Initial','$timeStamp');");
}

closePage();

?>
