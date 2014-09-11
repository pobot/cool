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

function openPage($title)
{
    global $websiteName, $template, $mysqli, $emailActivation, $loggedInUser;

    echo "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<title>".$websiteName."</title>
  <link rel='stylesheet' type='text/css' href='css/style.css' />
<script src='models/funcs.js' type='text/javascript'>
<!-- modernizr enables HTML5 elements and feature detects -->
<script type='text/javascript' src='js/modernizr-1.5.min.js'></script>
<script type='text/javascript' src='js/jquery.min.js'></script>
<script type='text/javascript' src='js/pop.js'></script>

</script>
</head>

<body>
    <div id='main'>
    <header>
        <div id='logo'>
        <div id='logo_text'>
          <!-- class='logo_six', allows you to change the colour of the text -->
          <h1><span class='logo_six'><a href='index.php'>$websiteName</a></span></a></h1>
          <h2>Services and Knowledge Exchange Money</h2>
        </div>
      </div>
";

if(isUserLoggedIn()) {
//Links for logged in user

    echo
	"<nav>
            <div id='menu_container'>
                <ul id='nav'>
                    <li><a href='account.php'>Account Home</a></li>";
	if (isUserReady($loggedInUser->user_id))
	{
		echo "          <li><a href='market.php'>Marketplace</a>";
   		if ($loggedInUser->checkPermission(array(1))){ //Links for permission level 1 (student)
   		echo "           <ul>
                        <li><a href='market.php'>Vos questions (seeker)</a></li>
                        <li><a href='market.php?cmd=bbuy'>Répondre aux questions (solver)</a></li>
                    </ul>";
   		}
   		echo "           </li>
                    <li><a href='wok.php'>WOC</a>";
   		if ($loggedInUser->checkPermission(array(1))){ //Links for permission level 1 (student)
  		echo " 
                    <ul>
                        <li><a href='wok.php'>Vos compétences</a></li>
                        <li><a href='wok.php?cmd=bsell'>Acheter une compétence</a></li>
                    </ul>";
   		}
   		echo "           </li>";
  		if ($loggedInUser->checkPermission(array(2))){ //Links for permission level 2 (professor)
      		echo "               <li><a href='pay.php'>Paiement</a></li>";
  	   		echo "               <li><a href='userlist.php'>Groupes</a></li>";
  		}
	}	
   	echo "           <li><a href='user_settings.php'>User Settings</a></li>
                    <li><a href='logout.php'>Logout</a></li>
                    </ul>
            </div>
        </nav>
        </header>
        ";

        echo '<div id="site_content">';
        $text = "<h3>".$loggedInUser->displayname."</h3>".
         "<h5>".$loggedInUser->title." (".getRoleById($loggedInUser->role).")</h5>
             <h4>Liens utiles</h4>
             <ul>
             <li><a target='_blank' href='http://movilab.org/index.php?title=SKEMA_BS_:_Cours_de_Performance_Durable_-_M1_-_Ann%C3%A9e_2014-2015'>Movilab</a></li>
             </ul>";

        echo displaySideMenu($text);

	//Links for permission level 3 (default admin)
	if ($loggedInUser->checkPermission(array(3))){
	$text = '</div>
            <h3>Admin Menu</h3>'.
                "<ul>
	<li><a href='admin_configuration.php'>Admin Configuration</a></li>
	<li><a href='admin_users.php'>Admin Users</a></li>
	<li><a href='admin_permissions.php'>Admin Permissions</a></li>
	<li><a href='admin_pages.php'>Admin Pages</a></li>
	<li><a href='admin_init.php'>Initialisation des comptes users</a></li>
	</ul>";

        echo displaySideMenu($text);


        }
} else {
//Links for users not logged in
    /*
    echo "
	<nav>
         <div id='menu_container'>
          <ul class='sf-menu' id='nav'>
	<li><a href='index.php'>Home</a></li>
	<li><a href='login.php'>Login</a></li>
	<li><a href='register.php'>Register</a></li>
	<li><a href='forgot-password.php'>Forgot Password</a></li>";
	if ($emailActivation)
	{
            echo "<li><a href='resend-activation.php'>Resend Activation Email</a></li>";
	}
	echo "</ul>
                    </div>
        </nav>
        </header>";

*/
    echo "
	<nav>
         <div id='menu_container'>
          <ul class='sf-menu' id='nav'>
	<li><a href='index.php'>Home</a></li>
	<li><a href='forgot-password.php'>Forgot Password</a></li>";
	echo "</ul>
                    </div>
        </nav>
        </header>";

        echo '<div id="site_content">
      <div id="sidebar_container">
        <img class="paperclip" src="images/paperclip.png" alt="paperclip" />
        <div class="sidebar">'.
         "<h3>".$loggedInUser->displayname."</h3>".
         "<h3>".$loggedInUser->title."</h3>
             <h2>Liens utiles</h2>
             <ul>
             <li><a href='http://movilab.org/index.php?title=SKEMA_BS_:_Cours_de_Performance_Durable_-_M1_-_Ann%C3%A9e_2013-2014'>Movilab</a></li>
             </ul>".
         "<h2>Important</h2>
          <ul>
          <li>Au début du cours, chaque équipe dispose d'un compte crédité d'un montant de 10.000 SKEMs sur ce site.</li>
          <li>Ce compte se gère de manière totalement collective (un compte pour une équipe).</li>
          </ul>".
         "</div>
            ";
}

echo "</div>
<div class='content'>".
        '<img style="float: left; vertical-align: middle; margin: 0 10px 0 0;" src="images/examples.png" alt="examples" />
            <h1 style="margin: 15px 0 0 0;">'.$title.'</h1>';

}

