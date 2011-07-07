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
<div style="margin-left: 50px;"><a href="http://info.opencaching.pl"><img src="/services/oc_logo.png" align="middle" alt="" style="margin-top:15px; margin-left:20px;" /></a>
<span class="content-title-noshade-size3" style="margin-left:130px;margin-top:20px;font-weight:bold;font-size: 20px;">Welcome to OpenCaching PL Node API </span>
<a href="http://wiki.oauth.net/w/page/25236487/OAuth-2"><img src="/services/OAuth2.png" align="middle" alt="" style="margin-top:15px; margin-left:190px;" /></a></div></br>
<div style="margin-left: 50px;">
<div class=logs style="width:95%">
</br><span style="margin-left:10px;font-weight:bold;font-size:15px;">ABOUT API</span></br></br>
</div>
<div class="searchdiv">
	<div >
		<h4>The OpenCaching PL Node API is offered through a rest interface</h4>
			<p>REST stands for Representational State Transfer. Following API REST means that all the public data OpenCaching PL Node has about geocaches, log etc. as well as methods to find of geocaches, logs etc. are available on the internet through a unique resource identifier (a URI or URL).</p>
			<p>For example:<br>
			To find all the geocaches in an around defined point center use <b>http://opencaching.pl/services/api/cache?point=53.0,18.53&dist=25&limit=10&api-key=xxxxx</b><br>
			To get information about a geocache with WP name OP26CB, use <b>http://opencaching.pl/services/api/cache?wp=OP26CB&api-key=xxxxxx</b><br>
	</div>

<h4>API-Key</h4>
	<div>
		<p>To access the OpenCaching.pl Node API you need to API key (add on end URL param: <b>api-key=xxxxxxxxxxxxxx</b>). API key is a 16-character string consisting of upper case letters, lower case letters, and numbers. Each user that is going to be accessing the API needs to get its own key which generate from profile user on OC PL.</p>
	</div>

</div>
</br>
<div class=logs style="width:95%">
</br><span style="margin-left:10px;font-weight:bold;font-size:15px;">GET requests to read information from database</span></br></br>
</div>
<div class="searchdiv">

<h3><?=anchor('geocache','cache');?> </h3>
	<div>
		<p>Returns a list of geocaches.</p>
	</div>

<h3><?=anchor('log','log');?>  </h3>
	<div >
		<p>Returns a list of logs.</p>
	</div>

<h3>user?userid=xxxx</h3>
	<div >
		<p>Returns information about specified user id.</p>
	</div>

</div>
</br>

<div class=logs style="width:95%">
</br><span style="margin-left:10px;font-weight:bold;font-size:15px;">POST requests to add a new object to database</span></br></br>
</div>
<div class="searchdiv">
<div>
<p>All requests require the user to be logged in.</p>
</div>

<h3>cache</h3>
	<div>
	<p>Create a new geocache.</p>
	</div>

<h3>log</h3>
	<div>
		<p>Create a new log signifying a user found, attempted to find, or is leaving a note about a particular geocache.</p>

	</div>
</div>
</br>
<div class=logs style="width:95%">
</br><span style="margin-left:10px;font-weight:bold;font-size:15px;">PUT requests to update an existing object in database</span></br></br>
</div>
<div class="searchdiv">
<div>
<p>All requests require the user to be logged in.</p>
</div>
<h3>cache?wp=wp_name</h3>
	<div>

		<p>Modifies a geocache.</p>
	</div>

<h3>log?id=log_id</h3>
	<div>
	<p>Modifies a log.</p>
	</div>

</div>

</br>
<p>If you are exploring CodeIgniter for the very first time, you should start by reading the <a href="user_guide/">User Guide</a>.</p>
<br/>
</body>
</html>
