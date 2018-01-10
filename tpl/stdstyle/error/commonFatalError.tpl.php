<!DOCTYPE html>
<html lang="<?=$view->getLang()?>">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="keywords" content="<?=$view->_keywords?>">
    <meta name="author" content="<?=$view->_siteName?>">
<style>
    body {
        text-align: center;
        background-color: #7B7C72;
    }

    #container {
        width: 50%;
        padding: 10px 0 50px 0;
        margin: 0 auto;
        border-radius: 15px;
        border: 5px solid #58524A;
        color: #3E3E41;
        background-color: #BCB393;
    }

    .error {
        color: #7B7C72;
        font-weight: bold;
    }
</style>
</head>
<body>

<div id="container">

    <a href="/">
      <img src="<?=$view->_mainLogo?>" alt="Opencaching logo" />
    </a>
    <h3>This is ERROR!</h3>

    <?php if($view->message) { ?>
      <h4>The only detail which maybe explain something:
      <span class="error"><?=$view->message?></span></h5>
    <?php } //if-message ?>

    If you think this error is OUR fault and we shoud know about it please:
    <a href="/articles.php?page=contact">contact us!</a>
</div>
</body>
</html>
