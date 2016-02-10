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

    $stats = new stats($dbo);
    $settings = new settings($dbo);
    $admin = new admin($dbo);

    $default = $settings->getIntValue("admob");

    if (isset($_GET['act'])) {

        $accessToken = isset($_GET['access_token']) ? $_GET['access_token'] : 0;
        $act = isset($_GET['act']) ? $_GET['act'] : '';

        if ($accessToken === admin::getAccessToken() && !APP_DEMO) {

            switch ($act) {

                case "global_off": {

                    $settings->setValue("admob", 0);

                    header("Location: /admin/admob.php");
                    break;
                }

                case "global_on": {

                    $settings->setValue("admob", 1);

                    header("Location: /admin/admob.php");
                    break;
                }

                case "on": {

                    $admin->setAdmobValueForAccounts(1);

                    header("Location: /admin/admob.php");
                    break;
                }

                case "off": {

                    $admin->setAdmobValueForAccounts(0);

                    header("Location: /admin/admob.php");
                    break;
                }

                default: {

                    header("Location: /admin/admob.php");
                    exit;
                }
            }
        }

    }

    $page_id = "admob";

    $error = false;
    $error_message = '';

    helper::newAuthenticityToken();

    $css_files = array("admin.css");
    $page_title = "AdMob Settings";

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
                                    <span>AdMob Settings</span>
                                </div>
                            </div>

                            <div class="note orange" style="margin-top: 20px; margin-bottom: 20px">
                                <div class="title">Note!</div>
                                In application changes will take effect during the next user authorization.
                            </div>

                            <table class="admin_table">
                                <tr>
                                    <th class="text-left">Type</th>
                                    <th>Count</th>
                                </tr>
                                <tr>
                                    <td class="text-left">AdMob active in accounts (On)</td>
                                    <td><?php echo $stats->getAccountsCountByAdmob(1); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-left">Accounts count with deactivated AdMob (Off)</td>
                                    <td><?php echo $stats->getAccountsCountByAdmob(0); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-left">Default AdMob value for new users</td>
                                    <td><?php if ($default == 1) {echo "On";} else {echo "Off"; } ?></td>
                                </tr>
                            </table>

                            <div style="margin-top: 10px; text-align: right">
                                <?php
                                    if ($default == 1) {

                                        ?>
                                            <a class="primary_btn big_btn" style="float: left" href="/admin/admob.php/?access_token=<?php echo admin::getAccessToken(); ?>&act=global_off">Turn Off AdMob for new users</a>
                                        <?php

                                    } else {

                                        ?>
                                            <a class="primary_btn big_btn" style="float: left" href="/admin/admob.php/?access_token=<?php echo admin::getAccessToken(); ?>&act=global_on">Turn On AdMob for new users</a>
                                        <?php
                                    }
                                ?>

                                <a class="primary_btn big_btn" href="/admin/admob.php/?access_token=<?php echo admin::getAccessToken(); ?>&act=on">Turn On AdMob in all accounts</a>
                                <a class="primary_btn big_btn" href="/admin/admob.php/?access_token=<?php echo admin::getAccessToken(); ?>&act=off">Turn Off AdMob in all accounts</a>
                            </div>

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