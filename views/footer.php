<?php

use Framework\ViewParams;

$viewParams = new ViewParams($params, $router);
$viewParams->setParamsDefaultVal();

$noFooter = $viewParams->getParam('no_footer');
$noAbout = $viewParams->getParam('no_about');
$noContainer = $viewParams->getParam('no_container');

?>

<?php if (!$noContainer) : ?>
    </div>
<?php endif; ?>

<?php if (!$noFooter) : ?>
    <footer class="footer">
        <p class="uk-h4"><b>Thomas SCHUBNEL</b> &middot; <b>Cyril MEYER</b> &middot; &copy; <?= date('Y') ?></p>
    </footer>
<?php endif; ?>

</body>

</html>