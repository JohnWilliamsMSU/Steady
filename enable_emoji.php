<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk, qascript@mail.ru
     *
     * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");

    $page_id = "emoji";

    include_once($_SERVER['DOCUMENT_ROOT']."/core/initialize.inc.php");

    $update = new update($dbo);
    $update->setChatEmojiSupport();
    $update->setGiftsEmojiSupport();
    $update->setPhotosEmojiSupport();

    $css_files = array("admin.css");
    $page_title = APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT']."/common/header.inc.php");
?>

<body class="main_page">

<div id="page_wrap">

    <!-- BEGIN TOP BAR -->
    <?php include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_panel_topbar.inc.php"); ?>
    <!-- END TOP BAR -->

    <div id="page_layout">
        <div id="page_body">

            <div id="wrap3">
                <div id="wrap2">
                    <div id="wrap1">
                        <div id="content">
                            <div class="note orange">
                                <div class="title">Success!</div>
                                Your MySQL version: <?php print mysql_get_client_info(); ?>
                                <br>
                                Database refactoring success!
                            </div>
                        </div>
                    </div>
                </div>

                <?php

                    include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_panel_footer.inc.php");
                ?>

            </div>

        </div>
    </div>

</body>
</html>