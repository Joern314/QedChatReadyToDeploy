<?php
	ignore_user_abort (true);

	set_error_handler ('ErrorHandler');

	function ErrorHandler ($number, $description, $file, $line)
	{
		if (error_reporting () & $number)
		{
			header ("HTTP/1.1 503 Service unavailable");

			global $recorded;
			if ($recorded)
				echo "Dein Post konnte zwar eingetragen, doch m�glicherweise nicht an die Clients gesendet werden!<br>";
			else
				echo "Dein Post konnte m�glicherweise nicht eingetragen werden!<br>";

			echo "Der Fehler mit Nummer $number trat in $file in Zeile $line auf.";
			if ($description != "")
				echo "<br>Beschreibung: $description";
			exit ();
		}
	}

	$recorded = false;

	require_once ("data.php");
	require_once ("common.php");

	/*if (SECURE_POSTS)
	{
		$sem = sem_get (SEM_SECURE_POSTS_KEY);
		sem_acquire ($sem);

		$authorized = true;
		if (!isset ($_POST["key"]))
			$authorized = false;
		else
			$authorized = is_key_valid ($_POST["key"]);

		sem_release ($sem);

		if (!$authorized)
		{
			header ("HTTP/1.1 401 Unauthorized");
			echo "Bot-Schutz ist aktiviert. Falls du einen Browser verwendest, lade erst die send.html und send.js (dort jeweils auf aktualisieren klicken) und dann den Chat und warte bis er vollst�ndig geladen hat.";
			die ();
		}
	}*/

	$post = array ("name" => demagicalize_string ($_POST["name"]),
				   "message" => demagicalize_string (@$_POST["message"]),
				   "ip" => getenv ("REMOTE_ADDR"),
				   "date" => date ("Y-m-d H-i-s"),
				   "delay" => ((!isset($_POST["delay"]) || !is_numeric ($_POST["delay"]) || ($_POST["delay"] < 0)) ? "NULL" : $_POST["delay"]),
				   'bottag' => (!empty($_REQUEST['bottag'])?1:0));


	if(strlen($post["message"])>10009)
		$post["message"]="zu lang";

	if (strpos($_SERVER["HTTP_USER_AGENT"],"Anonymouse"))
	{
	    exit();
	}


	if (strlen($post["message"])<1)
		exit();

	if (POST_LIMITS)
	{
    	if (strlen ($post["message"]) > POST_LIMITS_MAX_LENGTH)
    	    $valid = false;
    	else if (substr_count ($post["message"], "\n") >= POST_LIMITS_MAX_LINES)
    	    $valid = false;
        else
        {
            $whitespaces = array (' ', "\t", "\n");
            $offset = 0;

            for ($found = true; $found; )
            {
                $found = false;
                for ($i = 0; $i != count ($whitespaces); ++$i)
                {
                    $next = strpos ($post["message"], $whitespaces[$i], $offset);
                    if ($next !== false && $next - $offset - 1 <= POST_LIMITS_MAX_CONTIGUOUS_NWSP)
                    {
                        $offset = $next + 1;
                        $found = true;
                        break;
                    }
                }
            }

            $valid = (strlen ($post["message"]) - $offset <= POST_LIMITS_MAX_CONTIGUOS_NWSPS);
	 		if (!$valid)
			{
	    		header ("HTTP/1.1 403 Forbidden");
				echo "Aus Traffic-Gr�nden d�rfen Posts momentan nicht allzu gro� sein.<br>" . $offset . " " . strlen ($post["message"]);
				die ();
			}

        }

	}
	        mysql_pconnect (SQL_HOST, SQL_USER, SQL_PASSWORD);
                mysql_select_db (SQL_DATABASE);

		//Floodschutz
		$post['ip']=mysql_real_escape_string($post['ip']);
		$post['userid']=$userid;
		$IPhalb=explode('.',$post['ip']);
		$IPhalb=$IPhalb[0] . '.' . $IPhalb[1];
		if (mysql_result(mysql_query('SELECT COUNT(*) FROM flood WHERE DATE_SUB(NOW(),INTERVAL 5 SECOND) <= date AND (IP="'.$post['ip'].'" OR IPhalb="'.$post['ip'].'")'),0) >3 ) {
			header ("HTTP/1.1 403 Forbidden");
			echo "Floodschutz aktiv - WARNUNG, bei weiteren Versuchen wird DAUERHAFT gebannt!!!";
			die ();
		}
		mysql_query('INSERT INTO flood SET date=NOW(), IP="'.$post['ip'].'", IPhalb="'.$IPhalb.'"');

		//schon in whitelist?
		if (!mysql_result(mysql_query('SELECT COUNT(*) FROM getestet WHERE IP="'.mysql_real_escape_string($post['ip']).'"'),0)) {
			//in der blacklist?
			$gebannt=@mysql_result(mysql_query('SELECT grund FROM blacklist WHERE "'.mysql_real_escape_string($post['ip']).'" like IP'),0);

			if ($gebannt)
			{
		    		header ("HTTP/1.1 403 Forbidden");
				echo "IP gebannt, Grund: $gebannt<br>";
				die ();
			}
			//proxytest
			require_once('proxytest.php');
			$obj_proxytest = new proxytest();
			$result = $obj_proxytest->proxy_test($post['ip']);
			if ($result) {
				mysql_query('INSERT INTO blacklist SET zeit=NOW(), grund="proxytest", IP="'.mysql_real_escape_string($post['ip']).'"');
		    		header ("HTTP/1.1 403 Forbidden");
				echo "IP wurde gerade gebannt<br>";
				die ();

			}
			//alles ok
			mysql_query('INSERT INTO getestet SET zeit=NOW(), IP="'.mysql_real_escape_string($post['ip']).'"');

		}

	do_post ($post);
?>

