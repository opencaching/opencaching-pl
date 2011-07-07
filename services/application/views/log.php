<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to OpenCaching PL Node API</title>
<link rel="stylesheet" type="text/css" media="screen,projection" href="/tpl/stdstyle/css/style_screen.css" />
<link rel="SHORTCUT ICON" href="favicon.ico" />
</head>
<body style="background-color:#ddebf5;">

<div class="overall">
  <div class="page-container-1" style="position: relative;">
  <div id="bg1">
  &nbsp;
  </div>
  <div id="bg2">
  &nbsp;
  </div>
<div style="margin-left: 50px;"><a href="http://opencaching.pl/services"><img src="/services/oc_logo.png" align="middle" alt="" style="margin-top:15px; margin-left:20px;" /></a>
<span class="content-title-noshade-size3" style="margin-left:130px;margin-top:20px;font-weight:bold;font-size: 20px;">Welcome to OpenCaching PL Node API </span>
<a href="http://wiki.oauth.net/w/page/25236487/OAuth-2"><img src="/services/OAuth2.png" align="middle" alt="" style="margin-top:15px; margin-left:190px;" /></a></div></br>
<div style="margin-left: 50px;">
<div class=logs style="width:95%">
</br><span style="margin-left:10px;font-weight:bold;font-size:15px;">GET log requests are made to read logs data.</span></br></br>
</div>
<div class="searchdiv">

	<div class="arg2">
		<p>Returns a list of logs</p>
		<p>Either user, wp or log id are required.</p>

	</div>
	<div class="arg">
		<h4>userid=user_id1,user_id2 exmaple: userid=345,23</h4>
			<p>Only logs for the specified users are returned</p>
	</div>

	<div class="arg">
		<h4>wp=wp_name exmaple: wp=op23ea</h4>

			<p>Only logs for the specified geocache are returned</p>
	</div>
	
	<div class="arg">
		<h4>type=type exmaple:  type=1,3</h4>
			<p>List of the types of logs to be returned. If no type parameter is specificed, all types are returned. Otherwise, only the listed types are returned.</p>
			<p>1- <em>Found it</em></p>
			<p>2- <em>Didn't find it</em></p>
			<p>3 -<em>Comment</em></p> 
	</div>

	<div class="arg">
		<h4>limit=limit_logs   exmaple: limit=10 </h4>
			<p>Set the maximum number of logs that will be returned, or "all" to return all logs.
			<p>Defaults to 5.</p>
	</div>

	<div class="arg">
		<h4>offset=offset exmaple: offset=100</h4>
			<p>Instead of returning the most recent logs that meet the criteria, return the most recent logs that meat the criteria after the first &lt;offset&gt; logs.</p>

	</div>

	<div class="arg">
		<h4>id=log_id exmaple: id=1024</h4>
			<p>Returns a single log that matches the log id</p>
	</div>


</div>
</br>
</br/>
<br/>
</body>
</html>
