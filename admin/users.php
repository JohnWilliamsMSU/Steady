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

    $page_id = "search";

    $error = false;
    $error_message = '';
    $query = '';
    $result = array();
    $result['users'] = array();

    $stats = new stats($dbo);
    $settings = new settings($dbo);
    $admin = new admin($dbo);

    if (isset($_GET['query'])) {

        $query = isset($_GET['query']) ? $_GET['query'] : '';

        $query = helper::clearText($query);
        $query = helper::escapeText($query);

        if (strlen($query) > 2) {

            $result = $stats->searchAccounts(0, $query);
        }
    }

    helper::newAuthenticityToken();

    $css_files = array("admin.css");
    $page_title = "Users";

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
                                    <span>Search</span>
                                </div>
                            </div>

                            <form action="/admin/users.php" class="support_wrap" method="get" style="margin-bottom: 20px">
                                <input maxlength="20" placeholder="Find users by username, full name, email. Minimum of 3 characters." name="query" style="width: 805px" type="text" value="<?php echo stripslashes($query); ?>">
                                <button class="primary_btn big_btn right" style="width: 120px">Search</button>
                            </form>

                            <?php

                                if (count($result['users']) > 0) {

                                    ?>

                                        <div class="box_code">
                                            Matches found: <?php echo count($result['users']); ?>
                                        </div>

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

                                    if (strlen($query) < 3) {

                                        ?>
                                            <div class="info">
                                                Enter in the search box username, full name or email. Minimum of 3 characters.
                                            </div>
                                        <?php

                                    } else {

                                        ?>
                                            <div class="box_code">
                                                Matches found: 0
                                            </div>
                                        <?php
                                    }
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