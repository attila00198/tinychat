<?php

class UserController
{
    public static function register()
    {
        echo "[ERROR]: Not implemented!";
    }

    public static function getUser(int $id)
    {
        if (!self::validateRequest($id)) return false;
        //TODO: Implement calling the API for the actual user data.
        echo "[ERROR]: Not implemented";
        return [];
    }

    public static function getAllUser()
    {
        echo "[ERROR]: Not implemented";
    }

    private static function validateRequest(int $id)
    {
        if (!isset($_SESSION["user"]) || !isset($_COOKIE["access_token"]) || $id != $_SESSION["user"]["id"]) {
            return false;
        }
        return true;
    }
}
