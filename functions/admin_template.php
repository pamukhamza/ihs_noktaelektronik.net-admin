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
        $currentPage = $this->currentPage;
        include __DIR__ . '/../pages/components/admin_head.php';
    }

    public function header() {
        $currentPage = $this->currentPage; // Pass current page to header
        include  __DIR__ . '/../pages/components/admin_header.php';
    }

    public function footer() {
        include  __DIR__ . '/../pages/components/admin_footer.php';
    }
}

?>
