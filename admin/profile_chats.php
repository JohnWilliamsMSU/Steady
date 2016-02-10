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

    if (isset($_GET['id'])) {

        $accountId = isset($_GET['id']) ? $_GET['id'] : 0;

        $account = new account($dbo, $accountId);
        $accountInfo = $account->get();

        $messages = new messages($dbo);
        $messages->setRequestFrom($accountId);

    } else {

        header("Location: /admin/main.php");
    }

    if ($accountInfo['error'] === true) {

        header("Location: /admin/main.php");
    }

    $stats = new stats($dbo);

    $page_id = "chats";

    $inbox_all = $messages->myActiveChatsCount();
    $inbox_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : '';
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $messages->getChats($itemId);

        $inbox_loaded = count($result['chats']);

        $result['inbox_loaded'] = $inbox_loaded + $loaded;
        $result['inbox_all'] = $inbox_all;

        if ($inbox_loaded != 0) {

            ob_start();

            foreach ($result['chats'] as $key => $value) {

                draw($value, $helper);
            }

            if ($result['inbox_loaded'] < $inbox_all) {

                ?>

                <div class="more_cont">
                    <a class="more_link" href="javascript:void(0)" onclick="Profile.moreItems('<?php echo $result['itemId']; ?>'); return false;">View more</a>
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
    $page_title = "User active chats";

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
                                    <span>User active chats</span>
                                </div>
                            </div>

                            <div id="items_cont" class="items_cont">

                                <?php

                                    $result = $messages->getChats(0);

                                    $inbox_loaded = count($result['chats']);

                                    if ($inbox_loaded != 0) {

                                        foreach ($result['chats'] as $key => $value) {

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
                                        <a class="more_link" href="javascript:void(0)" onclick="Profile.moreItems('<?php echo $result['itemId']; ?>'); return false;">View more</a>
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

            window.Profile || ( window.Profile = {} );

            Profile.moreItems = function (offset) {

                $('a.more_link').hide();
                $('a.loading_link').show();

                $.ajax({
                    type: 'POST',
                    url: '/admin/profile_chats.php/?id=' + <?php echo $accountInfo['id'] ?>,
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

    function draw($chat, $helper = null) {

        ?>

        <div class="post profile_item">
            <a class="profile_cont" href="/admin/profile.php/?id=<?php echo $chat['withUserId']; ?>">
                <?php

                $profilePhotoUrl = "/img/profile_default_photo.png";

                if (strlen($chat['withUserPhotoUrl']) != 0) {

                    $profilePhotoUrl = $chat['withUserPhotoUrl'];
                }
                ?>

                <img src="<?php echo $profilePhotoUrl; ?>"/>
            </a>
            <div class="post_cont">
                <a class="fullname" href="/admin/profile.php/?id=<?php echo $chat['withUserId']; ?>"><?php echo $chat['withUserFullname']; ?></a>
                <div class="addon_info">
                    @<span class="username"><?php echo $chat['withUserUsername']; ?></span>
                </div>
                <div class="addon_info" style="margin-top: 10px">
                    <a href="/admin/chat.php/?id=<?php echo $chat['id']; ?>" class="username">View Conversation</a>
                </div>
            </div>
        </div>

    <?php
    }