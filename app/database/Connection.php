<?php

namespace app\database;

use PDO;
use PDOException;

class Connection
{
    private static $pdo = null;

    public static function connection()
    {
        if (static::$pdo) {
            return static::$pdo;
        }

        try {
            static::$pdo = new PDO('mysql:host=localhost;dbname=piqued26_vortex2', 'piqued26_admin', '12345', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
            ]);
            /*
            static::$pdo = new PDO('mysql:host=localhost;dbname=piqued26_vortex2', 'piqued26_admin', '12345'
            static::$pdo = new PDO('mysql:host=localhost;dbname=blog_slim4', 'root', '1234'
            */
            return static::$pdo;
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
    }
}


   /* $app = new Slim\App([

        'settings' => [

            'displayErrorDetails' => true,

            'db' => [

                'driver'     => 'mysql',

                'host'       => 'localhost',

                'database'   => 'piqued26_vortex2',

                'username'   => 'piqued26_admin',

                'password'   => '12345' 

            ]

        ]

    ]);*/