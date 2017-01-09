<?php
    use yii\helpers\Url;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset=utf-8>
    <title>monitor</title>
    <link href="<?=Url::to(['/'])?>dist/static/css/app.1c847a21838cb20f6c3143305ca3a681.css" rel=stylesheet>
</head>
<body>
<input type="hidden" name="_csrf" id="_csrf" value="<?=Yii::$app->request->csrfToken;?>">
    <div id=app></div>
<script type=text/javascript src="<?=Url::to(['/'])?>dist/static/js/manifest.df0fc8aa23ee9c6776ec.js"></script>
    <script type=text/javascript src="<?=Url::to(['/'])?>dist/static/js/vendor.1783897215bb499fbb26.js"></script>
    <script type=text/javascript src="<?=Url::to(['/'])?>dist/static/js/app.26ac36d47c55523329e8.js"></script>
</body>
</html>