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

if(!empty($_POST))
{   
//Forms posted
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
        openPage("Seeker/Solver Marketplace");        
            
        switch($_GET['cmd'])
        {
/*
 * 
 * 
 */
        case "bbuy":
            echo "<h3>Les demandes de compétence des autres équipes</h3>";
                    	if ($userIsBanker)
        	{
        		echo "
            	<p>Vous êtes banquier, vous êtes donc le seul dans votre équipe à pouvoir procéder aux transactions.";
	            echo "Vous pouvez en voir le détail et y répondre en cliquant sur la loupe <img src='images/loupe.png'>.<br/>
                Si à la place vous voyez une pastille <img src='images/add.png'> c'est que vous avez déjà répondu, si c'est <img src='images/accept.png'> c'est que vous avez répondu et que le marché a été accepté par l'autre équipe qui vous a payé.<br/><p>Cliquez sur l'une ou l'autre de ces icones pour revoir votre réponse.</p>";        	} else
        	{
        		echo "
            	<p>Vous n'êtes pas banquier, il est le seul dans votre équipe à pouvoir procéder aux transactions.";      		
        	}
            listeMarketPlace("bbuy","Demander","  AND U.teamId != '".$loggedInUser->team."'$sortOrder");
        break;
/*
 *     Effacer une proposition
 * 
 */
        case "delete":
            echo "<h2>Vous avez demandé à effacer cette question</h2>";
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
            echo "Détails de la question sélectionnée.";
            afficheDetails($_GET['id'], 'M');
            
            echo "<h2>Liste des réponses obtenues pour cette question</h2>
                <p>Vous pouvez choisir les solutions qui vous intéressent dans la liste ci-dessous et réaliser la transaction (payer l'autre équipe) en suivant le lien 'Accepter'. Si un <img src='images/accept.png'> apparait, c'est que vous avez déjà sélectionné cette proposition et payé l'autre équipe pour cela.";
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
                    $tmp ="<a href='".$_SERVER['PHP_SELF']."?cmd=Accept&idD=".$_GET['id']."&idR=$id'> Accepter</a>";
                }
                echo "<tr><td>$idUser</td><td>$titre</td><td>$description</td><td>$timestamp</td><td>$price</td><td>$tmp</td></tr>";                
            }
            echo "</table>";

        break;
/*
 *     proposition acceptée, on affiche le formulaire de règlement
 * 
 */
        case "Accept":
            echo "Vous avez sélectionné la réponse d'une équipe. Si vous ne l'avez pas encore fait, il faut maintenant contacter cette équipe pour finaliser la transaction et procéder au règlement avec le formulaire ci-dessous.";
            afficheAccept($_GET['idD'],$_GET['idR']);
        break;
/*
    Le détail d'une proposition et la boite de réponse
*/
        case "detail":
            echo "Voici les détails de la proposition sélectionnée.";
            afficheDetails($_GET['id'], 'M');
            
            echo '<h2>Répondre à cette annonce</h2>';
            afficheReponse($_GET['id'], 'M');
        break;
/*
 * La page de garde par défaut du Marketplace affiche les questions déjà posées
 * 
 */
        default:
           	echo "<h3>Les demandes d'aide de votre équipe</h3>";
        	if ($userIsBanker)
        	{
        		echo "
            	<p>Vous êtes banquier, vous êtes donc le seul dans votre équipe à pouvoir procéder aux transactions.";
        	} else
        	{
        		echo "
            	<p>Vous n'êtes pas banquier, il est le seul dans votre équipe à pouvoir procéder aux transactions.";      		
        	}
        	echo "Le nombre de réponses est affiché entre parenthèses, tant que vous n'avez pas reçu de demandes cous pouvez l'effacer en cliquant sur la corbeille <img src='images/delete.png' width='24px'>.
            	Si à la place vous voyez une pastille <img src='images/add.png'> ou <img src='images/accept.png'> c'est que vous avez déjà répondu. Cliquez dessus et vous verrez votre réponse.</p>";
            listeMarketPlace("ybuy","Demander"," AND U.teamId = '".$loggedInUser->team."'$sortOrder");
            
/*
 * Affiche le formulaire pour déposer un "seek", poser une question
 * 
 */            
            echo "<p>Décrivez votre problème pour le soumettre à la communauté.</p>";
            displayInputForm("Demander");

        break;
        }

    } //fin student
   
/* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =  

Page for permission level 2 (professor)

= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =  = */
    if ($loggedInUser->checkPermission(array(2))){
        openPage("Seeker/Solver Marketplace vue Professeur");    
        
        switch($_GET['cmd'])
        {
/*
    Le détail d'une proposition et la boite de réponse
*/
        case "detail":
            echo "<p>Voici les détails de la proposition sélectionnée.</p>";            
            afficheDetails($_GET['id'], 'M');
        break;
        default:
            echo "<p>Les questions de toutes les équipes, vous pouvez en voir le détail en cliquant sur la loupe <img src='images/loupe.png'>.
                </p>";
            listeMarketPlace("bbuy","Demander"," $sortOrder");
        break;
        }
        
    } // fin professeur
    
/* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =  

Page for permission level 3 (administrator)

= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =  = */    if ($loggedInUser->checkPermission(array(3))){
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