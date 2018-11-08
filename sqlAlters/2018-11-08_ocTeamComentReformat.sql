-- kojoty: reformat of ocTeam comment in cache-desc


UPDATE cache_desc SET rr_comment = REPLACE (rr_comment,"<br/>","");
UPDATE cache_desc SET rr_comment = REPLACE (rr_comment,'<b><span class="content-title-noshade txt-blue08">','<span class="ocTeamCommentHeader">');
UPDATE cache_desc SET rr_comment = REPLACE (rr_comment,"</span></b>","</span>");
