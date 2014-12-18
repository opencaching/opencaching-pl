<?php

/*

  Table structure for voting

  CREATE TABLE IF NOT EXISTS `rr_ocpl_candidates_2014` (
  `candidate_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `username` varchar(60) COLLATE utf8_polish_ci NOT NULL,
  `city` varchar(60) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`candidate_id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `username` (`username`)
  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;


  CREATE TABLE IF NOT EXISTS `rr_ocpl_vote_2014` (
  `vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  PRIMARY KEY (`vote_id`)
  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

 */

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

$tplname = 'glosowanie_do_rr';
$tpl_subtitle = 'Głosowanie do Rady Rejsu 2014';

//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        require($stylepath . '/glosowanie_do_rr.inc.php');

        if ((time() >= $vote_time_start) && (time() <= $vote_time_end)) { // data ok - można głosować
            // sprawdzanie kiedy użytkownik utworzył konto
            $query = "SELECT * FROM user WHERE user_id='" . sql_escape($usr['userid']) . "' AND date_created < '" . sql_escape(date('Y-m-d H:i:s', $user_max_date_created)) . "'";
            $rs = sql($query);
            $user_num_rows = mysql_num_rows($rs);
            mysql_free_result($rs);

            // sprawdzenie ile użytkownik ma znalezionych skrzynek
            $query = "SELECT COUNT( * ) founds_count FROM cache_logs WHERE user_id = " . sql_escape($usr['userid']) . " AND TYPE = 1 AND deleted = 0 AND date_created < '" . sql_escape(date('Y-m-d H:i:s', $user_found_caches_date)) . "'";
            $rs = sql($query);
            $rec1 = sql_fetch_array($rs);
            $num_find_caches = $rec1['founds_count'];
            mysql_free_result($rs);

            if (($user_num_rows == 1) && ($num_find_caches >= $user_min_found_caches)) { // użytkownik uprawiony do głosowania
                // sprawdzanie czy użytkownik już głosował
                $query = "SELECT * FROM rr_ocpl_vote_2014 WHERE user_id='" . sql_escape($usr['userid']) . "'";
                $rs = sql($query);
                $vote_num_rows = mysql_num_rows($rs);
                mysql_free_result($rs);

                if ($vote_num_rows == 0) { // użytkownik nie głosował
                    if (isset($_POST['glosowanie']) && ($_POST['glosowanie'] == 1)) { //został wysłany POST z głosami
                        $votes = array();

                        $query = "SELECT candidate_id FROM rr_ocpl_candidates_2014 ORDER BY username";
                        $rs = sql($query);
                        for ($i = 0; $i < mysql_num_rows($rs); $i++) {
                            $record = sql_fetch_array($rs);
                            $checkbox_name = 'candidate' . $record['candidate_id'];

                            if ((isset($_POST[$checkbox_name])) && ($_POST[$checkbox_name] == 1)) {
                                $votes[] = $record['candidate_id'];
                            }
                        }
                        mysql_free_result($rs);

                        if (count($votes) > 0 && count($votes) < 6) { //zapisz głos
                            $tplname = 'glosowanie_do_rr_wyniki';
                            tpl_set_var('vote_info', $vote_info);
                            tpl_set_var('information', $just_voted);
                            tpl_set_var('vote_results_header', '');
                            tpl_set_var('vote_results_list', '');
                            tpl_set_var('vote_results_foother', '');

                            for ($i = 0; $i < count($votes); $i++) {
                                sql('INSERT INTO `rr_ocpl_vote_2014` (`user_id`, `candidate_id`) VALUES (\'' . sql_escape($usr['userid']) . '\', \'' . sql_escape($votes[$i]) . '\')');
                            }
                        } else { // błędna ilość głosów
                            $tplname = 'glosowanie_do_rr';
                            tpl_set_var('vote_warning', $vote_warning);
                            tpl_set_var('vote_info', $vote_info);
                            tpl_set_var('candidate_vote_list', show_candidate_vote_list($candidate_vote_line));
                        }
                    } else { // standardowy formularz głosowania
                        $tplname = 'glosowanie_do_rr';
                        tpl_set_var('vote_warning', '');
                        tpl_set_var('vote_info', $vote_info);
                        tpl_set_var('candidate_vote_list', show_candidate_vote_list($candidate_vote_line));
                    }
                } else { // użytkownik już głosował
                    $tplname = 'glosowanie_do_rr_wyniki';
                    tpl_set_var('vote_info', $vote_info);
                    tpl_set_var('information', $already_voted);
                    tpl_set_var('vote_results_header', '');
                    tpl_set_var('vote_results_list', '');
                    tpl_set_var('vote_results_foother', '');
                }
            } else { // uzytkownik nieuprawniony do glosowania (zarejestrowany po ogloszeniu lub za mało znalezionych skrzynek)
                $tplname = 'glosowanie_do_rr_info';
                tpl_set_var('vote_info', $vote_info);
                tpl_set_var('candidate_info_list', show_candidate_info_list($candidate_info_line));

                $information = $vote_forbidden;
                if (!($user_num_rows == 1)) {
                    $information .= sprintf($vote_forbidden_wrong_date, date('d-m-Y', $user_max_date_created));
                }
                if (!($num_find_caches >= $user_min_found_caches)) {
                    $information .= sprintf($vote_forbidden_cache_count, date('d-m-Y', $user_found_caches_date), $num_find_caches, $user_min_found_caches);
                }
                tpl_set_var('information', $information);
            }
        } else { // głosowanie się jeszcze nie rozpoczęło lub już się zakończyło
            if (time() < $vote_time_start) { // głosowanie się jeszcze nie rozpoczęło
                $tplname = 'glosowanie_do_rr_info';
                tpl_set_var('vote_info', $vote_info);
                tpl_set_var('candidate_info_list', show_candidate_info_list($candidate_info_line));
                tpl_set_var('information', $vote_not_start);
            } else if (time() > $vote_time_end) { // głosowanie się już zakończyło - prezentacja wyników
                $tplname = 'glosowanie_do_rr_wyniki';
                tpl_set_var('vote_info', $vote_info);
                tpl_set_var('information', $vote_ended);
                tpl_set_var('vote_results_header', $vote_results_header);
                tpl_set_var('vote_results_list', show_candidate_result_list($candidate_result_line));
                tpl_set_var('vote_results_foother', $vote_results_foother);
            }
        }
    }
}

