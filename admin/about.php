<?php

/*!
 * ifsoft engine v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * qascript@ifsoft.co.uk, qascript@mail.ru
 *
 * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
 */

    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");


    $page_id = "about";

    $error = false;
    $error_message = '';

    helper::newAuthenticityToken();

    $css_files = array("admin.css");
    $page_title = "About | ".APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT']."/common/header.inc.php");

?>

<body class="bg_gray">

    <div id="page_wrap">

    <?php

        include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_panel_topbar.inc.php");
    ?>

    <div id="page_layout">

        <div id="page_body">
            <div id="wrap3">
                <div id="wrap2">
                    <div id="wrap1">

                        <div id="content">

                            <div class="header">
                                <div class="title">
                                    <span>About</span>
                                </div>
                            </div>


                            Copyright (C) 2016 by Demyanchuk Dmitry (<a href="https://vk.com/dmitry.demyanchuk">https://vk.com/dmitry.demyanchuk</a>)
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php

            include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_panel_footer.inc.php");
        ?>

        <script type="text/javascript">


        </script>

    </div>

</body>
</html>
