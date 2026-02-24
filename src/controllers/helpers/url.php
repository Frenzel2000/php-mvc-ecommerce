<?php

// Hilfsfunktion, um URLs für Assets zu generieren
function asset_url(string $path): string
{
    $base = rtrim(BASE_URL, '/');

    // externe URLs so lassen
    if (preg_match('~^(https?:)?//~i', $path) || str_starts_with($path, 'data:')) {
        return $path;
    }

    // wenn schon BASE_URL drin ist
    if (str_starts_with($path, $base . '/')) {
        return $path;
    }

    // wenn absolut ab Domain root: "/assets/..." -> zu "/BASE_URL/assets/..."
    if (str_starts_with($path, '/')) {
        return $base . $path;
    }

    // sonst relativ: "assets/..." -> "/BASE_URL/assets/..."
    return $base . '/' . $path;
}
