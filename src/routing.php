<?php

//wandelt von .htaccess übergebenen Pfad in controller, action, id um
function resolveRoute(): array
{
    // liest Pfad aus der URL und entfernt führende und abschließende Slashes
    $path = trim($_GET['path'] ?? '', '/');

    // Startseite, wenn kein Pfad angegeben ist
    if ($path === '') {
        return ['home', 'index', null];
    }

     /**
     * Sicherheits- und Format-Check per Regex:
     * Erlaubtes Format: controller/action/id
     *
     * - controller: Muss mit einem Buchstaben beginnen (kein "..", kein Leerzeichen etc.)
     * - action:     Optional, wenn nicht vorhanden -> default "index"
     * - id:         Optional, nur Zahlen (z.B. show/18), damit keine beliebigen Strings
     *               als Parameter an Controller-Methoden gelangen.
     *
     * Zusätzlich erlauben wir Unterstrich/Bindestrich im Namen (z.B. "user-profile").
     */
    if (!preg_match('~^([a-zA-Z][a-zA-Z0-9_-]*)(?:/([a-zA-Z][a-zA-Z0-9_-]*)(?:/([0-9]+))?)?/?$~', $path, $m)) {
        http_response_code(404);
        die('404 Not Found');
    }

    /**
     * Regex-Matches:
     * $m[1] = controller
     * $m[2] = action (optional)
     * $m[3] = id (optional)
     */
    $controllerName = $m[1];
    $actionName     = $m[2] ?? 'index';
    $id             = $m[3] ?? null;

    return [$controllerName, $actionName, $id];
}
