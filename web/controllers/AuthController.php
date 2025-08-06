<?php

class AuthController
{
    public static function login()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["login"])) {
                $username = $_POST["username"];
                $password = $_POST["password"];

                $data = json_encode([
                    "username" => $username,
                    "password" => $password
                ]);

                $opts = [
                    "http" => [
                        "method" => "POST",
                        "header" => "Content-Type: application/json",
                        "content" => $data
                    ]
                ];

                $context = stream_context_create($opts);
                $result = file_get_contents("http://localhost:8000/login", false, $context);

                if ($result === false) {
                    if (str_contains($http_response_header[0], "401 Unauthorized")) {
                        header("location: /login");
                        $_SESSION["message"] = ["error" => "Wrong username or password"];
                        exit(1);
                    } else {
                        http_response_code(500);
                        die("[ERROR]: Backend elérhetetlen vagy válasz hiba.");
                    }
                }
                $respons = json_decode($result, true);

                if (isset($respons["access_token"])) {
                    $user = [
                        "id" => $respons["id"],
                        "username" => $respons["username"],
                        "role" => $respons["role"]
                    ];
                    $_SESSION["user"] = $user;
                    setcookie("access_token", $respons["access_token"], time() + 1800, "/");
                    header("location: /");
                } else {
                    http_response_code(500);
                    die("[ERROR]: Login failed for unknown reasone.");
                }
            }
        }
    }

    public static function logout(): void
    {
        if (session_destroy() === true) {
            echo "[INFO]: Session Destroyed.";
            setcookie("access_token", "", time() - 3600, "/");
            setcookie("PHPSESSID", "", time() - 3600, "/");
            header("location: /login");
            exit(0);
        } else {
            echo "[ERROR]: Could not distroy session.";
            exit(1);
        }
    }

    public static function register(string $username, string $password, string $email): void
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (empty($username) || empty($password) || empty($email)) {
                $_SESSION["flash"] = ["error" => "All maked fields are required."];
            }

            // TODO: Implemet sending userdata to database.
            $_SESSION["flash"] = ["info" => "You are registered as " . $username];
        }
    }

    public static function getUser(string $access_key): array
    {
        return [];
    }
}
