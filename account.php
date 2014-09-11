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

/* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =  

Page for permission level 1 (user)

= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =  = */if ($loggedInUser->checkPermission(array(1))) {
    openPage("Your Account");

	if (isUserReady($loggedInUser->user_id))
	{
	    echo "<p>
            A chaque fois que vous collaborerez avec les membres d'une autre équipe, vous recevrez de la part de cette équipe une rémunération en SKEM pour service rendu.
            La règle du marché prévaut, aussi les prix d'échange se fixent d'un commun accord entre les parties, en toute transparence.</p>";

    	echo '<div style="display: none;" id="blanket"></div> 
<div style="display: none;" id="popUpDiv"> 
<a onclick="popup(\'popUpDiv\')" href="#"><img alt="" src="images/delete.png" /></a><br />
<p class="popup">
<ul>
<li>Vous pouvez vous prêter du matériel (caméra, micro, véhicule pour vous rendre sur un RDV, ...)</li>
<li>Vous pouvez échanger des idées</li>
<li>Vous pouvez apporter une compétence ou une connaissance spécifique en management (spécialiste de la finance, ...)</li>
<li>Vous pouvez apporter une compétence ou une connaissance spécifique en développement durable (issue d\'une expérience associative ou personnelle, d\'un stage, ...)</li>
<li>Vous pouvez apporter une compétence en gestion de projet et en organisation</li>
<li>Vous pouvez apporter une compétence technique en audiovisuelle et multimédia</li>
<li>Vous pouvez apporter une compétence technique en wiki</li>
<li>Vous pouvez partager vos réseaux de contacts pour identifier ou recruter les personnes à interviewer</li>
<li>... ou vous pouvez faire jouer vos talents hors scolaires...</li>
</ul>
</p> 
</div>

<p><a onclick="popup(\'popUpDiv\')" href="#">Quels sont les services que nous pouvons échanger ?</a></p>';

    	echo "</p>";
    	accountBrowse($loggedInUser->user_id);

	    echo "<h4>Prix moyens généralement constatés</h4>
                Voici les prix moyens généralement constatés, ceci vous donne l'ordre de grandeur d'une prestation mais rien ne vous empêche de vendre plus ou moins cher en fonction de ce que vous proposez réellement.
                <p>
            <table>
            <tr><th>Prestation</th><th>Prix conseillé</th><th>Prix moyen constaté</th><th>Prix minimum</th><th>Prix maximum</th></tr>";
    	$result = mysqli_query($mysqli, "SELECT id, prestation_name, prestation_price FROM prestation ORDER BY prestation_name");
    	while (list($idPresta,$name,$price) = mysqli_fetch_row($result)) {
        	echo "<tr><td>$name </td><td style='text-align: right;'>".number_format($price)."</td>";
                $sql = "SELECT AVG(price) FROM market WHERE prestation_id = '$idPresta'";
                $rc_market = mysqli_query($mysqli, $sql);
	        list($priceMarket) = mysqli_fetch_row($rc_market);
    	    if ($priceMarket == '0')
        	    $priceMarket = "N/A";
        	else
           		$priceMarket = number_format($priceMarket);
        	echo "<td style='text-align: right;'>$priceMarket</td>";
               
        	$sql = "SELECT MIN(price) FROM market WHERE prestation_id = '$idPresta'";
        	$rc_market = mysqli_query($mysqli, $sql);
        	list($priceMarket) = mysqli_fetch_row($rc_market);
        	if ($priceMarket == '0')
        	    $priceMarket = "N/A";
        	else
        	    $priceMarket = number_format($priceMarket);
        	echo "<td style='text-align: right;'>$priceMarket</td>";

	        $sql = "SELECT MAX(price) FROM market WHERE prestation_id = '$idPresta'";
    	    $rc_market = mysqli_query($mysqli, $sql);
        	list($priceMarket) = mysqli_fetch_row($rc_market);
        	if ($priceMarket == '0')
       	    	$priceMarket = "N/A";
        	else
           		$priceMarket = number_format($priceMarket);
        	echo"<td style='text-align: right;'>$priceMarket</td>";

	        echo"</tr>";
    	}
    	echo "</table>
        </p>";
    	echo "<p>Notez que nous (équipe pédagogique) n'interviendront en aucun cas dans la régulation des prix pratiqués dans ce marché, même si nous pourront participer aux échanges, et pourquoi pas à l'injection ou à la ponction de liquidité.</p>";
    
	    browseMyTeam($loggedInUser->team);
	} else
	{
		echo "<h4>Vous devez d'abord remplir votre profil dans <a href='user_settings.php'>User Settings</a></h4>";
	}
}
    
/* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =  

Page for permission level 2 (professor)

= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =  = */if ($loggedInUser->checkPermission(array(2)))
    {
        openPage("Les scores globaux");

        switch($_GET['sort'])
        {
            case "ccredit":
                $sortOrder =" ORDER BY count(credit) DESC";
            break;
            case "cdebit":
                $sortOrder =" ORDER BY count(debit) DESC";
            break;
            case "credit":
                $sortOrder =" ORDER BY sum(credit) DESC";
            break;
            case "debit":
                $sortOrder =" ORDER BY sum(debit) DESC";
            break;
            case "solde":
                $sortOrder =" ORDER BY coalesce(sum(credit), 0)- coalesce(sum(debit), 0) DESC";
            break;
            default:
                $sortOrder = "";
        }
        
        if ($result = mysqli_query($mysqli, "SELECT display_name, coalesce(sum(debit), 0) as totalDebit ,count(debit), coalesce(sum(credit),0) as totalCredit, count(credit),  coalesce(sum(credit), 0)- coalesce(sum(debit), 0) FROM account A, sk_users U WHERE A.account1 = U.id GROUP BY account1 $sortOrder"))
            {
    		echo "<table style='width:100%; border-spacing:0;'>".
				  "<tr><th>".sortLink("team","Team")."</th>
                                       <th>".sortLink("debit","Débit")." (".sortLink("cdebit","nb").")</th>
                                       <th>".sortLink("credit","Crédit")." (".sortLink("ccredit","nb").")</th>
                                       <th width='50%'>".sortLink("solde","Solde")."</th></tr>";
		/* fetch associative array */
                while (list($teamName, $debit, $debitTrans, $credit, $creditTrans, $solde)  = mysqli_fetch_row($result))
                {
                    echo "<tr><td>$teamName</td><td style='text-align: right;'>".number_format($debit)." ($debitTrans) </td><td  style='text-align: right;'>".number_format($credit)." ($creditTrans)</td><td>".number_format($solde)."</td></tr>";
	        }
                echo "</table>
		<br/>
* * * * End Of Report * * * *";
            /* Libération du jeu de résultats */
                mysqli_free_result($result);        
            }
    }
/* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =  

Page for permission level 3 (administrator)

= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =  = */    
if ($loggedInUser->checkPermission(array(3)))
    {
        openPage("Les scores globaux des équipes");

        switch($_GET['sort'])
        {
            case "ccredit":
                $sortOrder =" ORDER BY count(credit) DESC";
            break;
            case "cdebit":
                $sortOrder =" ORDER BY count(debit) DESC";
            break;
            case "credit":
                $sortOrder =" ORDER BY sum(credit) DESC";
            break;
            case "debit":
                $sortOrder =" ORDER BY sum(debit) DESC";
            break;
            case "solde":
                $sortOrder =" ORDER BY coalesce(sum(credit), 0)- coalesce(sum(debit), 0) DESC";
            break;
            default:
                $sortOrder = "ORDER BY teamId";
        }
        
        if ($result = mysqli_query($mysqli, "SELECT U.teamId, display_name, coalesce(sum(debit), 0) as totalDebit ,count(debit), coalesce(sum(credit),0) as totalCredit, count(credit),  coalesce(sum(credit), 0)- coalesce(sum(debit), 0) FROM account A, sk_users U WHERE A.account1 = U.id GROUP BY account1 $sortOrder"))
            {
    		echo "<table style='width:100%; border-spacing:0;'>".
				  "<tr><th>".sortLink("team","Team")."</th>
				  					   <th>".sortLink("user","User")."</th>
                                       <th>".sortLink("debit","Débit")." (".sortLink("cdebit","nb").")</th>
                                       <th>".sortLink("credit","Crédit")." (".sortLink("ccredit","nb").")</th>
                                       <th width='50%'>".sortLink("solde","Solde")."</th></tr>";
		/* fetch associative array */
                while (list($teamName, $userName, $debit, $debitTrans, $credit, $creditTrans, $solde)  = mysqli_fetch_row($result))
                {
                    echo "<tr><td>$teamName</td><td>$userName</td><td style='text-align: right;'>".number_format($debit)." ($debitTrans) </td><td  style='text-align: right;'>".number_format($credit)." ($creditTrans)</td><td>".number_format($solde)."</td></tr>";
	        }
                echo "</table>
		<br/>
* * * * End Of Report * * * *";
            /* Libération du jeu de résultats */
                mysqli_free_result($result);        
            }            
    }

closePage();

?>
