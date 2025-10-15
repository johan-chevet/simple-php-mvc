<?php

/**
 * Génère une URL absolue
 */
function url($path = '')
{
    $base_url = rtrim(BASE_URL, '/');
    $path = ltrim($path, '/');
    return "$base_url/$path";
}

function int_validation(string $nb, ?int $min = null, ?int $max = null): bool
{
    $nb_int = filter_var($nb, FILTER_VALIDATE_INT);

    if ($nb_int === false) {
        return false;
    }

    if ($min != null && $nb_int < $min) {
        return false;
    }

    if ($max != null && $nb_int > $max) {
        return false;
    }

    return true;
}

function load_view(string $path)
{
    ob_start();
    include VIEW_PATH . "/$path.php";
    return ob_get_clean();
}
