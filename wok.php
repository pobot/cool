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

$idUser = $loggedInUser->user_id;
$userIsBanker = (($loggedInUser->role == 6)? true : false);

//Forms posted
if(!empty($_POST))
{
    insertMarketTransaction();
}


if(!empty($_GET))
{
    $marketId     = trim($_GET["id"]);

    switch($_GET['sort'])
    {
        case "equipe":
            $sortOrder =" ORDER BY user_name DESC";
        break;
        case "titre":
            $sortOrder =" ORDER BY titre DESC";
        break;
        case "presta":
            $sortOrder =" ORDER BY prestation_name DESC";
        break;
        case "desc":
            $sortOrder =" ORDER BY description ASC";
        break;
        case "price":
            $sortOrder =" ORDER BY price ASC";
        break;
        case "time":
            $sortOrder =" ORDER BY timestamp ASC";
        break;
        default:
            $sortOrder = "";
    }
}

/* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =  

Page for permission level 1 (user)

= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =  = */if ($loggedInUser->checkPermission(array(1)))
    {        
        openPage("Le mur de compétences");    
        
        echo "<p>Vous avez évidemment des talents cachés, de part vos expériences associatives, personnelles, professionnelles.
            Mettez les en valeur et faites gagner à votre équipe des SKEMs.
            Concrètement, allez poster sur le WOC (Wall of competencies ou mur de compétences) la description de votre compétence,
            votre email de contact ainsi que le prix demandé pour faire appel à vous.</p>";
            
        switch($_GET['cmd'])
        {
/*
 *   Liste des offres de compétences déposées par les autres équipes
 */            
        case "bsell":
            echo "<p>Voir la liste des compétences déjà déposées par les autres équipes, vous pouvez en voir le détail et y répondre en cliquant sur la loupe <img src='images/loupe.png'>.<br/>Si à la place vous voyez une pastille <img src='images/accept.png'> c'est que vous avez déjà répondu. Cliquez dessus et vous verrez votre réponse.</p>";
            listeMarketPlace("bsell","Proposer"," AND U.teamId != '".$loggedInUser->team."'$sortOrder");
        break;
/*
 *     Effacer une proposition
 * 
 */
        case "delete":
            echo "<h2>Vous avez demandé à effacer cette offre de compétence</h2>";
            afficheDetails($_GET['id'], 'W');
            echo "
                <form name='delete' action='wok.php' method='post'>
                <div class='form_settings'>
                <input type='hidden'name='cmd'value='delete'>
                <input type='hidden'name='id'value='".$_GET['id']."'>
                <p style='color:red;text-align:center;'>Attention, cette action est définitive !</p>
                <p style='padding-top: 15px;'>
                <input type='submit' name='button' value='Confirmer' class='submit'>
                </p>
                </div>
                </form>";
        break;
/*
 *     La liste des réponses à une proposition
 * 
 */
        case "Rdetail":
            echo "Voici les détails de la proposition sélectionnée.";
            afficheDetails($_GET['id'], 'W');
            
            echo "<h2>Liste des paiements obtenus pour cette offre de compétence</h2>";
            echo "<table>
            <tr><th>".sortLink("equipe&cmd=$cmd","Equipe")."</th><th>".sortLink("titre&cmd=$cmd","Titre")."</th><th>".sortLink("desc&cmd=$cmd","Description")."</th><th>".sortLink("presta&cmd=$cmd","Date")."</th><th>".sortLink("time&cmd=$cmd","Prix proposé")."</th><th></th></tr>";

            $sql = "SELECT M.id, type, user_name, titre, description, timestamp, price FROM market M, sk_users U WHERE market_id = '".$_GET['id']."' AND user_id =U.id ";
//            echo "<p>$sql";
            $detail =  mysqli_query($mysqli, $sql);
            while (list($id, $type, $idUser, $titre, $description, $timestamp, $price)  = mysqli_fetch_row($detail))
            {
                if ($type == 'Payed') {
                    $tmp = "<img src='images/accept.png'>";
                } else {
                    $tmp ="PROBLEME";
                }
                echo "<tr><td>$idUser</td><td>$titre</td><td>$description</td><td>$timestamp</td><td>$price</td><td>$tmp</td></tr>";                
            }
            echo "</table>";                                    
        break;
/*
    Le détail d'une proposition et la boite de réponse
*/
        case "detail":
            echo "<p>Voici les détails de la proposition sélectionnée, si vous êtes réellement intéressé vous devrez prendre rapidement contact avec l'équipe qui a posté cette proposition de compétence pour réaliser la transaction.</p>";            
            afficheDetails($_GET['id'], 'W');
            echo "<h2>Paiement</h2>";
            afficheReponse($_GET['id'], 'W');
        break;
/*
 * La page de garde par défaut du Marketplace
 *   Liste des offres de compétences déposées par l'équipe.
 *   Quand une réponse est disponible, la loupe est affichée au bout de la ligne avec le nombre de réponses.
 */            

        default:
            echo "<h3>Les compétences déjà déposées par votre équipe.</h3>";
        	if ($userIsBanker)
        	{
        		echo "
            	<p>Vous êtes banquier, vous êtes donc le seul dans votre équipe à pouvoir procéder aux transactions.";
        	} else
        	{
        		echo "
            	<p>Vous n'êtes pas banquier, il est le seul dans votre équipe à pouvoir procéder aux transactions.";      		
        	}
        	echo "Si vous avez reçu des paiements pour cela,
            une pastille verte <img src='images/accept.png'> est affichée avec leur nombre entre parenthèses.<br/>
            Vous devez cliquer dessus pour afficher la liste des paiements reçus.</p>";
            listeMarketPlace("ysell","Proposer"," AND U.teamId = '".$loggedInUser->team."'$sortOrder");

            echo "Vendre une compétence";
            displayInputForm("Proposer");
        break;
        }

    } //fin student
   
/* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =  

Page for permission level 2 (professor)

= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =  = */   
    if ($loggedInUser->checkPermission(array(2))){
        openPage("Welcome professor");

        switch($_GET['cmd'])
        {
/*
    Le détail d'une proposition et la boite de réponse
*/
        case "detail":
          echo "<p>Voici les détails de la proposition sélectionnée.</p>";            
          afficheDetails($_GET['id'], 'W');
        break;
        default:
            echo "<p>Voir la liste des compétences déjà déposées par les équipes, vous pouvez en voir le détail en cliquant sur la loupe <img src='images/loupe.png'>.<br/></p>";
            listeMarketPlace("bsell","Proposer"," $sortOrder");
        break;
        }

        
    } // fin professeur
    
    if ($loggedInUser->checkPermission(array(3))){ //Links for permission level 3 (administrator)
        openPage("Les scores globaux");
        
        if ($result = mysqli_query($mysqli, "SELECT display_name, sum(debit),count(debit), sum(credit), count(credit) 
            FROM account A, sk_users U WHERE A.account1 = U.id GROUP BY account1")) {
    		echo "<table style='width:100%; border-spacing:0;'>".
				  "<tr><th>Team</th><th>Débit</th><th>Crédit</th><th width='50%'>Solde</th></tr>";
		/* fetch associative array */
                while (list($teamName, $debit, $debitTrans, $credit, $creditTrans)  = mysqli_fetch_row($result)) {
			echo "<tr><td>$teamName</td><td>".number_format($debit)." ($debitTrans) </td><td>".number_format($credit)." ($creditTrans)</td><td>".number_format($credit-$debit)."</td></tr>";
	         }
                 echo "</table>
		<br/>
* * * * End Of Report * * * *";
            /* Libération du jeu de résultats */
            mysqli_free_result($result);        
        }
    } // fin admin

closePage();

?>