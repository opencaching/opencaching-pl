#!/bin/sh
export QUERY_STRING="userid=8595&z=6&x=33&y=22&sc=0&h_u=false&h_t=false&h_m=false&h_v=false&h_w=false&h_e=false&h_q=false&h_o=false&h_ignored=false&h_own=false&h_found=false&h_noattempt=false&h_nogeokret=false&h_avail=false&h_temp_unavail=true&h_arch=true&signes=true&waypoints=true&be_ftf=false&h_de=true&h_pl=true&min_score=-3&max_score=3.000&h_noscore=true&mapid=0&"
./mapper.cgi | wc -c &
proc1=$!
export QUERY_STRING="userid=8595&z=6&x=34&y=22&sc=0&h_u=false&h_t=false&h_m=false&h_v=false&h_w=false&h_e=false&h_q=false&h_o=false&h_ignored=false&h_own=false&h_found=false&h_noattempt=false&h_nogeokret=false&h_avail=false&h_temp_unavail=true&h_arch=true&signes=true&waypoints=true&be_ftf=false&h_de=true&h_pl=true&min_score=-3&max_score=3.000&h_noscore=true&mapid=0&"
./mapper.cgi | wc -c &
proc2=$!
export QUERY_STRING="userid=8595&z=6&x=33&y=21&sc=0&h_u=false&h_t=false&h_m=false&h_v=false&h_w=false&h_e=false&h_q=false&h_o=false&h_ignored=false&h_own=false&h_found=false&h_noattempt=false&h_nogeokret=false&h_avail=false&h_temp_unavail=true&h_arch=true&signes=true&waypoints=true&be_ftf=false&h_de=true&h_pl=true&min_score=-3&max_score=3.000&h_noscore=true&mapid=0&"
./mapper.cgi | wc -c &
proc3=$!
export QUERY_STRING="userid=8595&z=6&x=33&y=20&sc=0&h_u=false&h_t=false&h_m=false&h_v=false&h_w=false&h_e=false&h_q=false&h_o=false&h_ignored=false&h_own=false&h_found=false&h_noattempt=false&h_nogeokret=false&h_avail=false&h_temp_unavail=true&h_arch=true&signes=true&waypoints=true&be_ftf=false&h_de=true&h_pl=true&min_score=-3&max_score=3.000&h_noscore=true&mapid=0&"
./mapper.cgi |wc -c  &
proc4=$!
export QUERY_STRING="userid=8595&z=6&x=35&y=20&sc=0&h_u=false&h_t=false&h_m=false&h_v=false&h_w=false&h_e=false&h_q=false&h_o=false&h_ignored=false&h_own=false&h_found=false&h_noattempt=false&h_nogeokret=false&h_avail=false&h_temp_unavail=true&h_arch=true&signes=true&waypoints=true&be_ftf=false&h_de=true&h_pl=true&min_score=-3&max_score=3.000&h_noscore=true&mapid=0&"
./mapper.cgi | wc -c &
proc5=$!
wait $proc1
wait $proc2
wait $proc3
wait $proc4
wait $proc5