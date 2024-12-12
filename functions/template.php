<?php
class Template {
    private $title;
    private $currentPage;

    public function __construct($title, $currentPage) {
        $this->title = $title;
        $this->currentPage = $currentPage;
    }

    public function head() {
        $title = $this->title;
        include 'pages/components/head.php';
    }

    public function header() {
        $currentPage = $this->currentPage; // Pass current page to header
        include 'pages/components/header.php';
    }

    public function footer() {
        include 'pages/components/footer.php';
    }

    public function mobile_menu() {
        include 'pages/components/mobile-menu.php';
    }

    public function canvas_search() {
        include 'pages/components/canvas-search.php';
    }
}

?>
