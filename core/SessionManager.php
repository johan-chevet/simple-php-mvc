<?php

namespace Core;

use Src\Models\User;

class SessionManager
{

    public static function set(string $key, mixed $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function get_user_id()
    {
        return static::get('user_id');
    }

    public static function is_logged(): bool
    {
        return static::get_user_id() ? true : false;
    }

    public static function start()
    {
        session_start();
    }

    public static function destroy()
    {
        session_destroy();
    }
}
