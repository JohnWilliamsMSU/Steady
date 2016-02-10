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
        $accessToken = isset($_GET['access_token']) ? $_GET['access_token'] : 0;
        $act = isset($_GET['act']) ? $_GET['act'] : '';

        $accountId = helper::clearInt($accountId);

        $account = new account($dbo, $accountId);
        $accountInfo = $account->get();

        $messages = new messages($dbo);
        $messages->setRequestFrom($accountId);

        if ($accessToken === admin::getAccessToken() && !APP_DEMO) {

            switch ($act) {

                case "disconnect": {

                    $account->setFacebookId('');

                    header("Location: /admin/profile.php/?id=".$accountInfo['id']);
                    break;
                }

                case "showAdmob": {

                    $account->setAdmob(1);

                    header("Location: /admin/profile.php/?id=".$accountInfo['id']);
                    break;
                }

                case "hideAdmob": {

                    $account->setAdmob(0);

                    header("Location: /admin/profile.php/?id=".$accountInfo['id']);
                    break;
                }

                case "close": {

                    $auth->removeAll($accountId);

                    header("Location: /admin/profile.php/?id=".$accountInfo['id']);
                    break;
                }

                case "block": {

                    $account->setState(ACCOUNT_STATE_BLOCKED);

                    header("Location: /admin/profile.php/?id=".$accountInfo['id']);
                    break;
                }

                case "unblock": {

                    $account->setState(ACCOUNT_STATE_ENABLED);

                    header("Location: /admin/profile.php/?id=".$accountInfo['id']);
                    break;
                }

                case "verify": {

                    $account->setVerify(1);

                    header("Location: /admin/profile.php/?id=".$accountInfo['id']);
                    break;
                }

                case "unverify": {

                    $account->setVerify(0);

                    header("Location: /admin/profile.php/?id=".$accountInfo['id']);
                    break;
                }

                case "ghost_set": {

                    $account->setGhost(1);

                    header("Location: /admin/profile.php/?id=".$accountInfo['id']);
                    break;
                }

                case "ghost_unset": {

                    $account->setGhost(0);

                    header("Location: /admin/profile.php/?id=".$accountInfo['id']);
                    break;
                }

                case "delete-cover": {

                    $data = array("originCoverUrl" => '',
                                  "normalCoverUrl" => '');

                    $account->setCover($data);

                    header("Location: /admin/profile.php/?id=".$accountInfo['id']);
                    break;
                }

                case "delete-photo": {

                    $data = array("originPhotoUrl" => '',
                                  "normalPhotoUrl" => '',
                                  "lowPhotoUrl" => '');

                    $account->setPhoto($data);

                    header("Location: /admin/profile.php/?id=".$accountInfo['id']);
                    break;
                }

                default: {

                    if (!empty($_POST)) {

                        $authToken = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';
                        $username = isset($_POST['username']) ? $_POST['username'] : '';
                        $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
                        $location = isset($_POST['location']) ? $_POST['location'] : '';
                        $balance = isset($_POST['balance']) ? $_POST['balance'] : 0;
                        $fb_page = isset($_POST['fb_page']) ? $_POST['fb_page'] : '';
                        $instagram_page = isset($_POST['instagram_page']) ? $_POST['instagram_page'] : '';
                        $email = isset($_POST['email']) ? $_POST['email'] : '';

                        $username = helper::clearText($username);
                        $username = helper::escapeText($username);

                        $fullname = helper::clearText($fullname);
                        $fullname = helper::escapeText($fullname);

                        $location = helper::clearText($location);
                        $location = helper::escapeText($location);

                        $balance = helper::clearInt($balance);

                        $fb_page = helper::clearText($fb_page);
                        $fb_page = helper::escapeText($fb_page);

                        $instagram_page = helper::clearText($instagram_page);
                        $instagram_page = helper::escapeText($instagram_page);

                        $email = helper::clearText($email);
                        $email = helper::escapeText($email);

                         if ($authToken === helper::getAuthenticityToken()) {

                            $account->setUsername($username);
                            $account->setFullname($fullname);
                            $account->setLocation($location);
                            $account->setBalance($balance);
                            $account->setFacebookPage($fb_page);
                            $account->setInstagramPage($instagram_page);
                            $account->setEmail($email);
                         }
                    }

                    header("Location: /admin/profile.php/?id=".$accountInfo['id']);
                    exit;
                }
            }
        }

    } else {

        header("Location: /admin/main.php");
    }

    if ($accountInfo['error'] === true) {

        header("Location: /admin/main.php");
    }

    $stats = new stats($dbo);

    $page_id = "account";

    $error = false;
    $error_message = '';

    helper::newAuthenticityToken();

    $css_files = array("admin.css");
    $page_title = $accountInfo['username']." | Account info";

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
                                    <span>Account info</span>
                                </div>
                            </div>

                            <table class="admin_table">
                                <tr>
                                    <th class="text-left">Name</th>
                                    <th>Value/Count</th>
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <td class="text-left">Username:</td>
                                    <td><?php echo $accountInfo['username']; ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="text-left">Fullname:</td>
                                    <td><?php echo $accountInfo['fullname']; ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="text-left">Email:</td>
                                    <td><?php echo $accountInfo['email']; ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="text-left">Facebook account:</td>
                                    <td><?php if (strlen($accountInfo['fb_id']) == 0) {echo "Not connected to facebook.";} else {echo "<a target=\"_blank\" href=\"https://www.facebook.com/app_scoped_user_id/{$accountInfo['fb_id']}\">Facebook account link</a>";} ?></td>
                                    <td><?php if (strlen($accountInfo['fb_id']) == 0) {echo "";} else {echo "<a href=\"/admin/profile.php/?id={$accountInfo['id']}&access_token=".admin::getAccessToken()."&act=disconnect\">Remove connection</a>";} ?></td>
                                </tr>
                                <tr>
                                    <td class="text-left">SignUp Ip address:</td>
                                    <td><?php if (!APP_DEMO) {echo $accountInfo['ip_addr'];} else {echo "It is not available in the demo version";} ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="text-left">SignUp Date:</td>
                                    <td><?php echo date("Y-m-d H:i:s", $accountInfo['regtime']); ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="text-left">AdMob (on/off AdMob in account):</td>
                                    <td>
                                        <?php

                                            if ($accountInfo['admob'] == 1) {

                                                echo "<span>On (AdMob is active in account)</span>";

                                            } else {

                                                echo "<span>Off (AdMob is not active in account)</span>";
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php

                                            if ($accountInfo['admob'] == 1) {

                                                ?>
                                                    <a class="" href="/admin/profile.php/?id=<?php echo $accountInfo['id']; ?>&access_token=<?php echo admin::getAccessToken(); ?>&act=hideAdmob">Turn Off AdMob in this account</a>
                                                <?php

                                            } else {

                                                ?>
                                                    <a class="" href="/admin/profile.php/?id=<?php echo $accountInfo['id']; ?>&access_token=<?php echo admin::getAccessToken(); ?>&act=showAdmob">Turn On AdMob in this account </a>
                                                <?php
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-left">Account state:</td>
                                    <td>
                                        <?php

                                            if ($accountInfo['state'] == ACCOUNT_STATE_ENABLED) {

                                                echo "<span>Account is active</span>";

                                            } else {

                                                echo "<span>Account is blocked</span>";
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php

                                            if ($accountInfo['state'] == ACCOUNT_STATE_ENABLED) {

                                                ?>
                                                    <a class="" href="/admin/profile.php/?id=<?php echo $accountInfo['id']; ?>&access_token=<?php echo admin::getAccessToken(); ?>&act=block">Block account</a>
                                                <?php

                                            } else {

                                                ?>
                                                    <a class="" href="/admin/profile.php/?id=<?php echo $accountInfo['id']; ?>&access_token=<?php echo admin::getAccessToken(); ?>&act=unblock">Unblock account</a>
                                                <?php
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-left">Account verified:</td>
                                    <td>
                                        <?php

                                            if ($accountInfo['verify'] == 1) {

                                                echo "<span>Account is verified.</span>";

                                            } else {

                                                echo "<span>Account is not verified.</span>";
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php

                                            if ($accountInfo['verify'] == 1) {

                                                ?>
                                                    <a class="" href="/admin/profile.php/?id=<?php echo $accountInfo['id']; ?>&access_token=<?php echo admin::getAccessToken(); ?>&act=unverify">Unset verified</a>
                                                <?php

                                            } else {

                                                ?>
                                                    <a class="" href="/admin/profile.php/?id=<?php echo $accountInfo['id']; ?>&access_token=<?php echo admin::getAccessToken(); ?>&act=verify">Set account as verified</a>
                                                <?php
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-left">Ghost Mode:</td>
                                    <td>
                                        <?php

                                            if ($accountInfo['ghost'] == 1) {

                                                echo "<span>Ghost Mode Activated.</span>";

                                            } else {

                                                echo "<span>Ghost Mode Not Active.</span>";
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php

                                            if ($accountInfo['ghost'] == 1) {

                                                ?>
                                                    <a class="" href="/admin/profile.php/?id=<?php echo $accountInfo['id']; ?>&access_token=<?php echo admin::getAccessToken(); ?>&act=ghost_unset">Off Ghost Mode</a>
                                                <?php

                                            } else {

                                                ?>
                                                    <a class="" href="/admin/profile.php/?id=<?php echo $accountInfo['id']; ?>&access_token=<?php echo admin::getAccessToken(); ?>&act=ghost_set">On Ghost Mode</a>
                                                <?php
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-left">User active chats (not removed):</td>
                                    <td>
                                        <?php
                                            $active_chats = $messages->myActiveChatsCount();

                                            echo $active_chats;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            if ($active_chats > 0) {

                                                ?>
                                                    <a href="/admin/profile_chats.php/?id=<?php echo $accountInfo['id']; ?>" >View</a></td>
                                                <?php
                                            }
                                        ?>
                                </tr>

                            </table>

                            <div style="margin-top: 15px; text-align: right">
                                <a class="primary_btn big_btn" href="/admin/personal_gcm.php/?id=<?php echo $accountInfo['id']; ?>">Send Personal Message (GCM)</a>
                            </div>

                            <div class="header" style="">
                                <div class="title">
                                    <span>Edit account</span>
                                </div>
                            </div>

                            <form method="post" action="/admin/profile.php/?id=<?php echo $accountInfo['id']; ?>&access_token=<?php echo admin::getAccessToken(); ?>" class="support_wrap">

                                <input type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">

                                <div style="margin-bottom: 20px; display: block; position: relative;width: 100%;float: left">

                                    <div class="left" style="text-align: center;">

                                        <?php

                                            if (strlen($accountInfo['lowPhotoUrl']) != 0) {

                                                ?>
                                                    <div><label>Photo:</label></div>
                                                    <div><img style="height: 150px; width: 150px; border: 1px solid #ccc" src="<?php echo $accountInfo['normalPhotoUrl'] ?>"></div>
                                                    <div><a target="_blank" href="<?php echo $accountInfo['bigPhotoUrl'] ?>">View full size</a></div>
                                                    <div><a href="/admin/profile.php/?id=<?php echo $accountInfo['id']; ?>&access_token=<?php echo admin::getAccessToken(); ?>&act=delete-photo">Remove</a></div>
                                                <?php
                                            }
                                        ?>

                                    </div>

                                    <div class="left" style="margin-left: 10px; text-align: center;">

                                        <?php

                                            if (strlen($accountInfo['coverUrl']) != 0) {

                                                ?>
                                                    <div><label>Cover:</label></div>
                                                    <div><img style="height: 150px; max-width: 400px; border: 1px solid #ccc" src="<?php echo $accountInfo['coverUrl'] ?>"></div>
                                                    <div><a target="_blank" href="<?php echo $accountInfo['coverUrl'] ?>">View full size</a></div>
                                                    <div><a href="/admin/profile.php/?id=<?php echo $accountInfo['id']; ?>&access_token=<?php echo admin::getAccessToken(); ?>&act=delete-cover">Remove</a></div>
                                                <?php

                                            }
                                        ?>
                                    </div>
                                </div>

                                <div class="ticket_email">
                                    <label for="username" class="noselect">Username:</label>
                                    <div><input id="username" type="text" name="username" value="<?php echo $accountInfo['username']; ?>"></div>
                                </div>

                                <div class="ticket_email">
                                    <label for="fullname" class="noselect">Fullname:</label>
                                    <div><input id="fullname" type="text" name="fullname" value="<?php echo $accountInfo['fullname']; ?>"></div>
                                </div>

                                <div class="ticket_email">
                                    <label for="location" class="noselect">Location:</label>
                                    <div><input id="location" type="text" name="location" value="<?php echo $accountInfo['location']; ?>"></div>
                                </div>

                                <div class="ticket_email">
                                    <label for="fb_page" class="noselect">Facebook page:</label>
                                    <div><input id="fb_page" type="text" name="fb_page" value="<?php echo $accountInfo['fb_page']; ?>"></div>
                                </div>

                                <div class="ticket_email">
                                    <label for="instagram_page" class="noselect">Instagram page:</label>
                                    <div><input id="instagram_page" type="text" name="instagram_page" value="<?php echo $accountInfo['instagram_page']; ?>"></div>
                                </div>

                                <div class="ticket_email">
                                    <label for="email" class="noselect">Email:</label>
                                    <div><input id="email" type="text" name="email" value="<?php echo $accountInfo['email']; ?>"></div>
                                </div>

                                <div class="ticket_email">
                                    <label for="balance" class="noselect">Balance (In Credits):</label>
                                    <div><input id="balance" type="text" name="balance" value="<?php echo $accountInfo['balance']; ?>"></div>
                                </div>

                                <div class="ticket_controls">
                                    <button class="primary_btn big_btn">Save</button>
                                </div>
                            </form>

                            <div class="header" style="margin-top: 30px;">
                                <div class="title">
                                    <span>Authorizations</span>
                                </div>
                            </div>

                            <div style="margin-bottom: 10px; text-align: right">
                                <a class="primary_btn big_btn" href="/admin/profile.php/?id=<?php echo $accountInfo['id']; ?>&access_token=<?php echo admin::getAccessToken(); ?>&act=close">Close all authorizations</a>
                            </div>

                            <?php

                                $result = $stats->getAuthData($accountInfo['id'], 0);

                                $inbox_loaded = count($result['data']);

                                if ($inbox_loaded != 0) {

                                    ?>

                                        <table class="admin_table">
                                            <tr>
                                                <th class="text-left">Id</th>
                                                <th>Access token</th>
                                                <th>Client Id</th>
                                                <th>Create At</th>
                                                <th>Close At</th>
                                                <th>User agent</th>
                                                <th>Ip address</th>
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

    function draw($authObj)
    {
        ?>

        <tr>
            <td class="text-left"><?php echo $authObj['id']; ?></td>
            <td><?php echo $authObj['accessToken']; ?></td>
            <td><?php echo $authObj['clientId']; ?></td>
            <td><?php echo date("Y-m-d H:i:s", $authObj['createAt']); ?></td>
            <td><?php if ($authObj['removeAt'] == 0) {echo "-";} else {echo date("Y-m-d H:i:s", $authObj['removeAt']);} ?></td>
            <td><?php echo $authObj['u_agent']; ?></td>
            <td><?php if (!APP_DEMO) {echo $authObj['ip_addr'];} else {echo "It is not available in the demo version";} ?></td>
        </tr>

        <?php
    }