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

    if (admin::isSession()) {

        header("Location: /admin/main.php");
    }

    $page_id = "login";

    $user_username = '';

    $error = false;
    $error_message = '';

    if (!empty($_POST)) {

        $user_username = isset($_POST['user_username']) ? $_POST['user_username'] : '';
        $user_password = isset($_POST['user_password']) ? $_POST['user_password'] : '';
        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $user_username = helper::clearText($user_username);
        $user_password = helper::clearText($user_password);

        $user_username = helper::escapeText($user_username);
        $user_password = helper::escapeText($user_password);

        if (helper::getAuthenticityToken() !== $token) {

            $error = true;
            $error_message = 'Error!';
        }

        if (!$error) {

            $access_data = array();

            $admin = new admin($dbo);
            $access_data = $admin->signin($user_username, $user_password);

            if ($access_data['error'] === false) {

                $clientId = 0; // Desktop version

                admin::createAccessToken();

                admin::setSession($access_data['accountId'], admin::getAccessToken());

                header("Location: /admin/main.php");

            } else {

                $error = true;
                $error_message = 'Incorrect login or password.';
            }
        }
    }

    helper::newAuthenticityToken();

    $css_files = array("admin.css");
    $page_title = APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT']."/common/header.inc.php");

?>

<body style="background-color: transparent; background-image: none">

    <div id="page_wrap">

        <?php

            include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_panel_topbar.inc.php");
        ?>

        <div id="page_layout">

            <?php

                include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_panel_banner.inc.php");
            ?>

            <div id="page_auth">

                <div class="header">
                    <div class="title">Log in</div>
                </div>


                <div class="error <?php if (!$error) echo "hide"; ?>">
                    <?php echo $error_message; ?>
                </div>

                <div class="frm">
                    <form action="/admin/login.php" method="post" id="login_form">
                        <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">
                        <div class="frm_header">
                            <label class="noselect" for="user_username">Username:</label>
                        </div>
                        <input autocomplete="off" type="text" id="user_username" class="frm_input" maxlength="24" name="user_username" value="<?php echo $user_username; ?>">
                        <div class="frm_header">
                            <label class="noselect" for="user_password">Password:</label>
                        </div>
                        <input autocomplete="off" type="password" id="user_password" class="frm_input" maxlength="20" name="user_password" value="">
                        <div class="">
                            <button type="submit" class="frm_btn primary_btn big_btn">Log In</button>
                        </div>
                    </form>
                </div>

            </div>

            <?php

                include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_panel_footer.inc.php");
            ?>

        </div>
    </div>

</body>
</html>