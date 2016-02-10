<?php

    /*!
     * ifsoft.co.uk engine v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");

    if (!admin::isSession()) {

        header("Location: /admin/login.php");
    }

    if (!empty($_POST)) {

        $authToken = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';
        $current_passw = isset($_POST['current_passw']) ? $_POST['current_passw'] : '';
        $new_passw = isset($_POST['new_passw']) ? $_POST['new_passw'] : '';

        $current_passw = helper::clearText($current_passw);
        $current_passw = helper::escapeText($current_passw);

        $new_passw = helper::clearText($new_passw);
        $new_passw = helper::escapeText($new_passw);

        if ($authToken === helper::getAuthenticityToken() && !APP_DEMO) {

            $admin = new admin($dbo);
            $admin->setId(admin::getCurrentAdminId());

            $result = $admin->setPassword($current_passw, $new_passw);

            if ($result['error'] === false) {

                header("Location: /admin/settings.php/?result=success");
                exit;

            } else {

                header("Location: /admin/settings.php/?result=error");
                exit;
            }
        }

        header("Location: /admin/settings.php");
        exit;
    }

    $stats = new stats($dbo);

    $page_id = "settings";

    $error = false;
    $error_message = '';

    helper::newAuthenticityToken();

    $css_files = array("admin.css");
    $page_title = "Settings";

    include_once($_SERVER['DOCUMENT_ROOT']."/common/header.inc.php");
?>

<body class="bg_gray">

    <div id="page_wrap">

    <?php

        include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_panel_topbar.inc.php");
    ?>

    <div id="page_layout">

        <?php

            include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_panel_banner.inc.php");
        ?>

        <div id="page_body">
            <div id="wrap3">
                <div id="wrap2">
                    <div id="wrap1">

                        <div id="content">

                            <div class="header">
                                <div class="title">
                                    <span>Settings</span>
                                </div>
                            </div>

                            <?php

                                if (isset($_GET['result'])) {

                                    $result = isset($_GET['result']) ? $_GET['result'] : '';

                                    switch ($result) {

                                        case "success": {

                                            ?>
                                                <div class="box_msg">
                                                    <b>Thanks!</b>
                                                    <br>
                                                    New password is saved.
                                                </div>
                                            <?php

                                            break;
                                        }

                                        case "error": {

                                            ?>
                                                <div class="box_error">
                                                    <b>Error!</b>
                                                    <br>
                                                    Invalid current password or incorrectly enter a new password.
                                                </div>
                                            <?php

                                            break;
                                        }

                                        default: {

                                            break;
                                        }
                                    }
                                }
                            ?>

                            <form method="post" action="/admin/settings.php" class="support_wrap">

                                <input type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">

                                <div class="ticket_email">
                                    <label for="current_passw" class="noselect">Current password:</label>
                                    <div><input id="current_passw" type="password" maxlength="100" name="current_passw" value=""></div>
                                </div>

                                <div class="ticket_email">
                                    <label for="new_passw" class="noselect">New password:</label>
                                    <div><input id="new_passw" type="password" maxlength="100" name="new_passw" value=""></div>
                                </div>

                                <div class="ticket_controls">
                                    <button class="primary_btn big_btn">Save</button>
                                </div>
                            </form>

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
