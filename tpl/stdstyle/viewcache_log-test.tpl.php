<script language="javascript" type="text/javascript">

function decrypt_logs(id)
{
var logid=id;
var hintLink = document.getElementById('ctl00_ContentBody_Encrypt');
hintLink.setAttribute('onclick', '$(\'logid\').innerHTML = convertROTStringWithBrackets($(\'logid\').innerHTML); return false;');
}
</script>

	<div class="content-txtbox-noshade line-box {show_deleted}">
	<p class="content-title-noshade-size1">{logimage} {date} {ratingimage} <a href="viewprofile.php?userid={userid}">{username}</a> {type} {logfunctions}</p> 
	<div class="viewcache_log-content">
	{log_coordinates}
	{logtext}
	{logpictures}
	</div>
	</div>
