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

//Forms posted
if(!empty($_POST))
{
    print_r($_POST);
    
    $account1 = $loggedInUser->user_id;
    $accountName1 = $loggedInUser->displayname;
    
    $account2 = trim($_POST["accountTo"]);
    $result = mysqli_query($mysqli,"SELECT display_name FROM sk_users WHERE id = '$account2'");
    list($accountName2) = mysqli_fetch_row($result);
    
    $deposit = trim($_POST["deposit"]);
    $prestationId = trim($_POST['prestation']);
    $marketId = trim($_POST['marketId']);
    $marketPlace = trim($_POST['marketPlace']);
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    
    $timeStamp = date("Y-m-d H:i:s");

/*
    echo "<h1>Performing transaction</h1>
    Deposit $deposit From $account1 to $account2";
*/
    // On retire la somme de account1
    $sql = "INSERT INTO `account` (`id`, `account1`, `account2`, `debit`, `credit`, `description`, timestamp, prestation_id) 
            VALUES (NULL, '$account1', '$account2', '$deposit', NULL, 'Deposit to $accountName2', '$timeStamp', '$prestationId');";
    $result = mysqli_query($mysqli, $sql);
    // On ajoute la somme à account2
    $sql = "INSERT INTO `account` (`id`, `account1`, `account2`, `debit`, `credit`, `description`, timestamp, prestation_id) 
            VALUES (NULL, '$account2', '$account1', NULL, '$deposit', 'Deposit from $accountName1', '$timeStamp', '$prestationId');";
    $result = mysqli_query($mysqli, $sql);
    // On marque la transaction effectuée dans la place de marché
    if ($marketPlace == 'WOC'){
        $sql = "INSERT INTO `market` (`id`, `user_id`,`type`, `titre`, `description`, prestation_id, market_id, timestamp, price) 
        VALUES (NULL, '$account1', 'Payed', '$titre', '$description', '$prestationId', '$marketId', '$timeStamp', '$deposit');";
    }  else {
        $sql = "UPDATE market SET type = 'Payed' WHERE id = '$marketId'";        
    }
    $result = mysqli_query($mysqli, $sql);
    header("Location: account.php");
    die();
} 

openPage("Online Payment");


echo "<h1>Payment</h1>
    <p>Le formulaire ci dessous permet de transférer directement une somme sur le compte d'une équipe. À utiliser avec précaution et parcimonie.</p>";


$result = mysqli_query($mysqli, "SELECT id, user_name FROM sk_users WHERE (id != '".$loggedInUser->user_id."') AND id NOT IN (SELECT user_id FROM sk_user_permission_matches WHERE permission_id != '1')");
echo "<p>
                <form name='deposit' action='".$_SERVER['PHP_SELF']."' method='post'>
                <div class='form_settings'>
                <span>Destination account:</span><select name='accountTo'>";
while (list($id, $user_name)  = mysqli_fetch_row($result)) {
    echo "<option value='$id'>$user_name</option>";
}
echo "</select>";

echo selectPresta();

echo "<span>Amount:</span> <input type='text' name='deposit'>
    <p style='padding-top: 15px'>
    <input type='submit' name='ok' value='Deposit' class='submit'>
                </div>
                </form>";

//echo "<h1>Your account</h1>";
//accountBrowse($loggedInUser->user_id);

closePage();

?>
