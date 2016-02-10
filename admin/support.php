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

    $page_id = "support";

    $error = false;
    $error_message = '';
    $query = '';
    $result = array();
    $result['id'] = 0;
    $result['tickets'] = array();

    $support = new support($dbo);

    if (isset($_GET['act'])) {

        $act = isset($_GET['act']) ? $_GET['act'] : '';
        $ticketId = isset($_GET['ticketId']) ? $_GET['ticketId'] : 0;
        $token = isset($_GET['access_token']) ? $_GET['access_token'] : '';

        $ticketId = helper::clearText($ticketId);

        if (admin::getAccessToken() === $token && !APP_DEMO) {

            switch ($act) {

                case "delete" : {

                    $support->removeTicket($ticketId);

                    header("Location: /admin/support.php");
                    break;
                }

                default: {

                    header("Location: /admin/support.php");
                }
            }
        }

        header("Location: /admin/support.php");
    }

    $result = $support->getTickets();

    $css_files = array("admin.css");
    $page_title = "Support";

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
                                    <span>Support</span>
                                </div>
                            </div>

                            <?php

                            function drawResultTable($result)
                            {

                                ?>

                                <table class="admin_table">
                                    <tr>
                                        <th class="text-left">Id</th>
                                        <th class="text-left"From account</th>
                                        <th class="text-left">Email</th>
                                        <th class="text-left">Subject</th>
                                        <th class="text-left">Text</th>
                                        <th class="text-left">Date</th>
                                        <th>Action</th>
                                    </tr>
                                    <?php

                                    foreach ($result['tickets'] as $key => $value) {

                                        ?>

                                        <tr>
                                            <td class="text-left"><?php echo $value['id']; ?></td>
                                            <td class="text-left"><?php if ($value['accountId'] != 0 ) echo "<a href=\"/admin/profile.php/?id={$value['accountId']}\">Profile [Id = {$value['accountId']}]</a>"; else echo "-"; ?></td>
                                            <td class="text-left"><?php echo $value['email']; ?></a></td>
                                            <td class="text-left" style="word-break: break-all;"><?php echo $value['subject']; ?></td>
                                            <td class="text-left" style="word-break: break-all;"><?php echo $value['text']; ?></td>
                                            <td class="text-left" style="white-space: nowrap;"><?php echo date("Y-m-d H:i:s", $value['createAt']); ?></td>
                                            <td><a href="/admin/support.php/?ticketId=<?php echo $value['id']; ?>&act=delete&access_token=<?php echo admin::getAccessToken(); ?>">Delete</a></td>
                                        </tr>

                                    <?php
                                    }

                                    ?>

                                </table>

                            <?php
                            }

                            if (count($result['tickets']) > 0) {

                                drawResultTable($result);

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

    </div>

</body>
</html>