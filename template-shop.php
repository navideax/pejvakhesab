<?php
/**
 * Template Name: فروشگاه
 * Pejvak Hesab Theme
 */
defined('ABSPATH') || exit;
get_header();
?>
<main>
<div class="page-hero"><div class="container"><div class="breadcrumb-lite"><a href="<?php echo home_url('/'); ?>">خانه</a><span>/</span><span><?php the_title(); ?></span></div><h1 id="shopTitle"><?php the_title(); ?></h1><p id="shopDesc"><?php echo esc_html(get_the_excerpt() ?: 'تجهیزات اصلی با ضمانت شرکتی، تست سازگاری با نرم‌افزار شما و پشتیبانی واقعی'); ?></p></div></div>
<div class="section"><div class="container">
  <div class="shop-cat-chips"><?php $ph_cur = isset($_GET['pcat']) ? sanitize_key($_GET['pcat']) : ''; ?><a href="<?php echo esc_url(ph_url('shop')); ?>" data-cat="" class="<?php echo $ph_cur ? '' : 'active'; ?>">همه محصولات</a><?php $ph_terms = get_terms(['taxonomy' => 'ph_cat', 'hide_empty' => false]); if ($ph_terms && !is_wp_error($ph_terms)) foreach ($ph_terms as $pt) echo '<a href="' . esc_url(ph_url('product-cat')) .  esc_attr($pt->slug) . '" data-cat="' . esc_attr($pt->slug) . '" class="' . ($ph_cur === $pt->slug ? 'active' : '') . '">' . esc_html($pt->name) . '</a>'; ?></div>
  <div class="shop-layout">
    <aside class="filters" id="filters">
      <div class="filters-head"><span><span data-icon="filter"></span> فیلترها</span><div><button id="clearFilters">حذف فیلترها</button><button class="icon-btn" id="closeFilters" style="width:32px;height:32px;margin-inline-start:8px" aria-label="بستن"><span data-icon="x"></span></button></div></div>
      <div class="filter-group"><h4>جستجو در محصولات</h4><input class="input" id="shopSearch" placeholder="نام محصول یا برند..."></div>
      <div class="filter-group"><h4>برند</h4><div id="brandFilters"></div></div>
      <div class="filter-group"><h4>پیشنهادها</h4><label class="check"><input type="checkbox" id="offOnly">فقط کالاهای تخفیف‌دار</label><label class="check"><input type="checkbox" id="stockOnly">فقط کالاهای موجود</label></div>
      <div class="filter-group"><h4>راهنمایی خرید</h4><p style="font-size:13px;color:var(--muted);margin-bottom:12px">برای انتخاب درست، از مشاوره رایگان استفاده کنید.</p><button class="btn btn-accent btn-sm btn-block" data-consult>مشاوره رایگان</button></div>
    </aside>
    <div>
      <div class="toolbar"><span class="count" id="shopCount"></span><select id="sortSel" aria-label="مرتب‌سازی"><option value="new">جدیدترین</option><option value="pop">پرفروش‌ترین</option><option value="cheap">ارزان‌ترین</option><option value="exp">گران‌ترین</option></select>
      <div class="view-toggle"><button id="viewGrid" class="active" aria-label="نمایش شبکه‌ای"><span data-icon="grid"></span></button><button id="viewList" aria-label="نمایش لیستی"><span data-icon="list"></span></button></div></div>
      <div class="p-grid" id="shopGrid"></div>
    </div>
  </div>
</div></div>
</main>
<button class="btn btn-primary filters-fab" id="filtersFab"><span data-icon="filter"></span>فیلترها</button>
<?php get_footer();