function closePage()
{
    echo "</div>
        </div>
<footer>
<p>(c) Marc Augier 2013 | <a href='http://www.css3templates.co.uk'>design from css3templates.co.uk</a></p>
</footer>

</body>
</html>";

}

function displaySideMenu($t)
{
    return '<div id="sidebar_container">
        <img class="paperclip" src="images/paperclip.png" alt="paperclip" />
        <div class="sidebar">'.$t."</div>";
}

function title($icon, $title)
{
    echo '<img style="float: left; vertical-align: middle; margin: 0 10px 0 0;" src="images/'.$icon.'.png" alt="examples" />
            <h1 style="margin: 15px 0 0 0;">'.$title.'</h1>';

}
function accountBrowse($userId)
{
    global $mysqli;


    /* Requête "Select" retourne un jeu de résultats */
    if ($result = mysqli_query($mysqli, "SELECT * FROM account WHERE account1 = '$userId'")) {
	$nbLignes = mysqli_num_rows($result);

        if ($nbLignes > 0){
		$amount = 0;
		echo "<p><table style='width:100%; border-spacing:0;'>".
				  "<tr><th>Date</th><th>Débit</th><th>Crédit</th><th width='50%'>Comment</th></tr>";
		/* fetch associative array */
                while (list($id, $account, $accountFrom, $debit, $credit, $comment, $date)  = mysqli_fetch_row($result)) {
			echo "<tr><td>$date</td><td>".number_format($debit)."</td><td>".number_format($credit)."</td><td>$comment</td></tr>";
			$amount += $credit-$debit;
                }

                echo "<tr><th></th><th><b>Solde Final : </b></th><th><b>".number_format($amount, 2, ',', ' ')."</b></th><th></th></tr>";

                // Notation anglaise (par défaut)
                // Notation française
                //$nombre_format_francais = number_format($number, 2, ',', ' ');
		echo "</table>";
          }

            /* Libération du jeu de résultats */
            mysqli_free_result($result);
        }
}

function selectPresta()
{
    global $mysqli;

    $tmp = "";

    $result = mysqli_query($mysqli, "SELECT id, prestation_name FROM prestation");
    $tmp .=  "
        <p><span>Prestation :</span><select name='prestation'>";
    while (list($id, $prestation)  = mysqli_fetch_row($result)) {
        $tmp .= "\n<option value='$id'>$prestation</option>";
    }
    $tmp .= "</select></p>";

    return $tmp;
}

function sortLink($cmd, $label)
{
 return "<a href='".$_SERVER['PHP_SELF']."?sort=$cmd'>$label</a>";
}

function cleanThisString($t)
{
	$t = htmlentities($t, ENT_QUOTES, "UTF-8");
	$t = strip_tags($t);

	return $t;
}


