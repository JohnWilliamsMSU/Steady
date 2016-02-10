<?php

/*!
 * ifsoft.co.uk engine v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * qascript@ifsoft.co.uk
 *
 * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
 */

class update extends db_connect
{
    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);

    }

    function setChatEmojiSupport()
    {
        $stmt = $this->db->prepare("ALTER TABLE messages charset = utf8mb4, MODIFY COLUMN message VARCHAR(800) CHARACTER SET utf8mb4");
        $stmt->execute();
    }

    function setGiftsEmojiSupport()
    {
        $stmt = $this->db->prepare("ALTER TABLE gifts charset = utf8mb4, MODIFY COLUMN message VARCHAR(400) CHARACTER SET utf8mb4");
        $stmt->execute();
    }

    function setPhotosEmojiSupport()
    {
        $stmt = $this->db->prepare("ALTER TABLE photos charset = utf8mb4, MODIFY COLUMN comment VARCHAR(400) CHARACTER SET utf8mb4");
        $stmt->execute();
    }
}
