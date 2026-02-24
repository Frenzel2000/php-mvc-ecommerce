<?php 

abstract class mainView {
    
    private string $title = '';
    private array $stylesheets = [];
    private array $scripts = [];

    public function set_title($title) {
        $this->title = $title; 
    }

    //lädt spezifische css stylesheets
    public function add_css($path)
    {
        $this->stylesheets[] = $path; 
    }

    //lädt spezifisches JS
    public function add_js($path)
    {
        $this->scripts[] = $path;
    }

    final protected function render_header($context)
    {
        /*IDE Hinweis:
        Variablen sind nicht ungenutzt sondern werden in head und header benutzt!!!
        */
        $title = $this->title;
        $categories = $context['navigation_categories'] ?? [];
        $stylesheets = $this->stylesheets;
        $scripts = $this->scripts;

        require __DIR__ . '/layouts/head.php';
        require __DIR__ . '/header.php';
    }

    final protected function render_footer()
    {
        require __DIR__ . '/../../static/html/footer.html';
    }

    final public function render_html($template, $data = [])
    {   
        $this->render_header($data);

        echo '<main class="page-main">';

        if (method_exists(static::class, $template)) {
            call_user_func([static::class, $template], $data);
        } else {
            echo "<pre>Template nicht gefunden: " . htmlspecialchars($template, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</pre>";
        }

    echo '</main>';

        $this->render_footer();
    }
}