function listeMarketPlace($cmd,$type,$sqlTmp)
{
    global $mysqli, $loggedInUser;


    
    $flagSelf = ((substr($cmd,0, 1) == 'y')? true : false);    	// On détermine si on affiche les données de l'équipe ($flagself true) ou des autres équipes ($flagself false)
	$userIsBanker = (($loggedInUser->role == 6)? true : false);	

    // On affiche les données de la table Market pour $type
    $sql = "SELECT M.id, display_name, email, titre, description, price, timestamp, prestation_name 
            FROM market M, sk_users U, prestation P 
            WHERE P.id = prestation_id AND U.id = M.user_id AND type = '$type'$sqlTmp";
               
//debug    echo "<br/>$sql";
    if ($result = mysqli_query($mysqli, $sql))
    {
        $nbLignes = mysqli_num_rows($result);
        
        if ($nbLignes > 0)
        {
            echo "<table>".
             "<tr><th>".sortLink("equipe&cmd=$cmd","Qui")."</th><th>".sortLink("titre&cmd=$cmd","Quoi")."</th><th>".sortLink("desc&cmd=$cmd","Description")."</th><th>".sortLink("presta&cmd=$cmd","Prestation")."</th><th>".sortLink("price&cmd=$cmd","Prix")."</th><th>".sortLink("time&cmd=$cmd","Date dépot")."</th><th></th></tr>";

            while (list($idMarket, $idUser, $email, $titre, $description, $price, $timestamp, $prestation)  = mysqli_fetch_row($result))
            {
                $nbLignesDetail = countMarketReply($idMarket);
                $iconDetail = iconMarketReply($idMarket, "");
                if ($flagSelf)
                {
                    // On fait la liste des $type postés par l'équipe connectée
                    // On vérifie d'abord si elle a reçu des réponses
                    if ($nbLignesDetail > 0)
                    {
                        $tmp = "<a href='".$_SERVER['PHP_SELF']."?cmd=Rdetail&id=$idMarket'>($nbLignesDetail) $iconDetail </a>";
                    } else {
                        // pas de réponse, on peut l'efffacer
                        $tmp = "<a href='".$_SERVER['PHP_SELF']."?cmd=delete&id=$idMarket'><img src='images/delete.png'></a>";
                    }
                } else {
                    // On fait la liste des $type postés par les autres équipes
                    if ($userIsBanker)
	                    $tmp = "<a href='".$_SERVER['PHP_SELF']."?cmd=detail&id=$idMarket'>".iconMarketReply($idMarket, $loggedInUser->user_id)."</a>";
	                else
	                    $tmp = "<img src='images/lock.png'>";
                }
                echo "<tr><td><a href='mailto:$email'>$idUser</a></td><td>$titre</td><td>$description</td><td>$prestation</td><td>$price</td><td>$timestamp</td><td>$tmp</td></tr>";
            }
            echo "</table>";

        } else {
            if ($flagSelf)
                echo "<h3>Vous n'avez encore rien déposé</h3>";
            else {
                echo "<h3>Il n'y a pas encore de demande des autres équipes</h3>";
            }
        }
   }
}

function iconMarketReply($marketId, $userId){
    global $mysqli;
    
    if ($userId != ""){
        $sql1 = "SELECT * FROM market WHERE market_id = '$marketId' and user_id='$userId'";
        $sql2 = "SELECT * FROM market WHERE market_id = '$marketId' and user_id='$userId' AND type != 'Payed'";
    } else {
        $sql1 = "SELECT * FROM market WHERE market_id = '$marketId'";
        $sql2 = "SELECT * FROM market WHERE market_id = '$marketId' AND type != 'Payed'";
    }
    $detail =  mysqli_query($mysqli, $sql1);
    $nbLignesDetail = mysqli_num_rows($detail);

    if ($nbLignesDetail > 0)
    {
        $detail =  mysqli_query($mysqli, $sql2);
        $nbLignesDetail = mysqli_num_rows($detail);

        if ($nbLignesDetail > 0){
            return "<img src='images/add.png'>";
        } else {            
            return "<img src='images/accept.png'>";
        }
    } else {
        return "<img src='images/loupe.png'>";
    }
}


function countMarketReply($marketId){
    global $mysqli;
    
    $sql = "SELECT * FROM market WHERE market_id = '$marketId'";
    
    $detail =  mysqli_query($mysqli, $sql);
    $nbLignesDetail = mysqli_num_rows($detail);

    return $nbLignesDetail;
}

function countMyMarketReply($marketId){
    global $mysqli, $loggedInUser;
    
    $userId =  $loggedInUser->user_id;
    
    $sql = "SELECT * FROM market WHERE market_id = '$marketId' and user_id='$userId'";
    
    $detail =  mysqli_query($mysqli, $sql);
    $nbLignesDetail = mysqli_num_rows($detail);

    return $nbLignesDetail;
}

