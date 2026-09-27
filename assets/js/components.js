/* Pejvak Hesab — Shared Components (Header / Footer / Drawers / Modals) */
/* ---------- Lucide icons (local UMD build: assets/js/lucide.min.js) ---------- */
const ICON_MAP = {
  search: 'search', cart: 'shopping-cart', user: 'user', compare: 'git-compare-arrows',
  heart: 'heart', menu: 'menu', x: 'x', phone: 'phone', mail: 'mail', pin: 'map-pin',
  clock: 'clock', chevDown: 'chevron-down', arrowLeft: 'arrow-left', arrowUp: 'arrow-up',
  check: 'check', checkCircle: 'circle-check-big', shield: 'shield-check', truck: 'truck',
  headset: 'headset', wrench: 'wrench', star: 'star', package: 'package', printer: 'printer',
  scan: 'scan-line', tag: 'tag', monitor: 'monitor', archive: 'archive', display: 'tv',
  chart: 'chart-column', filter: 'list-filter', grid: 'layout-grid', list: 'list',
  plus: 'plus', minus: 'minus', trash: 'trash-2', eye: 'eye', send: 'send', users: 'users',
  card: 'credit-card', cash: 'banknote', store: 'store', shirt: 'shirt', mobile: 'smartphone',
  coffee: 'coffee', home: 'house', briefcase: 'briefcase', gift: 'gift', book: 'book-open',
  layers: 'layers', server: 'server', key: 'key-round', logout: 'log-out', edit: 'pencil',
  cal: 'calendar', message: 'message-circle', award: 'award', spark: 'sparkles',
  wifi: 'wifi', cpu: 'cpu', instagram: 'camera', telegram: 'send',
  whatsapp: 'message-circle', linkedin: 'briefcase'
};
/* Proxy: every `${Icons.name}` emits a placeholder that hydrateIcons() turns into a Lucide SVG */
const Icons = new Proxy({}, { get: (_, n) => (typeof n === 'string' ? `<i data-icon="${n}"></i>` : '') });
function hydrateIcons(root) {
  const scope = (root instanceof Element) ? root : document;
  const fresh = scope.querySelectorAll('[data-icon]:not(svg):not([data-lucide])');
  if (!fresh.length) return false;
  fresh.forEach(el => el.setAttribute('data-lucide', ICON_MAP[el.getAttribute('data-icon')] || 'circle'));
  if (window.lucide && window.lucide.createIcons) window.lucide.createIcons();
  return true;
}
function logoSVG() {
  return `<svg class="logo-mark" viewBox="0 0 48 48" fill="none" aria-hidden="true">
    <rect x="1" y="1" width="46" height="46" rx="13" fill="#07242D"/>
    <rect x="1" y="1" width="46" height="46" rx="13" fill="url(#lg1)" fill-opacity=".55"/>
    <path d="M14 34 26 12h6L20 34h-6Z" fill="#0FA3BC"/>
    <path d="M23 34 35 12h6L29 34h-6Z" fill="#F0B429"/>
    <defs><linearGradient id="lg1" x1="0" y1="0" x2="48" y2="48"><stop stop-color="#0FA3BC"/><stop offset="1" stop-color="#00798C" stop-opacity="0"/></linearGradient></defs>
  </svg>`;
}
/* ---------- Header ---------- */
const NAV = [
  { t: 'صفحه اصلی', h: 'index.html', id: 'home' },
  { t: 'نرم‌افزار حسابداری', h: 'shop.html?group=software', id: 'software', mega: 'sw' },
  { t: 'تجهیزات فروشگاهی', h: 'hardware.html', id: 'hardware', mega: 'hw' },
  { t: 'راهکارهای فروشگاهی', h: 'index.html#solutions', id: 'solutions', mega: 'sol' },
  { t: 'خدمات', h: 'services.html', id: 'services' },
  { t: 'درباره ما', h: 'about.html', id: 'about' },
  { t: 'تماس با ما', h: 'contact.html', id: 'contact' }
];
function megaHTML(kind) {
  if (kind === 'sw') return `<div class="mega"><div class="container mega-inner">
    <div class="mega-col"><h4>نرم‌افزارها</h4><ul>
      <li><a href="software.html?id=baran"><span data-icon="chart"></span><span>نرم‌افزار باران<small>محبوب فروشگاه‌ها و سوپرمارکت‌ها</small></span></a></li>
      <li><a href="software.html?id=zafaran"><span data-icon="shirt"></span><span>نرم‌افزار زعفران<small>ویژه پوشاک و فروش زنجیره‌ای</small></span></a></li>
      <li><a href="software.html?id=pejvak"><span data-icon="briefcase"></span><span>نرم‌افزار پژواک<small>حسابداری جامع شرکتی</small></span></a></li>
      <li><a href="software.html?id=pos-suite"><span data-icon="coffee"></span><span>مجموعه فروشگاهی<small>رستوران، کافه و فست‌فود</small></span></a></li>
    </ul></div>
    <div class="mega-col"><h4>راهنمای انتخاب</h4><ul>
      <li><a href="compare.html"><span data-icon="compare"></span><span>مقایسه نرم‌افزارها</span></a></li>
      <li><a href="index.html#wizard"><span data-icon="spark"></span><span>کدام نرم‌افزار برای من مناسب است؟</span></a></li>
      <li><a href="blog.html"><span data-icon="book"></span><span>راهنمای خرید نرم‌افزار</span></a></li>
      <li><a href="faq.html"><span data-icon="message"></span><span>سوالات متداول</span></a></li>
    </ul></div>
    <div class="mega-col"><h4>بر اساس کسب‌وکار</h4><ul>
      <li><a href="solution.html?biz=supermarket"><span data-icon="store"></span><span>سوپرمارکت</span></a></li>
      <li><a href="solution.html?biz=clothing"><span data-icon="shirt"></span><span>پوشاک</span></a></li>
      <li><a href="solution.html?biz=restaurant"><span data-icon="coffee"></span><span>رستوران و کافه</span></a></li>
      <li><a href="solution.html?biz=wholesale"><span data-icon="package"></span><span>عمده‌فروشی</span></a></li>
    </ul></div>
    <div class="mega-banner"><h5>در انتخاب نرم‌افزار مطمئن نیستید؟</h5><p>کارشناسان پژواک حساب رایگان راهنمایی‌تان می‌کنند.</p><button class="btn btn-accent btn-sm" data-consult>مشاوره رایگان</button></div>
  </div></div>`;
  if (kind === 'hw') return `<div class="mega"><div class="container mega-inner">
    <div class="mega-col"><h4>چاپ و اسکن</h4><ul>
      <li><a href="category.html?cat=receipt-printers"><span data-icon="printer"></span><span>فیش پرینتر</span></a></li>
      <li><a href="category.html?cat=label-printers"><span data-icon="tag"></span><span>لیبل پرینتر</span></a></li>
      <li><a href="category.html?cat=scanners"><span data-icon="scan"></span><span>بارکدخوان</span></a></li>
      <li><a href="shop.html"><span data-icon="grid"></span><span>همه تجهیزات</span></a></li>
    </ul></div>
    <div class="mega-col"><h4>صندوق و پرداخت</h4><ul>
      <li><a href="category.html?cat=pos-systems"><span data-icon="monitor"></span><span>صندوق فروشگاهی</span></a></li>
      <li><a href="category.html?cat=ready-systems"><span data-icon="package"></span><span>سیستم‌های آماده</span></a></li>
      <li><a href="category.html?cat=cash-drawers"><span data-icon="archive"></span><span>کشوی پول</span></a></li>
      <li><a href="category.html?cat=customer-displays"><span data-icon="display"></span><span>نمایشگر مشتری</span></a></li>
    </ul></div>
    <div class="mega-col"><h4>خدمات تجهیزات</h4><ul>
      <li><a href="services.html"><span data-icon="wrench"></span><span>نصب و راه‌اندازی</span></a></li>
      <li><a href="services.html"><span data-icon="shield"></span><span>گارانتی و خدمات پس از فروش</span></a></li>
      <li><a href="compare.html"><span data-icon="compare"></span><span>مقایسه تجهیزات</span></a></li>
    </ul></div>
    <div class="mega-banner"><h5>سیستم آماده فروشگاهی</h5><p>فقط وصل کنید و شروع کنید؛ با نصب و آموزش رایگان.</p><a class="btn btn-accent btn-sm" href="category.html?cat=ready-systems">مشاهده سیستم‌ها</a></div>
  </div></div>`;
  return `<div class="mega"><div class="container mega-inner">
    <div class="mega-col"><h4>خرده‌فروشی</h4><ul>
      <li><a href="solution.html?biz=supermarket"><span data-icon="store"></span><span>سوپرمارکت و هایپرمارکت</span></a></li>
      <li><a href="solution.html?biz=clothing"><span data-icon="shirt"></span><span>فروشگاه پوشاک</span></a></li>
      <li><a href="solution.html?biz=mobile"><span data-icon="mobile"></span><span>فروشگاه موبایل</span></a></li>
      <li><a href="solution.html?biz=home"><span data-icon="home"></span><span>لوازم خانگی</span></a></li>
    </ul></div>
    <div class="mega-col"><h4>غذا، عمده و خدمات</h4><ul>
      <li><a href="solution.html?biz=restaurant"><span data-icon="coffee"></span><span>رستوران و کافه</span></a></li>
      <li><a href="solution.html?biz=chain"><span data-icon="layers"></span><span>فروشگاه زنجیره‌ای</span></a></li>
      <li><a href="solution.html?biz=wholesale"><span data-icon="package"></span><span>عمده‌فروشی</span></a></li>
      <li><a href="solution.html?biz=service"><span data-icon="briefcase"></span><span>کسب‌وکارهای خدماتی</span></a></li>
    </ul></div>
    <div class="mega-col"><h4>چرا راهکار آماده؟</h4><ul>
      <li><a href="services.html"><span data-icon="users"></span><span>مشاوره قبل از خرید</span></a></li>
      <li><a href="services.html"><span data-icon="wrench"></span><span>نصب، آموزش و پشتیبانی</span></a></li>
      <li><a href="about.html"><span data-icon="award"></span><span>چرا پژواک حساب؟</span></a></li>
    </ul></div>
    <div class="mega-banner"><h5>راهکار مناسب کسب‌وکار شما؟</h5><p>در ۲ دقیقه، نرم‌افزار مناسب خود را پیدا کنید.</p><a class="btn btn-accent btn-sm" href="index.html#wizard">شروع راهنمای انتخاب</a></div>
  </div></div>`;
}
function renderHeader(active) {
  if (document.getElementById('wpHeader')) return; /* WordPress renders header in header.php */
  const s = PH.site;
  const links = NAV.map(n => `<div class="has-mega" style="position:${n.mega ? 'static' : 'relative'}">
    <a class="nav-link ${active === n.id ? 'active' : ''}" href="${n.h}">${n.t}${n.mega ? '<span data-icon="chevDown"></span>' : ''}</a>
    ${n.mega ? megaHTML(n.mega) : ''}</div>`).join('');
  document.getElementById('site-header').innerHTML = `
  <div class="topbar"><div class="container">
    <div class="topbar-right">
      <a class="topbar-item" href="tel:+989120241120"><span data-icon="phone"></span><strong class="num">${s.phone}</strong></a>
      <span class="topbar-sep hide-m"></span>
      <span class="topbar-item hide-m"><span data-icon="clock"></span>${s.hours}</span>
    </div>
    <div class="topbar-left">
      <a class="topbar-item hide-m" href="account.html">پیگیری سفارش</a>
      <a class="topbar-item hide-m" href="faq.html">سوالات متداول</a>
      <a class="topbar-item" href="blog.html">مجله پژواک</a>
    </div>
  </div></div>
  <header class="site-header" id="mainHeader"><div class="container header-inner" style="position:relative">
    <a class="logo" href="index.html" aria-label="پژواک حساب">${logoSVG()}
      <span class="logo-text"><span class="logo-fa"><span class="t">پژواک</span> <span class="a">حساب</span></span><span class="logo-en">Pejvak Hesab</span></span>
    </a>
    <nav class="main-nav" aria-label="ناوبری اصلی">${links}</nav>
    <div class="header-actions">
      <button class="icon-btn" id="btnSearch" aria-label="جستجو"><span data-icon="search"></span></button>
      <a class="icon-btn" href="compare.html" aria-label="مقایسه"><span data-icon="compare"></span><span class="count" id="cmpCount" style="display:none">۰</span></a>
      <a class="icon-btn" href="account.html" aria-label="حساب کاربری"><span data-icon="user"></span></a>
      <button class="icon-btn" id="btnCart" aria-label="سبد خرید"><span data-icon="cart"></span><span class="count" id="cartCount" style="display:none">۰</span></button>
      <button class="btn btn-accent btn-sm header-cta" data-consult><span data-icon="headset"></span><span>مشاوره رایگان</span></button>
      <button class="icon-btn hamburger" id="btnMenu" aria-label="منو"><span data-icon="menu"></span></button>
    </div>
  </div></header>`;
}
/* ---------- Footer ---------- */
function renderFooter() {
  if (document.getElementById('wpFooter')) return; /* WordPress renders footer in footer.php */
  const s = PH.site;
  document.getElementById('site-footer').innerHTML = `
  <footer class="site-footer"><div class="container">
    <div class="footer-main">
      <div class="footer-brand">
        <a class="logo" href="index.html">${logoSVG()}<span class="logo-text"><span class="logo-fa"><span class="t">پژواک</span> <span class="a">حساب</span></span><span class="logo-en">Pejvak Hesab</span></span></a>
        <p>مجموعه تخصصی ارائه‌دهنده نرم‌افزارهای حسابداری، تجهیزات فروشگاهی و راهکارهای یکپارچه فروش؛ از مشاوره و انتخاب تا نصب، آموزش و پشتیبانی در کنار کسب‌وکار شما هستیم.</p>
        <ul class="footer-contact">
          <li><span data-icon="phone"></span><span>تلفن مشاوره و فروش: <strong class="num">${s.phone}</strong></span></li>
          <li><span data-icon="mail"></span><span dir="ltr">${s.email}</span></li>
          <li><span data-icon="pin"></span><span>${s.address}</span></li>
        </ul>
        <div class="socials">
          <a href="#" aria-label="اینستاگرام"><span data-icon="instagram"></span></a>
          <a href="#" aria-label="تلگرام"><span data-icon="telegram"></span></a>
          <a href="#" aria-label="واتساپ"><span data-icon="whatsapp"></span></a>
          <a href="#" aria-label="لینکدین"><span data-icon="linkedin"></span></a>
        </div>
      </div>
      <div class="footer-col"><h4>دسترسی سریع</h4><ul>
        <li><a href="about.html">درباره ما</a></li><li><a href="contact.html">تماس با ما</a></li>
        <li><a href="shop.html">فروشگاه</a></li><li><a href="hardware.html">تجهیزات فروشگاهی</a></li>
        <li><a href="services.html">خدمات</a></li><li><a href="blog.html">مجله پژواک حساب</a></li>
        <li><a href="faq.html">سوالات متداول</a></li>
      </ul></div>
      <div class="footer-col"><h4>نرم‌افزارها</h4><ul>
        <li><a href="software.html?id=baran">نرم‌افزار باران</a></li><li><a href="software.html?id=zafaran">نرم‌افزار زعفران</a></li>
        <li><a href="software.html?id=pejvak">نرم‌افزار پژواک</a></li><li><a href="software.html?id=pos-suite">مجموعه فروشگاهی</a></li>
        <li><a href="compare.html">مقایسه نرم‌افزارها</a></li><li><a href="index.html#wizard">راهنمای انتخاب نرم‌افزار</a></li>
      </ul></div>
      <div class="footer-col"><h4>عضویت در خبرنامه</h4>
        <p style="font-size:13px">آموزش‌ها، تخفیف‌ها و راهنمای خرید تجهیزات را دریافت کنید.</p>
        <form class="newsletter" id="nlForm"><input class="input" type="email" placeholder="ایمیل شما" required aria-label="ایمیل"><button class="btn btn-accent btn-sm" type="submit">عضویت</button></form>
        <div class="trust-row">
          <span class="trust-chip"><span data-icon="shield"></span>ضمانت اصالت کالا</span>
          <span class="trust-chip"><span data-icon="truck"></span>ارسال به سراسر کشور</span>
          <span class="trust-chip"><span data-icon="headset"></span>پشتیبانی تخصصی</span>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© ۱۴۰۵ پژواک حساب — تمامی حقوق محفوظ است. <span>· طراحی و توسعه: <a href="https://onecode.ir" target="_blank" rel="noopener" style="color:var(--accent-bright);font-weight:700">وان کد | onecode.ir</a></span> <a href="#" style="text-decoration:underline">قوانین و مقررات</a> · <a href="#" style="text-decoration:underline">حریم خصوصی</a></span>
      <span class="en">© Pejvak Hesab — Retail Technology Solutions</span>
    </div>
  </div></footer>`;
}
/* ---------- Global shells (drawers/modals/search/toast) ---------- */
function renderShells() {
  const TAGS = (window.PH_WP && PH_WP.searchTags && PH_WP.searchTags.length ? PH_WP.searchTags : ['فیش پرینتر', 'باران', 'بارکدخوان', 'صندوق', 'لیبل']);
  document.body.insertAdjacentHTML('beforeend', `
  <div class="overlay" id="overlay"></div>
  <aside class="drawer right" id="drawerNav" aria-label="منوی موبایل">
    <div class="drawer-head"><a class="logo" href="${(window.PH_WP && PH_WP.home) || 'index.html'}">${logoSVG()}<span class="logo-text"><span class="logo-fa"><span class="t">پژواک</span> <span class="a">حساب</span></span></span></a><button class="icon-btn" data-close aria-label="بستن"><span data-icon="x"></span></button></div>
    <div class="drawer-body" id="mnavBody"></div>
    <div class="drawer-foot"><button class="btn btn-accent btn-block" data-consult><span data-icon="headset"></span>دریافت مشاوره رایگان</button></div>
  </aside>
  <aside class="drawer left" id="drawerCart" aria-label="سبد خرید">
    <div class="drawer-head"><h3>سبد خرید شما</h3><button class="icon-btn" data-close aria-label="بستن"><span data-icon="x"></span></button></div>
    <div class="drawer-body" id="miniCartBody"></div>
    <div class="drawer-foot" id="miniCartFoot"></div>
  </aside>
  <div class="modal" id="modalQuick"><div class="modal-box wide"><div class="modal-head"><h3>مشاهده سریع محصول</h3><button class="icon-btn" data-close aria-label="بستن"><span data-icon="x"></span></button></div><div class="modal-body" id="quickBody"></div></div></div>
  <div class="modal" id="modalConsult"><div class="modal-box"><div class="modal-head"><h3 id="consultTitle">درخواست مشاوره رایگان</h3><button class="icon-btn" data-close aria-label="بستن"><span data-icon="x"></span></button></div>
    <div class="modal-body"><p style="font-size:13.5px;color:var(--muted);margin-bottom:18px">فرم زیر را تکمیل کنید تا کارشناسان پژواک حساب در اسرع وقت با شما تماس بگیرند.</p>
    <form id="consultForm" novalidate>
      <div class="form-row"><div class="field"><label>نام و نام خانوادگی <span class="req">*</span></label><input class="input" name="name" placeholder="مثلاً علی محمدی"><div class="form-error">نام را وارد کنید</div></div>
      <div class="field"><label>شماره موبایل <span class="req">*</span></label><input class="input num" name="phone" inputmode="tel" placeholder="۰۹۱۲۰۰۰۰۰۰۰"><div class="form-error">شماره موبایل معتبر وارد کنید</div></div></div>
      <div class="field"><label>موضوع مشاوره</label><select class="input" name="subject" id="consultSubject"><option>نرم‌افزار حسابداری</option><option>تجهیزات فروشگاهی</option><option>سیستم آماده فروشگاهی</option><option>راه‌اندازی فروشگاه جدید</option><option>سایر</option></select></div>
      <div class="field"><label>توضیحات</label><textarea class="input" name="msg" style="min-height:80px" placeholder="نوع کسب‌وکار و نیاز خود را بنویسید..."></textarea></div>
      <button class="btn btn-primary btn-block btn-lg" type="submit"><span data-icon="send"></span>ثبت درخواست مشاوره</button>
      <p class="form-hint" style="text-align:center;margin-top:10px">یا مستقیم تماس بگیرید: <a href="tel:${(window.PH && PH.site && PH.site.phoneDir) || '+989120241120'}" style="font-weight:800;color:var(--brand-strong)" class="num">${PH.site.phone}</a></p>
    </form></div></div></div>
  <div class="search-overlay" id="searchOverlay"><div class="container search-inner">
    <form class="search-box" id="searchForm"><input class="input" id="searchInput" placeholder="جستجو در محصولات، نرم‌افزارها و مقالات..." autocomplete="off"><button class="btn btn-primary" type="submit"><span data-icon="search"></span>جستجو</button><button class="icon-btn" type="button" data-close aria-label="بستن" style="width:52px;height:52px"><span data-icon="x"></span></button></form>
    <div class="search-tags"><span>جستجوهای پرتکرار:</span>${TAGS.map(t => `<button type="button" data-q="${t}">${t}</button>`).join('')}</div>
    <div class="search-results" id="searchResults"></div>
  </div></div>
  <div id="toasts"></div>
  <button class="to-top" id="toTop" aria-label="بازگشت به بالا"><span data-icon="arrowUp"></span></button>`);
  /* mobile nav */
  let mnav = [
    { t: 'صفحه اصلی', h: 'index.html' },
    { t: 'نرم‌افزار حسابداری', sub: [['باران', 'software.html?id=baran', 'chart'], ['زعفران', 'software.html?id=zafaran', 'shirt'], ['پژواک', 'software.html?id=pejvak', 'briefcase'], ['مجموعه فروشگاهی', 'software.html?id=pos-suite', 'coffee'], ['مقایسه نرم‌افزارها', 'compare.html', 'compare']] },
    { t: 'تجهیزات فروشگاهی', sub: [['صندوق فروشگاهی', 'category.html?cat=pos-systems', 'monitor'], ['سیستم‌های آماده', 'category.html?cat=ready-systems', 'package'], ['فیش پرینتر', 'category.html?cat=receipt-printers', 'printer'], ['لیبل پرینتر', 'category.html?cat=label-printers', 'tag'], ['بارکدخوان', 'category.html?cat=scanners', 'scan'], ['کشوی پول', 'category.html?cat=cash-drawers', 'archive'], ['نمایشگر مشتری', 'category.html?cat=customer-displays', 'display'], ['همه محصولات', 'shop.html', 'grid']] },
    { t: 'راهکارهای فروشگاهی', sub: [['سوپرمارکت', 'solution.html?biz=supermarket', 'store'], ['پوشاک', 'solution.html?biz=clothing', 'shirt'], ['رستوران و کافه', 'solution.html?biz=restaurant', 'coffee'], ['عمده‌فروشی', 'solution.html?biz=wholesale', 'package'], ['فروشگاه زنجیره‌ای', 'solution.html?biz=chain', 'layers']] },
    { t: 'خدمات', h: 'services.html' }, { t: 'مجله پژواک', h: 'blog.html' }, { t: 'درباره ما', h: 'about.html' }, { t: 'تماس با ما', h: 'contact.html' }
  ];
  if (window.PH_WP && PH_WP.links) {
    const L = PH_WP.links;
    const sols = (window.PH && PH.solutions) || {};
    const solArr = Object.keys(sols).length ? Object.entries(sols).map(([k, v]) => [v.n, (L.solution || '#') + '?biz=' + k, v.i || 'store']) : [['سوپرمارکت', (L.solution || '#') + '?biz=supermarket', 'store'], ['پوشاک', (L.solution || '#') + '?biz=clothing', 'shirt'], ['رستوران و کافه', (L.solution || '#') + '?biz=restaurant', 'coffee'], ['عمده‌فروشی', (L.solution || '#') + '?biz=wholesale', 'package'], ['فروشگاه زنجیره‌ای', (L.solution || '#') + '?biz=chain', 'layers']];
    mnav = [
      { t: 'صفحه اصلی', h: L.home || '/' },
      { t: 'نرم‌افزار حسابداری', sub: [...PH.software.map(s => [s.name, ((L.software || {})[s.id] || '#'), 'chart']), ['مقایسه نرم‌افزارها', L.compare || '#', 'compare']] },
      { t: 'تجهیزات فروشگاهی', sub: [...PH.cats.filter(c => c.id !== 'software').map(c => [c.name, (L.shop || '#') + '?pcat=' + c.id, c.icon || 'grid']), ['همه محصولات', L.shop || '#', 'grid']] },
      { t: 'راهکارهای فروشگاهی', sub: solArr },
      { t: 'خدمات', h: L.services || '#' }, { t: 'مجله پژواک', h: L.blog || '#' }, { t: 'درباره ما', h: L.about || '#' }, { t: 'تماس با ما', h: L.contact || '#' }
    ];
  }
  document.getElementById('mnavBody').innerHTML = mnav.map(g => g.sub
    ? `<div class="mnav-group"><button class="mnav-btn">${g.t}<span data-icon="chevDown"></span></button><div class="mnav-sub">${g.sub.map(s => `<a href="${s[1]}"><span data-icon="${s[2]}"></span>${s[0]}</a>`).join('')}</div></div>`
    : `<a class="mnav-link" href="${g.h}">${g.t}</a>`).join('');
}
