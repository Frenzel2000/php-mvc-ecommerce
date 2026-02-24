<?php

function hasPermission(string $permission): bool
{
    return isset($_SESSION['permissions'])
        && in_array($permission, $_SESSION['permissions'], true);
}