tpl_BuildTemplate();

function show_candidate_vote_list($candidate_vote_line)
{
    $candidate_vote_list = '';
    $query = "SELECT * FROM rr_ocpl_candidates_2014 ORDER BY username";
    $rs = sql($query);
    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $record = sql_fetch_array($rs);
        $tmp_record = $candidate_vote_line;
        $tmp_record = mb_ereg_replace('{candidate_id}', htmlspecialchars($record['candidate_id'], ENT_COMPAT, 'UTF-8'), $tmp_record);
        $tmp_record = mb_ereg_replace('{username}', $record['username'], $tmp_record);
        $tmp_record = mb_ereg_replace('{city}', $record['city'], $tmp_record);
        $tmp_record = mb_ereg_replace('{user_id}', htmlspecialchars($record['user_id'], ENT_COMPAT, 'UTF-8'), $tmp_record);
        $checkbox_name = 'candidate' . $record['candidate_id'];
        if ((isset($_POST[$checkbox_name])) && ($_POST[$checkbox_name] == 1)) {
            $tmp_record = mb_ereg_replace('{checked}', 'checked="checked" ', $tmp_record);
        } else {
            $tmp_record = mb_ereg_replace('{checked}', '', $tmp_record);
        }
        $candidate_vote_list .= "\n" . $tmp_record;
    }
    mysql_free_result($rs);
    return $candidate_vote_list;
}

function show_candidate_info_list($candidate_info_line)
{
    $candidate_info_list = '';
    $query = "SELECT * FROM rr_ocpl_candidates_2014 ORDER BY username";
    $rs = sql($query);
    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $record = sql_fetch_array($rs);
        $tmp_record = $candidate_info_line;
        $tmp_record = mb_ereg_replace('{candidate_id}', htmlspecialchars($record['candidate_id'], ENT_COMPAT, 'UTF-8'), $tmp_record);
        $tmp_record = mb_ereg_replace('{username}', $record['username'], $tmp_record);
        $tmp_record = mb_ereg_replace('{city}', $record['city'], $tmp_record);
        $tmp_record = mb_ereg_replace('{user_id}', htmlspecialchars($record['user_id'], ENT_COMPAT, 'UTF-8'), $tmp_record);
        $candidate_info_list .= "\n" . $tmp_record;
    }
    mysql_free_result($rs);
    return $candidate_info_list;
}

function show_candidate_result_list($candidate_result_line)
{
    $candidate_result_list = '';
    //$query = "SELECT count(v.candidate_id), v.candidate_id, c.username, c.city, c.user_id FROM rr_ocpl_candidates_2014 c, rr_ocpl_vote_2014 v WHERE v.candidate_id = c.candidate_id GROUP BY v.candidate_id ORDER BY count(v.candidate_id), c.username";
    $query = "SELECT count(v.candidate_id) as ilosc, c.username, c.city, c.user_id FROM rr_ocpl_candidates_2014 c LEFT JOIN rr_ocpl_vote_2014 v ON c.candidate_id = v.candidate_id GROUP BY c.username ORDER BY count(v.candidate_id) DESC, c.username";
    $rs = sql($query);
    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $record = sql_fetch_array($rs);
        $tmp_record = $candidate_result_line;
        $tmp_record = mb_ereg_replace('{candidate_id}', htmlspecialchars($record['candidate_id'], ENT_COMPAT, 'UTF-8'), $tmp_record);
        $tmp_record = mb_ereg_replace('{username}', $record['username'], $tmp_record);
        $tmp_record = mb_ereg_replace('{city}', $record['city'], $tmp_record);
        $tmp_record = mb_ereg_replace('{user_id}', htmlspecialchars($record['user_id'], ENT_COMPAT, 'UTF-8'), $tmp_record);
        $tmp_record = mb_ereg_replace('{ilosc}', htmlspecialchars($record['ilosc'], ENT_COMPAT, 'UTF-8'), $tmp_record);
        $candidate_result_list .= "\n" . $tmp_record;
    }
    mysql_free_result($rs);
    return $candidate_result_list;
}

?>
