<?php

namespace app\classes;

use app\database\models\ModelUser;

class Login
{
    public function login($email, $password)
    {
        $user = new ModelUser;
        $userFound = $user->findBy('email', $email);

        if (!$userFound) {
            return false;
        }

        if (password_verify($password, $userFound->password)) {
            $_SESSION['user_logged_data'] = [
                'name' => $userFound->name,
                'email' => $userFound->email,
            ];
            $_SESSION['is_logged_in'] = true;
            return true;
        }

        return false;
    }

    public function logout()
    {
        unset($_SESSION['user_logged_data'], $_SESSION['is_logged_in']);
        session_destroy();
    }
}
