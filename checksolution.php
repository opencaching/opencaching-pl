<?php


/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************

   Unicode Reminder ¹œæ³

*/

  //prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	require($stylepath.'/smilies.inc.php');

	function getUserSolvedLog($cache_id)
	{
		global $usr;
		
		$log_id = 0;
		$rs = sql("SELECT `id` FROM `cache_logs` WHERE `cache_id` = &1 AND `user_id` = &2 AND `type` = 9", $cache_id, $usr['userid']);

		if ($r = sql_fetch_assoc($rs))
		{
			$log_id = $r['id'];
		}

		mysql_free_result($rs);

		return $log_id;
	}

	//Preprocessing
	if ($error == false)
	{
		//user logged in?
		if ($usr == false)
		{
			$target = urlencode(tpl_get_current_page());
			tpl_redirect('login.php?target='.$target);
		}
		else
		{
			$log_id = 0;

			if (isset($_REQUEST['logid']))
			{
				$log_id = $_REQUEST['logid'];
			}
			else
			{
				$cacheid = $_GET['cacheid'];
				$solution = $_GET['solution'];
				$solution_rs = sql("SELECT `solution`, `status` FROM `caches` WHERE `cache_id` = &1", $cacheid);

				if (mysql_num_rows($solution_rs) > 0)
				{
					$solution_r = sql_fetch_array($solution_rs);

					if ($solution_r['status'] != 4 && $solution_r['status'] != 5 && $solution_r['status'] != 6)
					{
						$is_solved = $solution_r['solution'] == $solution;

						if ($is_solved)
						{
							$log_type = 9;
							$solved_column = 'solved';
							tpl_set_var('solution_status', tr('correct_solution'));
						}
						else
						{
							$log_type = 10;
							$solved_column = 'not_solved';
							tpl_set_var('solution_status', tr('incorrect_solution'));
						}

						$log_id = getUserSolvedLog($cacheid);

						if ($is_solved && $log_id > 0)
						{
							sql("UPDATE `cache_logs` SET `date`=NOW(), `last_modified`=NOW(), `text`=NULL, `text_html`=1, `text_htmledit`=1, `deleted`=0, `hidden`=1 WHERE id = &1", $log_id);
						}
						else
						{
							sql("UPDATE `caches` SET `&1` = `&1` + 1 WHERE `cache_id` = &2", $solved_column, $cacheid);

							$log_uuid = create_uuid();
							sql("INSERT INTO `cache_logs` (`id`, `cache_id`, `user_id`, `type`, `date`, `date_created`, `last_modified`, `uuid`, `node`, `text_html`, `text_htmledit`, `hidden`)
									VALUES ('', '&1', '&2', '&3', NOW(), NOW(), NOW(), '&4', '&5', 1, 1, 1)",
									$cacheid, $usr['userid'], $log_type, $log_uuid, $oc_nodeid);
							
							$log_rs = sql("SELECT id FROM cache_logs WHERE uuid = '&1'", $log_uuid);

							if ($log_record = sql_fetch_assoc($log_rs))
							{
								$log_id = $log_record['id'];
							}

							mysql_free_result($log_rs);
						}
					}
				}
			}

			if ($log_id > 0)
			{
				$log_rs = sql("SELECT `cache_logs`.`cache_id` AS `cache_id`, `cache_logs`.`node` AS `node`, `cache_logs`.`type` AS `logtype`, `caches`.`name` AS `cachename`, `caches`.`type` AS `cachetype`, `caches`.`user_id` AS `cache_user_id` FROM `cache_logs` INNER JOIN `caches` ON (`caches`.`cache_id`=`cache_logs`.`cache_id`) WHERE `id`='&1' AND `deleted` = &2", $log_id, 0);

				if (mysql_num_rows($log_rs) > 0)
				{
					$log_record = sql_fetch_array($log_rs);
					require($stylepath . '/checksolution.inc.php');
					require_once($rootpath . 'lib/caches.inc.php');

					if ($log_record['node'] != $oc_nodeid)
					{
						tpl_errorMsg('checksolution', $error_wrong_node);
						exit;
					}

					$tplname = 'checksolution';

					//load settings
					$cache_name = $log_record['cachename'];
					$cache_type = $log_record['cachetype'];
					$cache_user_id = $log_record['cache_user_id'];

					if (isset($_POST['descMode']))
					{
						$descMode = $_POST['descMode'] + 0;

						if ($descMode < 1 || $descMode > 3)
							$descMode = 3;
					}
					else
					{
						$descMode = 3;
					}

					if ($descMode != 1)
					{
						// Text from textarea
						$log_text = isset($_POST['logtext']) ? ($_POST['logtext']) : '';

						// check input
						require_once($rootpath . 'lib/class.inputfilter.php');
						$myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
						$log_text = $myFilter->process($log_text);
					}
					else
					{
						// escape text
						$log_text = isset($_POST['logtext']) ? htmlspecialchars($_POST['logtext'], ENT_COMPAT, 'UTF-8') : '';
					}

					if (isset($_POST['submitform']))
					{
						sql("UPDATE `cache_logs` SET `text`='&1', `text_html`='&2', `text_htmledit`='&3', `last_modified`=NOW(), `hidden`=0
							WHERE `id`='&4'",
							tidy_html_description((($descMode != 1) ? $log_text : nl2br($log_text))), (($descMode != 1) ? 1 : 0), (($descMode == 3) ? 1 : 0), $log_id);

						require_once($rootpath . 'lib/eventhandler.inc.php');
						event_change_log_type($log_record['cache_id'], $usr['userid']);
					}

					if (isset($_POST['submitform']) || isset($_POST['cancelform']))
					{
						tpl_redirect('viewcache.php?cacheid=' . urlencode($log_record['cache_id']));
						exit;
					}

					//set template vars
					tpl_set_var('cachename', htmlspecialchars($cache_name, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('cachename', htmlspecialchars($cache_name, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('cacheid', $log_record['cache_id']);
					tpl_set_var('logid', $log_id);

					$log_text = tidy_html_description($log_text);

					if ($descMode != 1)
						tpl_set_var('logtext', htmlspecialchars($log_text, ENT_NOQUOTES, 'UTF-8'), true);
					else
						tpl_set_var('logtext', $log_text);

					// Text / normal HTML / HTML editor
					tpl_set_var('use_tinymce', (($descMode == 3) ? 1 : 0));

					if ($descMode == 1)
						tpl_set_var('descMode', 1);
					else if ($descMode == 2)
						tpl_set_var('descMode', 2);
					else
					{
						// TinyMCE
						$headers = tpl_get_var('htmlheaders') . "\n";
						$headers .= '<script language="javascript" type="text/javascript" src="lib/phpfuncs.js"></script>' . "\n";
						$headers .= '<script language="javascript" type="text/javascript" src="lib/tinymce/tiny_mce.js"></script>' . "\n";
						$headers .= '<script language="javascript" type="text/javascript" src="lib/tinymce/config/log.js.php?lang='.$lang.'&amp;dateformat='.$dateformat.'&amp;logid=0"></script>' . "\n";
						tpl_set_var('htmlheaders', $headers);
						tpl_set_var('descMode', 3);
					}

					// build smilies
					$smilies = '';
					if ($descMode != 3)
					{
						for($i=0; $i<count($smileyshow); $i++)
						{
							if($smileyshow[$i] == '1')
							{
								$tmp_smiley = $smiley_link;
								$tmp_smiley = mb_ereg_replace('{smiley_image}', $smileyimage[$i], $tmp_smiley);
								$smilies = $smilies.mb_ereg_replace('{smiley_text}', ' '.$smileytext[$i].' ', $tmp_smiley).'&nbsp;';
							}
						}
					}
					tpl_set_var('smilies', $smilies);
				}
				else
				{
					// no such log or log marked as deleted
					header('HTTP/1.0 404 not found');
					include('./error_pages/404.html');
					exit();
				}
			}
			else
			{
				header('Location: viewcache.php?cacheid='.$cacheid);
			}
		}
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>
