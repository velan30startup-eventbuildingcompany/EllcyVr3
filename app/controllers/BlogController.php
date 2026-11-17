<?php
final class BlogController
{
    public function index(): void
    {
        $page_title = 'Wedding & Event Planning Blog Chennai | ELLCY';
        $meta_title = $page_title;
        $meta_description = 'Practical wedding and event planning guides for Chennai: vendor selection, budgeting, Tamil marriage services and online booking support.';
        $extra_css = ['seo-content.css'];
        $skip_data_js = true;
        require VIEWS_PATH . '/blog/index.php';
    }

    public function article(): void
    {
        $page_title = 'Complete Wedding & Event Planning Guide Chennai | ELLCY';
        $meta_title = $page_title;
        $meta_description = 'Plan a Tamil wedding or special occasion in Chennai with ELLCY’s guide to vendors, decoration, catering, photography, music and online booking.';
        $extra_css = ['seo-content.css'];
        $skip_data_js = true;
        require VIEWS_PATH . '/blog/complete-wedding-event-planning-chennai.php';
    }
}
