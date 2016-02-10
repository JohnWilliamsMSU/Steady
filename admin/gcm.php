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
        $message = isset($_POST['message']) ? $_POST['message'] : '';
        $type = isset($_POST['type']) ? $_POST['type'] : 1;

        $message = helper::clearText($message);
        $message = helper::escapeText($message);

        $type = helper::clearInt($type);

        if ($authToken === helper::getAuthenticityToken() && !APP_DEMO) {

            if (strlen($message) != 0) {

                $gcm = new gcm($dbo, 0);
                $gcm->setData($type, $message, 0);
                $gcm->forAll();
                $gcm->send();
            }
        }

        header("Location: /admin/gcm.php");
    }

    $stats = new stats($dbo);

    $page_id = "gcm";

    $error = false;
    $error_message = '';

    helper::newAuthenticityToken();

    $css_files = array("admin.css");
    $page_title = "Google Cloud Messages";

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
                                    <span>Send message (GCM) for all users</span>
                                </div>
                            </div>

                            <?php
                                if (APP_DEMO) {

                                    ?>
                                        <div class="box_msg">
                                            <b>Note!</b>
                                            <br>
                                            Sending push notifications (GCM) is not available in the demo version mode. That we turned off the sending push notifications (GCM) in the demo version mode to protect users from spam and of foul language.
                                        </div>
                                    <?php
                                }
                            ?>

                            <form method="post" action="/admin/gcm.php" class="support_wrap">

                                <input type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">

                                <div class="ticket_email">
                                    <label for="type" class="noselect">Message type:</label>
                                    <div>
                                        <select name="type">
                                           <option selected="selected" value="<?php echo GCM_NOTIFY_SYSTEM; ?>">For all users</option>
                                           <option value="<?php echo GCM_NOTIFY_CUSTOM; ?>">Only for authorized users</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="ticket_email">
                                    <label for="message" class="noselect">Message text:</label>
                                    <div><input type="text" maxlength="100" name="message" value=""></div>
                                </div>

                                <div class="ticket_controls">
                                    <button class="primary_btn big_btn">Send</button>
                                </div>
                            </form>

                            <div class="header" style="margin-top: 30px;">
                                <div class="title">
                                    <span>Recently sent messages</span>
                                </div>
                            </div>

                            <?php

                                $result = $stats->getGcmHistory();

                                $inbox_loaded = count($result['data']);

                                if ($inbox_loaded != 0) {

                                    ?>

                                        <table class="admin_table">
                                            <tr>
                                                <th class="text-left">Id</th>
                                                <th>Message</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Delivered</th>
                                                <th>Create At</th>
                                            </tr>

                                    <?php

                                    foreach ($result['data'] as $key => $value) {

                                        draw($value);
                                    }

                                    ?>

                                        </table>

                                    <?php

                                } else {

                                    ?>

                                    <div class="info">
                                        History is empty.
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

    function draw($authObj)
    {
        ?>

        <tr>
            <td class="text-left"><?php echo $authObj['id']; ?></td>
            <td><?php echo $authObj['msg']; ?></td>
            <td>
                <?php

                    switch ($authObj['msgType']) {

                        case GCM_NOTIFY_SYSTEM: {

                            echo "For all users";
                            break;
                        }

                        case GCM_NOTIFY_CUSTOM: {

                            echo "Only for authorized users";
                            break;
                        }

                        default: {

                            break;
                        }
                    }
                ?>
            </td>
            <td><?php if ($authObj['status'] == 1) {echo "success";} else {echo "failure";} ?></td>
            <td><?php echo $authObj['success']; ?></td>
            <td><?php echo date("Y-m-d H:i:s", $authObj['createAt']); ?></td>
        </tr>

        <?php
    }