/* = = = = = = = = = = = = = = = = = = = = = = *
    Decoding functions
 * = = = = = = = = = = = = = = = = = = = = = = */
function getTeamById($id)
{
    global $mysqli;

	$result = mysqli_query($mysqli,"SELECT teamName FROM team WHERE id='$id'");
	
	if (list($name) = mysqli_fetch_row($result))
		return $name." ($id)";
	else
		return "$id";}


function getRoleById($id)
{
    global $mysqli;

	$result = mysqli_query($mysqli,"SELECT role FROM role WHERE id='$id'");
	
	if (list($name) = mysqli_fetch_row($result))
		return $name;
	else
		return "No role defined";}

function getCampusById($id)
{
    global $mysqli;

	$result = mysqli_query($mysqli,"SELECT campusName FROM campus WHERE id='$id'");
	
	if (list($name) = mysqli_fetch_row($result))
		return $name;
	else
		return false;}

function isUserReady($id)
{
    global $mysqli;

	$result = mysqli_query($mysqli,"SELECT roleId FROM sk_users WHERE id='$id'");
	list($name) = mysqli_fetch_row($result);

	if ($name > 0)
	{
		$result = mysqli_query($mysqli,"SELECT teamId FROM sk_users WHERE id='$id'");
		list($name) = mysqli_fetch_row($result);
		
		if ($name > 0)
		{
			$result = mysqli_query($mysqli,"SELECT campusId FROM sk_users WHERE id='$id'");
			list($name) = mysqli_fetch_row($result);
			
			if ($name > 0)
			{
				return true;
			} else
			{
				return false;
			}		} else
		{
			return false;
		}
	} else
	{
		return false;
	}
}

function getUserFromMarket($id)
{
    global $mysqli;

    $result = mysqli_query($mysqli, "SELECT user_id FROM market WHERE id = '$id'");    
    list($user_id) = mysqli_fetch_row($result);
    
    return $user_id;
}

function browseMyTeam($id)
{
	global $mysqli;
	    
    echo "<img style='float: left; vertical-align: middle; margin: 0 10px 0 0;' src='images/examples.png' alt='Information' />
           <h1 style='margin: 15px 0 0 0;'>Your team is #$id</h1><p>
           <table>
           <tr><th>Nom</th><th>Role</th></tr>";

    $result = mysqli_query($mysqli, "SELECT `display_name`, R.role FROM `sk_users` U, role R WHERE teamId = '$id' and U.roleId = R.id ORDER BY display_name");
    
    while (list($user, $role) = mysqli_fetch_row($result))
	{
        echo "<tr><td>$user</td><td>$role</td></tr>";
    }
        
    echo "
    </table>";
	
}

