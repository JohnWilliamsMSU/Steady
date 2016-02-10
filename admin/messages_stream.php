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

    $accountInfo = array();

    $stats = new stats($dbo);

    $page_id = "messages_stream";

    $messages = new messages($dbo);

    $inbox_all = $messages->getMessagesCount();
    $inbox_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $messages->getStream($itemId);

        $inbox_loaded = count($result['messages']);

        $result['inbox_loaded'] = $inbox_loaded + $loaded;
        $result['inbox_all'] = $inbox_all;

        if ($inbox_loaded != 0) {

            ob_start();

            foreach ($result['messages'] as $key => $value) {

                draw($value, $helper);
            }

            if ($result['inbox_loaded'] < $inbox_all) {

                ?>

                <div class="more_cont">
                    <a class="more_link" href="javascript:void(0)" onclick="Stream.moreItems('<?php echo $result['msgId']; ?>'); return false;">View more</a>
                    <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
                </div>

                <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $css_files = array("admin.css");
    $page_title = "Messages Stream";

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
                                    <span>Messages Stream</span>
                                </div>
                            </div>

                            <div id="items_cont" class="items_cont">

                                <?php

                                    $result = $messages->getStream(0);

                                    $inbox_loaded = count($result['messages']);

                                    if ($inbox_loaded != 0) {

                                        foreach ($result['messages'] as $key => $value) {

                                            draw($value, $helper);
                                        }

                                    } else {

                                        ?>

                                        <div class="info">
                                            List is empty.
                                        </div>

                                        <?php
                                }
                                ?>

                                <?php

                                if ($inbox_all > 20) {

                                    ?>

                                    <div class="more_cont">
                                        <a class="more_link" href="javascript:void(0)" onclick="Stream.moreItems('<?php echo $result['msgId']; ?>'); return false;">View more</a>
                                        <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
                                    </div>

                                <?php
                                }

                                ?>

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

            var inbox_all = <?php echo $inbox_all; ?>;
            var inbox_loaded = <?php echo $inbox_loaded; ?>;

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

            window.Stream || ( window.Stream = {} );

            Stream.moreItems = function (offset) {

                $('a.more_link').hide();
                $('a.loading_link').show();

                $.ajax({
                    type: 'POST',
                    url: '/admin/messages_stream.php',
                    data: 'itemId=' + offset + "&loaded=" + inbox_loaded,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $('div.more_cont').remove();

                        if (response.hasOwnProperty('html')){

                            $("div.items_cont").append(response.html);
                        }

                        inbox_loaded = response.inbox_loaded;
                        inbox_all = response.inbox_all;
                    },
                    error: function(xhr, type){

                        $('a.more_link').show();
                        $('a.loading_link').hide();
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

            <a class="profile_cont" href="/admin/profile.php/?id=<?php echo $msg['fromUserId']; ?>">
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