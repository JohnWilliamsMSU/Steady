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
    $admin = new admin($dbo);

    $postId = 0;
    $chatInfo = array();

    if (isset($_GET['id'])) {

        $chatId = isset($_GET['id']) ? $_GET['id'] : 0;
        $accessToken = isset($_GET['access_token']) ? $_GET['access_token'] : 0;
        $act = isset($_GET['act']) ? $_GET['act'] : '';

        $chatId = helper::clearInt($chatId);

        $messages = new messages($dbo);
        $chatInfo = $messages->getFull($chatId);

        if ($chatInfo['error'] === true) {

            header("Location: /admin/main.php");
            exit;
        }

    } else {

        header("Location: /admin/main.php");
        exit;
    }

    $page_id = "chat";

    $error = false;
    $error_message = '';

    $css_files = array("admin.css");
    $page_title = "Chat";

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
                                    <span>Chat</span>
                                </div>
                            </div>

                            <?php

                                foreach ($chatInfo['messages'] as $key => $value) {

                                    draw($value, $helper);
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

            window.Message || ( window.Message = {} );

            Message.remove = function (offset, accessToken) {

                $.ajax({
                    type: 'GET',
                    url: '/admin/msg.php/?id=' + offset  + '&access_token=' + accessToken,
                    data: 'itemId=' + offset + "&access_token=" + accessToken,
                    timeout: 30000,
                    success: function(response) {

                        $('div.post_item[data-id=' + offset + ']').remove();
                    },
                    error: function(xhr, type){

                    }
                });
            };

        </script>

    </div>

</body>
</html>

<?php

    function draw($msg, $helper = null) {

        $msg['message'] = helper::processMsgText($msg['message']);

        $fromUserPhoto = "/img/profile_default_photo.png";

        if (strlen($msg['fromUserPhotoUrl']) != 0) {

            $fromUserPhoto = $msg['fromUserPhotoUrl'];
        }

        ?>

        <div class="post post_item" data-id="<?php echo $msg['id']; ?>">

            <a class="profile_cont" href="/admin/profile.php/?id<?php echo $msg['fromUserId']; ?>">
                <img src="<?php echo $fromUserPhoto; ?>">
            </a>

            <div class="post_content">

                <div class="action_remove" onclick="Message.remove('<?php echo $msg['id']; ?>', '<?php echo admin::getAccessToken(); ?>'); return false;"></div>

                <div class="post_title">
                    <a href="/admin/profile.php/?id=<?php echo $msg['fromUserId']; ?>">
                        <span class="post_fullname"><?php echo $msg['fromUserFullname']; ?></span>
                        <s>@</s><b class="post_username"><?php echo $msg['fromUserUsername']; ?></b>
                    </a>
                </div>
                <div class="post_data">
                    <?php echo $msg['message']; ?>
                </div>

                <?php

                if (strlen($msg['imgUrl'])) {

                    ?>

                    <div class="post_img">
                        <img src="<?php echo $msg['imgUrl']; ?>"/>
                    </div>
                    <?php
                }
                ?>

                <div class="post_footer">
                    <?php

                        $time = new language(NULL, "en");
                    ?>
                    <a class="time" href="javascript:void(0);"><?php echo $time->timeAgo($msg['createAt']); ?></a>
                </div>
            </div>

        </div>

    <?php
    }