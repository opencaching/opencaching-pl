<!DOCTYPE html>
<html lang="<?=$view->getLang()?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Opencaching: error</title>
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
      <img src="<?=$view->_mainLogo?>" alt="Opencaching logo" style="width: 64px">
    </a>
    <h3>This is ERROR!</h3>

    <?php if($view->message) { ?>
      <h4>The only detail which maybe explain something:
      <span class="error"><?=$view->message?></span></h4>
    <?php } //if-message ?>

    If you think this error is OUR fault and we shoud know about it please:
    <a href="/articles.php?page=contact">contact us!</a>
</div>
</body>
</html>
