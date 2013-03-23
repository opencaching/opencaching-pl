<script language="javascript" type="text/javascript">
function addtext(obj) 
{
	var newtext = obj.value;
	document.myform.email_content.value = newtext;
}
</script>

		<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/rproblems.png" alt="" class="icon32" align="middle"/>&nbsp;{{admin_09}}</div>
	<div class="buffer"></div>
	<p>{confirm_resp_change}{confirm_status_change}</p>
	{email_sent}
	<p>{{admin_02}}: [<a href='viewreports.php'>{{admin_10}}</a>] [<a href='viewreports.php?archiwum=1'>{{admin_11}}</a>]</p>
		<input type="hidden" name="cacheid" value="{cacheid}"/>
		<form action='viewreport.php' method='post' name='myform'>
		<table border='1' class="table" width="90%">
			<tr>
				<th >ID</th>
				<th >{{admin_03}}</th>
				<th >Cache</th>
				<th >{{admin_04}}</th>
				<th >{{admin_05}}</th>
				<th >{{admin_06}}</th>
				<th >{{admin_07}}</th>
				<th >Status</th>
				<th >{{admin_08}}</th>
			</tr>
			{content}
		</table>
		<div class="buffer" style="height:50px;"></div>
		<div class="content2-container line-box">
			<p class="content-title-noshade-size1">{report_text_lbl}</p><br/>
			<p>{report_text}</p>
		</div>
		<div class="content2-container line-box">
			<p class="content-title-noshade-size1">{note_lbl}</p><br/>
			<p>{active_form}</p>
		</div>
		<div class="content2-container line-box">
			<p>{note_area}</p>
		</div>			
		<div class="buffer"></div>
		<div class="content2-container line-box">
			<p class="content-title-noshade-size1">{perform_action_lbl}</p>
			<ul>
				{mail_actions}
			</ul>
			<ul>
				{actions}
			</ul>
			<br/>
		</div>
			<p>{{admin_02}}: [<a href='viewreports.php'>{{admin_10}}</a>] [<a href='viewreports.php?archiwum=1'>{{admin_11}}</a>]</p>
		</form>
