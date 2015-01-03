<?php
    $user_max_date_created = strtotime("2014-10-17 00:00:00");
    $user_min_found_caches = 10;
    $user_found_caches_date = strtotime("2014-12-14 00:00:00");

    $vote_time_start = strtotime("2014-12-14 00:00:00");
    $vote_time_end = strtotime("2014-12-21 23:59:59");

// Do testów
// $user_max_date_created = strtotime("2007-04-14 00:00:00");
// $user_min_found_caches = 1000;
// $vote_time_start = strtotime("2014-05-27 00:00:00");
// $vote_time_end = strtotime("2014-05-27 21:25:00");

    $just_voted = "<br /><b>Twój głos został zapisany. Dziękujemy za udział w głosowaniu. Wyniki głosowania zostaną opublikowane po zakończeniu głosowania (tj. po godz. 23:59:59, 21 grudnia 2014 roku).</b>";
    $already_voted = "<br /><b>Oddałeś już swój głos na kandydatów. Wyniki głosowania zostaną opublikowane po zakończeniu głosowania (tj. po godz. 23:59:521 grudnia 2014 roku).</b>";
    $vote_ended = "<br /><b>Głosowanie do Rady Rejsu 2015 zostało zakończone. Poniżej prezentujemy wyniki głosowania.</b>";
    $vote_not_start = "<br /><b>Głosowanie rozpocznie się 14 grudnia 2014 roku o godzinie 00:00:00.</b>";
    $vote_forbidden = "<br /><b style='color: #c00'>Nie jesteś uprawiony do głosowania.</b>";
    $vote_forbidden_cache_count = "<br /><i>Masz za mało skrzynek znalezionych do dnia %s, znalezione: %s, wymagane: %s.</i>";
    $vote_forbidden_wrong_date = "<br /><i>Twoje konto zostało założone po %s.</i>";

    $vote_results_header = '<table cellpadding="2"><tr><td><b>Liczba głosów</b></td><td><b>Kandydat</b></td><td><b>Miejscowość</b></td><td><b>Profil</b></td></tr>';
    $vote_results_foother = '</table>';

    $candidate_vote_line = '<tr><td><input type="checkbox" name="candidate{candidate_id}" value="1" id="l_candidate{candidate_id}" class="checkbox" {checked}/></td><td><label for="l_candidate{candidate_id}">{username}</td><td>{city}</td><td><a href="http://www.opencaching.pl/viewprofile.php?userid={user_id}" target="_blank">[Profil kandydata]</a></label></td></tr>';
    $candidate_info_line = '<tr><td>{username}</td><td>{city}</td><td><a href="http://opencaching.pl/viewprofile.php?userid={user_id}" target="_blank">[Profil kandydata]</a></label></td></tr>';
    $candidate_result_line = '<tr><td>{ilosc}</td><td>{username}</td><td>{city}</td><td><a href="http://opencaching.pl/viewprofile.php?userid={user_id}" target="_blank">[Profil kandydata]</a></label></td></tr>';

    $vote_warning = '<font color="red">Nie została wybrana odpowiednia liczba kandydatów. </font>';

    $vote_info = '<br /><b>Regulamin uczestnictwa w wyborach do Rady Rejsu 2014/2015</b><br /><br />
1. Głosowanie rozpocznie się 14 grudnia 2014 roku o godzinie 00:00:00 i trwać będzie do 21 grudnia 2014 roku do godziny 23:59:59.<br />
2. Do głosowania uprawniony jest każdy użytkownik serwisu OC PL, który spełnia następujące warunki:<br />
&nbsp;&nbsp;* zarejestrował się w serwisie na miesiąc przed ogłoszeniem wyborów, tj. przed 7 pazdziernia 2014,<br />
&nbsp;&nbsp;* do dnia ogłoszenia wyborów, tj. do17 pazdziernia 2014 miał znalezionych co najmniej 10 skrzynek.<br />
3. Głosowanie polega na wyborze od 1 do 5 kandydatów z listy.<br />
4. Każdy uprawniony do głosowania może głosować tylko jeden raz i nie może zmienić ani uzupełnić swojego oddanego uprzednio głosu.<br />
5. Wyniki głosowania zostaną opublikowane po zakończeniu głosowania tj. po godzinie 23:59:59, 21 grudnia 2014 roku. Zwycięzcami wyborów będzie 5 osób z największą liczbą głosów.<br />';

?>
