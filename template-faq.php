<?php
/**
 * Template Name: سوالات متداول
 * Pejvak Hesab Theme (generated from static design)
 */
defined('ABSPATH') || exit;
get_header();
?>
<main>
<div class="page-hero"><div class="container"><div class="breadcrumb-lite"><a href="<?php echo esc_url(home_url('/')); ?>">خانه</a><span>/</span><span><?php the_title(); ?></span></div><h1><?php the_title(); ?></h1><p><?php echo esc_html(get_the_excerpt() ?: 'پاسخ سریع به پرتکرارترین سؤالات مشتریان'); ?></p></div></div>
<div class="section"><div class="container faq-layout">
  <aside class="faq-cats" id="faqCats"></aside>
  <div><div class="faq-search"><span data-icon="search"></span><input class="input" id="faqSearch" placeholder="جستجو در سؤالات..."></div><div id="faqList"></div>
  <div class="side-cta" style="margin-top:24px"><h4>پاسخ سؤال خود را پیدا نکردید؟</h4><p>با ما در تماس باشید یا درخواست مشاوره ثبت کنید.</p><div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap"><button class="btn btn-accent" data-consult>مشاوره رایگان</button><a class="btn btn-outline-white" href="<?php echo esc_url(ph_url('contact')); ?>">تماس با ما</a></div></div></div>
</div></div>
</main>
<?php get_footer();
