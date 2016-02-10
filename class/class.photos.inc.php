<?php

/*!
 * ifsoft.co.uk engine v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * qascript@ifsoft.co.uk
 *
 * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
 */

class photos extends db_connect
{
	private $requestFrom = 0;
    private $language = 'en';
    private $profileId = 0;

	public function __construct($dbo = NULL)
    {
		parent::__construct($dbo);
	}

    public function getAllCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM photos");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    private function getMaxId()
    {
        $stmt = $this->db->prepare("SELECT MAX(id) FROM photos");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function count()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM photos WHERE fromUserId = (:fromUserId) AND removeAt = 0");
        $stmt->bindParam(":fromUserId", $this->requestFrom, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function add($mode, $comment, $originImgUrl = "", $previewImgUrl = "", $imgUrl = "", $photoArea = "", $photoCountry = "", $photoCity = "", $photoLat = "", $photoLng = "")
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        if (strlen($originImgUrl) == 0 && strlen($previewImgUrl) == 0 && strlen($imgUrl) == 0) {

            return $result;
        }

        if (strlen($comment) != 0) {

            $comment = $comment." ";
        }

        $currentTime = time();
        $ip_addr = helper::ip_addr();
        $u_agent = helper::u_agent();

        $stmt = $this->db->prepare("INSERT INTO photos (fromUserId, accessMode, comment, originImgUrl, previewImgUrl, imgUrl, area, country, city, lat, lng, createAt, ip_addr, u_agent) value (:fromUserId, :accessMode, :comment, :originImgUrl, :previewImgUrl, :imgUrl, :area, :country, :city, :lat, :lng, :createAt, :ip_addr, :u_agent)");
        $stmt->bindParam(":fromUserId", $this->requestFrom, PDO::PARAM_INT);
        $stmt->bindParam(":accessMode", $mode, PDO::PARAM_INT);
        $stmt->bindParam(":comment", $comment, PDO::PARAM_STR);
        $stmt->bindParam(":originImgUrl", $originImgUrl, PDO::PARAM_STR);
        $stmt->bindParam(":previewImgUrl", $previewImgUrl, PDO::PARAM_STR);
        $stmt->bindParam(":imgUrl", $imgUrl, PDO::PARAM_STR);
        $stmt->bindParam(":area", $photoArea, PDO::PARAM_STR);
        $stmt->bindParam(":country", $photoCountry, PDO::PARAM_STR);
        $stmt->bindParam(":city", $photoCity, PDO::PARAM_STR);
        $stmt->bindParam(":lat", $photoLat, PDO::PARAM_STR);
        $stmt->bindParam(":lng", $photoLng, PDO::PARAM_STR);
        $stmt->bindParam(":createAt", $currentTime, PDO::PARAM_INT);
        $stmt->bindParam(":ip_addr", $ip_addr, PDO::PARAM_STR);
        $stmt->bindParam(":u_agent", $u_agent, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array("error" => false,
                            "error_code" => ERROR_SUCCESS,
                            "photoId" => $this->db->lastInsertId(),
                            "photo" => $this->info($this->db->lastInsertId()));

            $account = new account($this->db, $this->requestFrom);
            $account->updateCounters();
            unset($account);
        }

        return $result;
    }

    public function remove($photoId)
    {
        $result = array("error" => true);

        $photoInfo = $this->info($photoId);

        if ($photoInfo['error'] === true) {

            return $result;
        }

        if ($photoInfo['fromUserId'] != $this->requestFrom) {

            return $result;
        }

        $currentTime = time();

        $stmt = $this->db->prepare("UPDATE photos SET removeAt = (:removeAt) WHERE id = (:photoId)");
        $stmt->bindParam(":photoId", $photoId, PDO::PARAM_INT);
        $stmt->bindParam(":removeAt", $currentTime, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array("error" => false);

            $account = new account($this->db, $photoInfo['fromUserId']);
            $account->updateCounters();
            unset($account);
        }

        return $result;
    }

    public function restore($photoId)
    {
        $result = array("error" => true);

        $photoInfo = $this->info($photoId);

        if ($photoInfo['error'] === true) {

            return $result;
        }

        $stmt = $this->db->prepare("UPDATE photos SET removeAt = 0 WHERE id = (:photoId)");
        $stmt->bindParam(":photoId", $photoId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array("error" => false);
        }

        return $result;
    }

    public function info($photoId)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("SELECT * FROM photos WHERE id = (:photoId) LIMIT 1");
        $stmt->bindParam(":photoId", $photoId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                $time = new language($this->db, $this->language);

                $profile = new profile($this->db, $row['fromUserId']);
                $profileInfo = $profile->get();
                unset($profile);

                $result = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "id" => $row['id'],
                                "accessMode" => $row['accessMode'],
                                "fromUserId" => $row['fromUserId'],
                                "fromUserVerify" => $profileInfo['verify'],
                                "fromUserUsername" => $profileInfo['username'],
                                "fromUserFullname" => $profileInfo['fullname'],
                                "fromUserPhoto" => $profileInfo['lowPhotoUrl'],
                                "comment" => htmlspecialchars_decode(stripslashes($row['comment'])),
                                "area" => htmlspecialchars_decode(stripslashes($row['area'])),
                                "country" => htmlspecialchars_decode(stripslashes($row['country'])),
                                "city" => htmlspecialchars_decode(stripslashes($row['city'])),
                                "lat" => $row['lat'],
                                "lng" => $row['lng'],
                                "imgUrl" => $row['imgUrl'],
                                "previewImgUrl" => $row['previewImgUrl'],
                                "originImgUrl" => $row['originImgUrl'],
                                "rating" => $row['rating'],
                                "commentsCount" => $row['commentsCount'],
                                "likesCount" => $row['likesCount'],
                                "createAt" => $row['createAt'],
                                "date" => date("Y-m-d H:i:s", $row['createAt']),
                                "timeAgo" => $time->timeAgo($row['createAt']),
                                "removeAt" => $row['removeAt']);
            }
        }

        return $result;
    }

    public function get($profileId, $photoId = 0, $accessMode = 0)
    {
        if ($photoId == 0) {

            $photoId = $this->getMaxId();
            $photoId++;
        }

        $photos = array("error" => false,
                       "error_code" => ERROR_SUCCESS,
                       "photoId" => $photoId,
                       "photos" => array());

        if ($accessMode == 0) {

            $stmt = $this->db->prepare("SELECT id FROM photos WHERE accessMode = 0 AND fromUserId = (:fromUserId) AND removeAt = 0 AND id < (:photoId) ORDER BY id DESC LIMIT 16");
            $stmt->bindParam(':fromUserId', $profileId, PDO::PARAM_INT);
            $stmt->bindParam(':photoId', $photoId, PDO::PARAM_INT);

        } else {

            $stmt = $this->db->prepare("SELECT id FROM photos WHERE fromUserId = (:fromUserId) AND removeAt = 0 AND id < (:photoId) ORDER BY id DESC LIMIT 16");
            $stmt->bindParam(':fromUserId', $profileId, PDO::PARAM_INT);
            $stmt->bindParam(':photoId', $photoId, PDO::PARAM_INT);
        }

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $photoInfo = $this->info($row['id']);

                array_push($photos['photos'], $photoInfo);

                $photos['photoId'] = $photoInfo['id'];

                unset($photoInfo);
            }
        }

        return $photos;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function setRequestFrom($requestFrom)
    {
        $this->requestFrom = $requestFrom;
    }

    public function getRequestFrom()
    {
        return $this->requestFrom;
    }

    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;
    }

    public function getProfileId()
    {
        return $this->profileId;
    }
}
