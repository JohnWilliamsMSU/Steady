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

    $page_id = "main";

    $error = false;
    $error_message = '';

    helper::newAuthenticityToken();

    $css_files = array("admin.css");
    $page_title = "General";

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
                                    <span>Statistics</span>
                                </div>
                            </div>

                            <table class="admin_table">
                                <tr>
                                    <th class="text-left">Name</th>
                                    <th>Count</th>
                                </tr>
                                <tr>
                                    <td class="text-left">Accounts</td>
                                    <td><?php echo $stats->getUsersCount(); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-left">Active accounts</td>
                                    <td><?php echo $stats->getUsersCountByState(ACCOUNT_STATE_ENABLED); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-left">Blocked accounts</td>
                                    <td><?php echo $stats->getUsersCountByState(ACCOUNT_STATE_BLOCKED); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-left">Total photos</td>
                                    <td><?php echo $stats->getPhotosCount(); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-left">Total active photos (not removed)</td>
                                    <td><?php echo $stats->getActivePhotosCount(); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-left">Total gifts</td>
                                    <td><?php echo $stats->getGiftsCount(); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-left">Total active gifts (not removed)</td>
                                    <td><?php echo $stats->getActiveGiftsCount(); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-left">Total chats</td>
                                    <td><?php echo $stats->getChatsTotal(); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-left">Total active chats (not removed)</td>
                                    <td><?php echo $stats->getChatsCount(); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-left">Total messages</td>
                                    <td><?php echo $stats->getMessagesTotal(); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-left">Total active messages (not removed)</td>
                                    <td><?php echo $stats->getMessagesCount(); ?></td>
                                </tr>
                            </table>

                            <div class="header" style="margin-top: 30px;">
                                <div class="title">
                                    <span>Search users</span>
                                </div>
                            </div>

                            <form action="/admin/users.php" class="support_wrap" method="get" style="margin-bottom: 20px">
                                <input maxlength="20" placeholder="Find users by username, full name, email. Minimum of 3 characters." name="query" style="width: 805px" type="text" value="">
                                <button class="primary_btn big_btn right" style="width: 120px">Search</button>
                            </form>

                            <div class="header" style="margin-top: 30px;">
                                <div class="title">
                                    <span>The recently registered users</span>
                                </div>
                            </div>

                            <?php

                                $result = $stats->getAccounts(0);

                                $inbox_loaded = count($result['users']);

                                if ($inbox_loaded != 0) {

                                    ?>

                                        <table class="admin_table">
                                            <tr>
                                                <th class="text-left">Id</th>
                                                <th>Account state</th>
                                                <th>Username</th>
                                                <th>Fullname</th>
                                                <th>Facebook</th>
                                                <th>Email</th>
                                                <th>Sign up date</th>
                                                <th>Ip address</th>
                                                <th>Action</th>
                                            </tr>

                                    <?php

                                        foreach ($result['users'] as $key => $value) {

                                            draw($value);
                                        }

                                    ?>

                                        </table>

                                    <?php

                                } else {

                                    ?>

                                    <div class="info">
                                        List is empty.
                                    </div>

                                    <?php
                                }
                            ?>

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

<?php

    function draw($user)
    {
        ?>

        <tr>
            <td class="text-left"><?php echo $user['id']; ?></td>
            <td><?php if ($user['state'] == 0) {echo "Enabled";} else {echo "Blocked";} ?></td>
            <td><?php echo $user['username']; ?></td>
            <td><?php echo $user['fullname']; ?></td>
            <td><?php if (strlen($user['fb_id']) == 0) {echo "Not connected to facebook.";} else {echo "<a target=\"_blank\" href=\"https://www.facebook.com/app_scoped_user_id/{$user['fb_id']}\">Facebook account link</a>";} ?></td>
            <td><?php echo $user['email']; ?></td>
            <td><?php echo date("Y-m-d H:i:s", $user['regtime']); ?></td>
            <td><?php if (!APP_DEMO) {echo $user['ip_addr'];} else {echo "It is not available in the demo version";} ?></td>
            <td><a href="/admin/profile.php/?id=<?php echo $user['id']; ?>">Go to account</a></td>
        </tr>

        <?php
    }