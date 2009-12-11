<?php
	$user_max_date_created = strtotime("2009-05-11 00:00:00");
	
	$vote_time_start = strtotime("2009-05-12 00:00:00");
	$vote_time_end = strtotime("2009-05-25 23:59:59");

	$just_voted = "<br /><b>Twój głos został zapisany. Dziękujemy za udział w głosowaniu. Wyniki głosowania zostaną opublikowane po zakończeniu głosowania (tj. po godz. 23:59:59, dnia 25 maja 2009 roku).</b>";
	$already_voted = "<br /><b>Oddałeś już swój głos na kandydatów. Wyniki głosowania zostaną opublikowane po zakończeniu głosowania (tj. po godz. 23:59:59, dnia 25 maja 2009 roku).</b>";
	$vote_ended = "<br /><b>Głosowanie do Rady Rejsu zostało zakończone. Poniżej prezentujemy wyniki głosowania.</b>";
	$vote_not_start = "<br /><b>Głosowanie rozpocznie się dnia 12 maja 2009 roku o godzinie 00:00:00. Poniżej prezentujemy regulamin głosowania w wyborach do Rady Rejsu 2009.</b>";
	$vote_forbidden = "<br /><b>Nie jesteś uprawiony do głosowania. Poniżej prezentujemy regulamin głosowania w wyborach do Rady Rejsu 2009.</b>";
	
	$vote_results_header = '<table><tr><td><b>Ilość głosów</b></td><td><b>Kandydat</b></td><td><b>Miejscowość</b></td><td><b>Profil</b></td></tr>';
	$vote_results_foother = '</table>';
	
	$candidate_vote_line = '<tr><td><input type="checkbox" name="candidate{candidate_id}" value="1" id="l_candidate{candidate_id}" class="checkbox" {checked}/></td><td><label for="l_candidate{candidate_id}">{username}</td><td>{city}</td><td><a href="http://www.opencaching.pl/viewprofile.php?userid={user_id}" target="_blank">[Profil kandydata]</a></label></td></tr>';
	$candidate_info_line = '<tr><td>{username}</td><td>{city}</td><td><a href="http://www.opencaching.pl/viewprofile.php?userid={user_id}" target="_blank">[Profil kandydata]</a></label></td></tr>';
	$candidate_result_line = '<tr><td>{ilosc}</td><td>{username}</td><td>{city}</td><td><a href="http://www.opencaching.pl/viewprofile.php?userid={user_id}" target="_blank">[Profil kandydata]</a></label></td></tr>';
	
	$vote_warning = '<font color="red">Nie została wybrana odpowiednia ilość kandydatów. </font>';
	
	$vote_info = 'Regulamin uczestnictwa w wyborach do Rady Rejsu 2009:<br />
1. Głosowanie rozpocznie się dnia 12 maja 2009 roku o godzinie 00:00:00 i trwać będzie do dnia 25 maja 2009 roku do godziny 23:59:59.<br />
2. Do głosowania uprawniony jest każdy użytkownik serwisu OC PL, który posiadał w nim konto założone przed zamknięciem listy kandydatów (tj. przed godz. 00:00:00 dnia 11 maja 2009).<br />
3. Głosowanie polega na wyborze od 1 do 7 kandydatów z listy.<br />
4. Każdy uprawniony do głosowania może głosować tylko jeden raz i nie może zmienić ani uzupełnić swojego oddanego uprzednio głosu.<br />
5. Wyniki głosowania zostaną opublikowane po zakończeniu głosowania tj. po godzinie 23:59:59, 25 maja 2009 roku. Zwycięzcami wyborów będzie 7 osób z największą ilością głosów.<br />';	

?>
