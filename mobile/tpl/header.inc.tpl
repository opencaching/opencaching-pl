<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN"
    "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta name="description" content="Geocaching Opencaching"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="Content-Language" content="pl" />
        <title>{$site_name} Mobile - {$pagename}</title>
        <meta name="HandheldFriendly" content="true" />
        <meta name="Viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
        <link rel="stylesheet" type="text/css" href="../lib/style.css" />
        <script type="text/javascript" src="../lib/script.js"></script>
        <script type="text/javascript" src="../lib/ga.js"></script>
        {$HeaderContent}
    </head>

    <body style="max-width:600px; margin:auto; padding: 8px;">

        <div id="header">

            <div id="logo" class="headertitle">
                <a href="./index.php" ><img alt="" src="../images/oc_logo.gif"/>m.{$site_name}</a>
            </div>

            <div id="login_nav" class="button">
                {if isset($smarty.session.user_id)}
                    <a href='./logout.php?token={$smarty.session.logout_cookie}'>{$login_info} <b>{$smarty.session.username}</b><br/></span><span class='login'>{$logout}</span></a>
                    {else}
                    <a href='login.php'><span class='login'>{$login}</span></a>
                    {/if}
            </div>
            <hr/>

        </div>