function afficheDetails($id, $type)
{
    global $mysqli, $loggedInUser;

    $result = mysqli_query($mysqli, "SELECT M.id, display_name, email, titre, description, timestamp, prestation_name, price 
                                       FROM market M, sk_users U, prestation P WHERE M.id = '$id' AND U.id = M.user_id");

    echo "<table>";
    
    list($idMarket, $idUser, $email, $titre, $description, $timestamp, $prestation, $price) = mysqli_fetch_row($result);
    if ($type == 'M') {
        echo "<tr><td colspan='2'>Demande de <b>$prestation</b> déposée par l'équipe $idUser (<a href='mailto:$email'>$email</a>) pour un montant de ".number_format($price)." SKEMs.</td></tr>";
    } else if ($type == 'W') {
        echo "<tr><td colspan='2'>Proposition de <b>$prestation</b> déposée par l'équipe $idUser (<a href='mailto:$email'>$email</a>) pour un montant de ".number_format($price)." SKEMs.</td></tr>";
    }
        
    echo "<tr><td>Titre :</td><td>$titre</td></tr>
    <tr><td>Description :</td><td>$description</td></tr>
    <tr><td>Date de dépot :</td><td>$timestamp</td></tr>
    </table>";
}

function afficheAccept($id1, $id2)
{
    global $mysqli, $loggedInUser;

    $result1 = mysqli_query($mysqli, "SELECT display_name, email, titre, description, timestamp, prestation_name, price 
                                       FROM market M, sk_users U, prestation P WHERE M.id = '$id1' AND U.id = M.user_id");

    $result2 = mysqli_query($mysqli, "SELECT U.id, display_name, email, titre, description, timestamp, prestation_name, price 
                                       FROM market M, sk_users U, prestation P WHERE M.id = '$id2' AND U.id = M.user_id");
    echo "<table>
        <tr><td>
        <h2>Votre demande</h2>
        <table>";
    list($nameUser, $email, $titre, $description, $timestamp, $prestation, $price) = mysqli_fetch_row($result1);
    echo "<tr><td>Equipe :</td><td>$nameUser <a href='mailto:$email'>$email</a></td></tr>
    <tr><td>Titre :</td><td>$titre</td></tr>
    <tr><td>Description :</td><td>$description</td></tr>
    <tr><td>Type :</td><td>$prestation</td></tr>
    <tr><td>Prix :</td><td>$price</td></tr>
    <tr><td>Date de dépot :</td><td>$timestamp</td></tr>
    </table>
    </td>
    <td>";
    
    echo "<h2>La réponse sélectionnée</h2>
          <table>";
    list($idUser, $nameUser, $email, $titre, $description, $timestamp, $prestation, $price) = mysqli_fetch_row($result2);
    echo "<tr><td>Equipe :</td><td>$nameUser <a href='mailto:$email'>$email</a></td></tr>
    <tr><td>Titre :</td><td>$titre</td></tr>
    <tr><td>Description :</td><td>$description</td></tr>
    <tr><td>Type :</td><td>$prestation</td></tr>
    <tr><td>Prix :</td><td>$price</td></tr>
    <tr><td>Date de dépot :</td><td>$timestamp</td></tr>
    </table>";
    echo "    </td>
        </tr>
        </table>";
    echo "<h2>Paiement</h2>
    <form name='deposit' action='pay.php' method='post'>
    <div class='form_settings'>
    <span>Vous payez à l'équipe $nameUser : </span><input type='text'name='deposit'>
            <input type='hidden'name='accountTo' value='$idUser'>
            <input type='hidden'name='prestation'value='$prestation'>
            <input type='hidden'name='marketId'value='$id2'>
    <span style='color:red'>Attention, cette somme sera immédiatement débitée de votre compte !</span>
    <p style='padding-top: 15px'>
    <input type='submit' name='button' value='Payer' class='submit'>
    </div>
    </form>";
}

function afficheReponse($id,$market)
{
    global $mysqli, $loggedInUser;

    // On vérifie si cette demande a reçu une réponse de ce user
    if (countMyMarketReply($_GET['id'])>0)
    {
        $sql = "SELECT M.id, type, display_name, titre, description, timestamp, price 
                FROM market M, sk_users U WHERE market_id = '".$_GET['id']."' AND user_id =U.id AND U.id = '".$loggedInUser->user_id."'";
        $detail =  mysqli_query($mysqli, $sql);
        list($id, $type, $idUser, $titre, $description, $timestamp, $price)  = mysqli_fetch_row($detail);
        
        echo "Vous avez déjà répondu le $timestamp";
//debug echo "<p>$id, *$type*, $idUser, $titre, $description, $timestamp, $price, $market</p>";
        if ($type == "Payed"){
            if ($market == "M"){
                echo ", votre offre a été acceptée et vous avez été payé";
            } else if ($market == "W"){
                echo ", vous avez déjà payé pour acheter cette offre de compétence";
            }
        } else {
            echo ", votre offre n'a pas encore reçu de réponse";            
        }
        echo ".<br/>";
        echo "<table><tr><td>$titre</td></tr><tr><td>$description</td></tr><tr><td>".number_format ($price)." SKEMs </td></tr></table>";                
    } else {
        if ($market == 'M'){
            echo "Vous pouvez y répondre directement à l'aide du formulaire suivant.<br/>
                <form name='sell' action='".$_SERVER['PHP_SELF']."' method='post'>
                  <div class='form_settings'>";

            echo '<span>Titre : </span><input type="text" name="titre" value="" /><br/>
            <span>Description : </span><textarea rows="8" cols="50" name="description"></textarea><br/>
            <span>Prix : </span><input type="text" name="price" value="" /><br/>
            <input type="hidden" name="id" value="'.$id.'" />
            <input type="hidden" name="prestationId" value="NULL" />'.
            "<p style='padding-top: 15px'>
            <input type='submit' name='cmd' value='Repondre' class='submit' />
            </div></form></div>";
        }else if ($market == 'W'){
            $idUserWOC = getUserFromMarket($id);
            echo "<p>Une fois que vous êtes d'accord sur les modalités de la prestation, vous devez payer l'autre équipe à l'aide du formulaire ci-dessous</p>";

            echo "<form name='deposit' action='pay.php' method='post'>
            <div class='form_settings'>";
            
            echo "<span>Titre : </span><input type='text' name='titre' value='' /><br/>
            <span>Description : </span><textarea rows='8' cols='50' name='description'></textarea><br/>
            <input type='hidden' name='id' value='$id' />
            <input type='hidden' name='prestation' value='NULL' />
            <span>Vous payez à l'équipe $nameUser : </span><input type='text'name='deposit'>
            <input type='hidden'name='accountTo' value='$idUserWOC'>
            <input type='hidden'name='marketId'value='$id'>
            <input type='hidden'name='marketPlace'value='WOC'>
            <span style='color:red'>Attention, cette somme sera immédiatement débitée de votre compte !</span>
            <p style='padding-top: 15px'>
            <input type='submit' name='button' value='Payer' class='submit'>
            </div>
            </form>";
        }
    }
}

function insertMarketTransaction()
    {
        global $mysqli, $idUser;
        
//debug        print_r($_POST);

        $type         = $_POST["cmd"];
	$titre        = cleanThisString(trim($_POST["titre"]));
	$description  = cleanThisString(trim($_POST["description"]));
	$prestationId = trim($_POST["prestation"]);
	$marketId     = trim($_POST["id"]);
	$price        = trim($_POST["price"]);
        
        $timeStamp    = date("Y-m-d H:i:s");
        
        if ($type == 'delete'){
            $sql = "DELETE FROM market WHERE id = '$marketId'";
        } else {
            $sql = "INSERT INTO `market` (`id`, `user_id`,`type`, `titre`, `description`, prestation_id, market_id, timestamp, price) 
            VALUES (NULL, '$idUser', '$type', '$titre', '$description', '$prestationId', '$marketId', '$timeStamp', '$price');";
        }
//debug      echo "<p>$sql";
//      die ();
        $result = mysqli_query($mysqli, $sql);
        
        // Pour enchainer on force la cmd suivante
        switch ($type)
        {
            case "Repondre":
                $_GET['cmd'] = "bsell";
            break;
            case "Proposer":
                $_GET['cmd'] = "ysell";
            break;
            case "Demander":
                $_GET['cmd'] = "ybuy";
            break;
        }
    }
function displayInputForm($type){

    echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>
          <div class='form_settings'>";
    echo '<p><span>Titre : </span><input type="text" name="titre" value="" /></p>
          <p><span>Description : </span><textarea rows="8" cols="50" name="description"></textarea></p>'.selectPresta().
         "</p><p><span>Prix proposé : </span><input type='text' name='price' value='' /></p>
          <p style='padding-top: 15px'>
          <input type='submit' name='cmd' value='$type' class='submit' />
          </div></form></div>";
}

function addProfessor($name,$display, $mail){
    global $mysqli;
    
    $result = mysqli_query($mysqli, "INSERT INTO `sk_users` (`id`, `user_name`, `display_name`, `password`, `email`, `activation_token`, `last_activation_request`, `lost_password_request`, `active`, `title`, `sign_up_stamp`, `last_sign_in_stamp`) VALUES (NULL, '$name', '$display', '9051a509f95691159c7ed617fd884f29af9213d747b13b6c7860fff6fb40cb24d', '$mail', 'b3f4ed2c42cc370d457f9caa201617a8', 1377894239, 0, 1, 'Professor', 1377894239, 1377898821);");
    $result = mysqli_query($mysqli,"SELECT id FROM `sk_users` WHERE user_name = '$name';");
    list($idNew)  = mysqli_fetch_row($result);
    $result = mysqli_query($mysqli, "INSERT INTO `sk_user_permission_matches` (`id`, `user_id`, `permission_id`) VALUES (NULL, '$idNew', '2');");    
}

function initUserBankAccount($id){
    global $mysqli;
    
    if (!mysqli_query($mysqli, "INSERT INTO `account` (`id`, `account1`, `account2`, `debit`, `credit`, `description`, `timestamp`, `prestation_Id`) VALUES (NULL, '$id', NULL, NULL, '10000', 'Initial Value', NOW(), NULL);"))
    {
	    printf("Erreur : %s\n", mysqli_error($mysqli));
    }
    
}

?>
