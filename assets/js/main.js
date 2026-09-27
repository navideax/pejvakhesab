/* Pejvak Hesab — App Engine (store, renderers, interactions, page inits) */
const $ = (s, r) => (r || document).querySelector(s);
const $$ = (s, r) => Array.from((r || document).querySelectorAll(s));
const faNum = n => Number(n).toLocaleString('fa-IR');
const fmtPrice = n => n && n > 0 ? `<span class="num">${faNum(n)}</span> <small>تومان</small>` : 'تماس بگیرید';
const fmtShort = n => n && n > 0 ? faNum(n) : '—';
const q = k => new URLSearchParams(location.search).get(k);
/* WP-aware link helpers: direct WP URLs when localized, static .html otherwise */
const PHL = (window.PH_WP && PH_WP.links) || null;
const PHH = (window.PH_WP && PH_WP.home) || 'index.html';
const linkHome = (hash) => PHH + (hash || '');
const linkPage = (key, query) => ((PHL && PHL[key]) || (key + '.html')) + (query || '');
const linkProduct = (id) => PHL ? (((PHL.product || {})[id]) || PHL.shop || '#') : ('product.html?id=' + id);
const linkSoftware = (id) => PHL ? (((PHL.software || {})[id]) || PHL.shop || '#') : ('software.html?id=' + id);
const linkArticle = (id) => PHL ? (((PHL.article || {})[id]) || PHL.blog || '#') : ('article.html?id=' + id);
const linkCat = (cat) => PHL ? ((PHL.shop || '#') + '?pcat=' + cat) : ('category.html?cat=' + cat);
const prod = id => PH.products.find(p => p.id === id);
const soft = id => PH.software.find(s => s.id === id);
const catName = id => (PH.cats.find(c => c.id === id) || {}).name || id;
const off = p => p.old ? Math.round((1 - p.price / p.old) * 100) : 0;
/* ---------- store ---------- */
const store = {
  get(k, d) { try { return JSON.parse(localStorage.getItem('ph_' + k)) ?? d; } catch { return d; } },
  set(k, v) { localStorage.setItem('ph_' + k, JSON.stringify(v)); }
};
const cartGet = () => store.get('cart', []);
const cartCount = () => cartGet().reduce((a, i) => a + i.q, 0);
const cartDetailed = () => cartGet().map(i => ({ ...prod(i.id), q: i.q })).filter(i => i.id);
const cartTotal = (l) => (l || cartDetailed()).reduce((a, i) => a + i.price * i.q, 0);
function addToCart(id, qty = 1, silent) {
  const c = cartGet(); const f = c.find(i => i.id === id);
  if (f) f.q += qty; else c.push({ id, q: qty });
  store.set('cart', c); updateBadges();     renderMiniCart(); if (window.PH_WP) rewriteLinks(document);
  if (!silent) { showToast(`«${prod(id).name}» به سبد خرید اضافه شد`, 'success'); openDrawer('drawerCart'); }
}
function setQty(id, qty) { let c = cartGet(); if (qty <= 0) c = c.filter(i => i.id !== id); else c.find(i => i.id === id).q = qty; store.set('cart', c); updateBadges(); renderMiniCart(); if (document.body.dataset.page === 'cart') initCart(); }
function toggleCmp(id, type) {
  let c = store.get('cmp', []);
  const key = (type || 'p') + ':' + id;
  c = c.includes(key) ? c.filter(x => x !== key) : [...c, key].slice(-3);
  store.set('cmp', c); updateBadges();
  showToast(c.includes(key) ? 'به لیست مقایسه اضافه شد' : 'از لیست مقایسه حذف شد', 'info');
  $$('[data-cmp]').forEach(b => b.classList.toggle('active', store.get('cmp', []).includes(b.dataset.cmp)));
}
function toggleWish(id) {
  let w = store.get('wish', []);
  w = w.includes(id) ? w.filter(x => x !== id) : [...w, id];
  store.set('wish', w);
  showToast(w.includes(id) ? 'به علاقه‌مندی‌ها اضافه شد' : 'از علاقه‌مندی‌ها حذف شد', 'info');
  $$('[data-wish]').forEach(b => b.classList.toggle('active', store.get('wish', []).includes(b.dataset.wish)));
}
/* ---------- WordPress AJAX helper (unused on static site) ---------- */
function phAjax(action, data) {
  const fd = new FormData();
  fd.append('action', action); fd.append('nonce', (window.PH_WP || {}).nonce || '');
  for (const k in (data || {})) fd.append(k, data[k]);
  return fetch(PH_WP.ajax, { method: 'POST', body: fd }).then(r => r.json());
}
/* ---------- toast / badges ---------- */
function showToast(msg, type = 'success') {
  const t = document.createElement('div');
  t.className = 'toast ' + type;
  t.innerHTML = `${Icons[type === 'error' ? 'x' : type === 'info' ? 'message' : 'checkCircle']}<span>${msg}</span>`;
  $('#toasts').appendChild(t);
  setTimeout(() => { t.classList.add('out'); setTimeout(() => t.remove(), 350); }, 3200);
}
function updateBadges() {
  const cc = $('#cartCount'), mc = $('#cmpCount');
  if (cc) { const n = cartCount(); cc.style.display = n ? 'flex' : 'none'; cc.textContent = faNum(n); }
  if (mc) { const n = store.get('cmp', []).length; mc.style.display = n ? 'flex' : 'none'; mc.textContent = faNum(n); }
}
/* ---------- drawers & modals ---------- */
function openDrawer(id) { closeAll(); $('#' + id).classList.add('show'); $('#overlay').classList.add('show'); document.body.classList.add('no-scroll'); }
function openModal(id) { closeAll(); $('#' + id).classList.add('show'); $('#overlay').classList.add('show'); document.body.classList.add('no-scroll'); }
function closeAll() { $$('.drawer.show,.modal.show').forEach(d => d.classList.remove('show')); $('#overlay').classList.remove('show'); $('#searchOverlay').classList.remove('show'); document.body.classList.remove('no-scroll'); }
function openConsult(subject) {
  if (subject) { const s = $('#consultSubject'); [...s.options].forEach(o => { if (subject.includes(o.text.slice(0, 4))) s.value = o.text; }); }
  openModal('modalConsult');
}
function openQuick(id) {
  const p = prod(id); if (!p) return;
  $('#quickBody').innerHTML = `<div class="qv-grid">
    <img src="${p.img}" alt="${p.name}">
    <div><span class="badge badge-gray">${catName(p.cat)}</span>
      <h3>${p.name}</h3>
      <div class="p-rating">${stars(p.rating)}<span class="num">${faNum(p.rating)} (${faNum(p.reviews)} دیدگاه)</span></div>
      <p style="font-size:13.5px;color:var(--muted);margin-bottom:14px">${p.short}</p>
      <div style="font-size:22px;font-weight:900;color:var(--ink);margin-bottom:6px">${fmtPrice(p.price)}</div>
      <div style="margin-bottom:18px"><span class="stock ${p.stock}">${p.stock === 'in' ? 'موجود در انبار' : p.stock === 'low' ? 'موجودی محدود' : 'ناموجود'}</span></div>
      <div style="display:flex;gap:10px"><button class="btn btn-primary" style="flex:1" data-add="${p.id}"><span data-icon="cart"></span>افزودن به سبد</button><a class="btn btn-outline" href="${linkProduct(p.id)}">مشاهده محصول</a></div>
    </div></div>`;
  hydrateIcons($('#quickBody')); openModal('modalQuick');
}
/* ---------- renderers ---------- */
function stars(r) { let h = '<span class="stars">'; for (let i = 1; i <= 5; i++) h += Icons.star.replace('<svg', `<svg class="${i <= Math.round(r) ? 'on' : ''}"`); return h + '</span>'; }
function productCard(p) {
  const inC = store.get('cmp', []).includes('p:' + p.id), inW = store.get('wish', []).includes(p.id);
  return `<article class="p-card reveal in">
    <div class="p-media"><a href="${linkProduct(p.id)}" aria-label="${p.name}"><img src="${p.img}" alt="${p.name}" loading="lazy"></a>
      <div class="p-badges">${p.badge ? `<span class="badge ${p.badge === 'پرفروش‌ترین' ? 'badge-accent' : p.badge === 'جدید' ? 'badge-success' : 'badge-brand'}">${p.badge}</span>` : ''}${off(p) ? `<span class="p-off num">٪${faNum(off(p))}</span>` : ''}</div>
      <div class="p-actions">
        <button data-quick="${p.id}" aria-label="مشاهده سریع">${Icons.eye}</button>
        <button data-cmp="p:${p.id}" class="${inC ? 'active' : ''}" aria-label="مقایسه">${Icons.compare}</button>
        <button data-wish="${p.id}" class="${inW ? 'active' : ''}" aria-label="علاقه‌مندی">${Icons.heart}</button>
      </div></div>
    <div class="p-body"><span class="p-brand">${p.brand}</span>
      <a href="${linkProduct(p.id)}"><h3 class="p-name">${p.name}</h3></a>
      <p class="p-desc">${p.short}</p>
      <div class="p-rating">${stars(p.rating)}<span class="num">${faNum(p.reviews)} دیدگاه</span></div>
      <div class="p-price"><div>${p.old ? `<span class="old num">${faNum(p.old)}</span>` : ''}<span class="now">${fmtPrice(p.price)}</span></div><span class="stock ${p.stock}">${p.stock === 'in' ? 'موجود' : p.stock === 'low' ? 'محدود' : 'ناموجود'}</span></div>
      <div class="p-foot"><button class="btn btn-primary" data-add="${p.id}" ${p.stock === 'out' ? 'disabled' : ''}><span data-icon="cart"></span>افزودن به سبد</button><a class="icon-btn" href="${linkProduct(p.id)}" aria-label="مشاهده محصول"><span data-icon="eye"></span></a></div>
    </div></article>`;
}
function softCard(s, featured) {
  return `<article class="soft-card ${featured ? 'featured' : ''} reveal in">
    ${s.badge ? `<span class="soft-badge">${s.badge}</span>` : ''}
    <div class="soft-mono" style="background:${s.color}1A;color:${s.color};border:1.5px solid ${s.color}45">${s.mono}</div>
    <h3>${s.name}</h3><div class="soft-tag">${s.tag}</div><p>${s.short}</p>
    <ul class="soft-feats">${s.features.slice(0, 3).map(f => `<li>${Icons.check}<span>${f[0].trim()}</span></li>`).join('')}</ul>
    <div class="soft-for">مناسب: ${s.forWho.slice(0, 2).join('، ')}</div>
    <div class="soft-price"><div><b>${s.price ? `<span class="num">${faNum(s.price)}</span> <small style="font-size:11px">تومان</small>` : 'تماس بگیرید'}</b></div>${stars(s.rating)}</div>
    <div class="soft-btns"><a class="btn ${featured ? 'btn-primary' : 'btn-white'}" href="${linkSoftware(s.id)}">مشاهده جزئیات</a><button class="btn ${featured ? 'btn-outline' : 'btn-outline-white'}" data-consult data-subject="نرم‌افزار ${s.name}">مشاوره</button></div>
  </article>`;
}
function postCard(p) {
  return `<article class="post-card reveal in"><a class="post-thumb" href="${linkArticle(p.id)}"><img src="${p.img}" alt="${p.title}" loading="lazy"><span class="post-cat">${p.cat}</span></a>
    <div class="post-body"><a href="${linkArticle(p.id)}"><h3>${p.title}</h3></a><p>${p.excerpt}</p>
    <div class="post-meta"><span><span data-icon="cal"></span>${p.date}</span><span><span data-icon="clock"></span>${p.read}</span><a class="link-more" style="margin-inline-start:auto" href="${linkArticle(p.id)}">مطالعه <span data-icon="arrowLeft"></span></a></div></div></article>`;
}
function renderMiniCart() {
  const items = cartDetailed(), body = $('#miniCartBody'), foot = $('#miniCartFoot');
  if (!body) return;
  if (!items.length) { body.innerHTML = `<div class="empty">${Icons.cart}<h3>سبد خرید خالی است</h3><p>هنوز محصولی اضافه نکرده‌اید.</p><a class="btn btn-primary btn-sm" style="margin-top:14px" href="${linkPage('shop')}">مشاهده فروشگاه</a></div>`; foot.innerHTML = ''; return; }
  body.innerHTML = items.map(i => `<div class="mini-item"><img src="${i.img}" alt="${i.name}"><div><b>${i.name}</b><span class="num">${faNum(i.q)} × ${faNum(i.price)} تومان</span></div><button class="rm" data-rm="${i.id}" aria-label="حذف">${Icons.trash}</button></div>`).join('');
  foot.innerHTML = `<div class="mini-total"><span>جمع سبد:</span><span class="num">${faNum(cartTotal(items))} تومان</span></div>
  <div style="display:flex;gap:10px"><a class="btn btn-outline" style="flex:1" href="${linkPage('cart')}">مشاهده سبد</a><a class="btn btn-primary" style="flex:1" href="${linkPage('checkout')}">تسویه حساب</a></div>`;
}
/* ---------- global bindings ---------- */
function bindGlobal() {
  document.addEventListener('click', e => {
    const t = sel => e.target.closest(sel);
    let m;
    if (t('[data-close]') || e.target.id === 'overlay') closeAll();
    else if (m = t('[data-add]')) { e.preventDefault(); addToCart(m.dataset.add, 1); }
    else if (m = t('[data-rm]')) setQty(m.dataset.rm, 0);
    else if (m = t('[data-quick]')) openQuick(m.dataset.quick);
    else if (m = t('[data-cmp]')) toggleCmp(m.dataset.cmp.split(':')[1], m.dataset.cmp.split(':')[0]);
    else if (m = t('[data-wish]')) toggleWish(m.dataset.wish);
    else if (m = t('[data-consult]')) openConsult(m.dataset.subject || '');
    else if (t('#btnCart')) { renderMiniCart(); openDrawer('drawerCart'); }
    else if (t('#btnMenu')) openDrawer('drawerNav');
    else if (t('#btnSearch')) { closeAll(); $('#searchOverlay').classList.add('show'); document.body.classList.add('no-scroll'); setTimeout(() => $('#searchInput').focus(), 100); doSearch(''); }
    else if (m = t('.mnav-btn')) { const g = m.parentElement, sub = g.querySelector('.mnav-sub'), open = g.classList.toggle('open'); sub.style.maxHeight = open ? sub.scrollHeight + 'px' : 0; }
    else if (m = t('.acc-btn')) { const it = m.parentElement, bd = it.querySelector('.acc-body'), open = it.classList.toggle('open'); bd.style.maxHeight = open ? bd.scrollHeight + 'px' : 0; }
    else if (m = t('.tab-btn')) { const wrap = m.closest('[data-tabs]'); $$('.tab-btn', wrap).forEach(b => b.classList.remove('active')); m.classList.add('active'); $$('.tab-pane', wrap).forEach(p => p.classList.toggle('active', p.dataset.pane === m.dataset.tab)); }
    else if (m = t('[data-q]')) { $('#searchInput').value = m.dataset.q; doSearch(m.dataset.q); }
    else if (t('#toTop')) window.scrollTo({ top: 0, behavior: 'smooth' });
  });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAll(); });
  window.addEventListener('scroll', () => {
    $('#mainHeader')?.classList.toggle('scrolled', scrollY > 10);
    $('#toTop')?.classList.toggle('show', scrollY > 600);
    const sb = $('#stickyBuy'); if (sb) sb.classList.toggle('show', scrollY > 700);
  }, { passive: true });
  /* search */
  let deb; $('#searchInput')?.addEventListener('input', e => { clearTimeout(deb); deb = setTimeout(() => doSearch(e.target.value), 180); });
  $('#searchForm')?.addEventListener('submit', e => { e.preventDefault(); const base = (window.PH_WP && PH_WP.urls && PH_WP.urls.shop) ? PH_WP.urls.shop : 'shop.html'; location.href = base + '?q=' + encodeURIComponent($('#searchInput').value.trim()); });
  /* consult */
  $('#consultForm')?.addEventListener('submit', e => {
    e.preventDefault();
    const f = e.target; let ok = true;
    const name = f.name, phone = f.phone;
    name.closest('.field').classList.toggle('invalid', !(ok = name.value.trim().length >= 3) ? true : false);
    const phOk = /^0?9\d{9}$/.test(phone.value.trim().replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d)));
    phone.closest('.field').classList.toggle('invalid', !phOk);
    if (!ok || !phOk) return;
    if (window.PH_WP) {
      const btn = f.querySelector('button[type=submit]'); btn.disabled = true;
      phAjax('ph_lead', { name: name.value.trim(), phone: phone.value.trim(), subject: f.subject.value, msg: f.msg.value }).then(res => { btn.disabled = false; if (res && res.success) { closeAll(); showToast('درخواست شما ثبت شد؛ کارشناسان ما به‌زودی تماس می‌گیرند', 'success'); f.reset(); } else showToast('خطا در ثبت درخواست', 'error'); }).catch(() => { btn.disabled = false; showToast('خطا در ثبت درخواست', 'error'); });
      return;
    }
    closeAll(); showToast('درخواست شما ثبت شد؛ کارشناسان ما به‌زودی تماس می‌گیرند', 'success'); f.reset();
  });
  /* newsletter */
  document.addEventListener('submit', e => {
    if (e.target.id !== 'nlForm') return;
    e.preventDefault();
    const em = e.target.querySelector('input[type=email]').value.trim();
    if (window.PH_WP) phAjax('ph_subscribe', { email: em }).then(res => showToast(res && res.success ? 'عضویت شما در خبرنامه ثبت شد' : ((res && res.data) || 'خطا در ثبت'), res && res.success ? 'success' : 'error')).catch(() => showToast('خطا در ثبت', 'error'));
    else showToast('عضویت شما در خبرنامه ثبت شد', 'success');
    e.target.reset();
  });
  /* reveal */
  const io = new IntersectionObserver(es => es.forEach(x => x.isIntersecting && x.target.classList.add('in')), { threshold: .08 });
  $$('.reveal').forEach(el => io.observe(el));
}
function doSearch(term) {
  term = (term || '').trim();
  const box = $('#searchResults'); if (!box) return;
  if (window.PH_WP && PH_WP.rest) {
    box.innerHTML = '<p style="color:var(--muted);font-size:13px">در حال جستجو...</p>';
    fetch(PH_WP.rest + 'ph/v1/search?q=' + encodeURIComponent(term)).then(r => r.json()).then(res => {
      const arr = (res && res.results) || [];
      box.innerHTML = arr.length ? arr.map(r => `<a class="search-result" href="${r.url}">${r.img ? `<img src="${r.img}" alt="">` : '<span style="width:56px;height:56px;border-radius:8px;background:var(--brand-tint);display:flex;align-items:center;justify-content:center;flex-shrink:0"><i data-icon="package"></i></span>'}<div><b>${r.t}</b><span>${r.s}</span></div></a>`).join('') : `<div class="empty" style="grid-column:1/-1;padding:30px"><i data-icon="search"></i><p>نتیجه‌ای یافت نشد.</p></div>`;
      hydrateIcons(box);
    }).catch(() => { box.innerHTML = ''; });
    return;
  }
  const all = [...PH.products.map(p => ({ t: p.name, s: catName(p.cat), img: p.img, h: linkProduct(p.id) })),
    ...PH.software.map(s => ({ t: 'نرم‌افزار ' + s.name, s: s.tag, img: null, mono: s.mono, color: s.color, h: linkSoftware(s.id) })),
    ...PH.posts.map(p => ({ t: p.title, s: 'مجله پژواک', img: p.img, h: linkArticle(p.id) }))];
  const res = term ? all.filter(x => x.t.includes(term) || x.s.includes(term)).slice(0, 8) : all.slice(0, 4);
  box.innerHTML = res.length ? res.map(r => `<a class="search-result" href="${r.h}">${r.img ? `<img src="${r.img}" alt="">` : `<span style="width:56px;height:56px;border-radius:8px;background:${r.color}1A;color:${r.color};display:flex;align-items:center;justify-content:center;font-weight:900;font-size:22px;flex-shrink:0">${r.mono}</span>`}<div><b>${r.t}</b><span>${r.s}</span></div></a>`).join('')
    : `<div class="empty" style="grid-column:1/-1;padding:30px">${Icons.search}<p>نتیجه‌ای برای «${term}» یافت نشد.</p></div>`;
}
/* ================= PAGE INITS ================= */
function initHome() {
  $('#softGrid').innerHTML = PH.software.map((s, i) => softCard(s, i === 0)).join('');
  $('#hwRail').innerHTML = PH.products.slice(0, 4).map(productCard).join('');
  $('#postRail').innerHTML = PH.posts.slice(0, 3).map(postCard).join('');
  hydrateIcons(document); updateBadges();
  /* wizard */
  const wiz = { step: 0, biz: null, users: null, volume: null, warehouse: null };
  const rec = () => {
    const b = wiz.biz;
    const dyn = window.PH && PH.solutions && PH.solutions[b] && PH.solutions[b].soft;
    const sid = dyn || (b === 'restaurant' ? 'pos-suite' : (b === 'clothing' || b === 'chain' ? 'zafaran' : (b === 'wholesale' || b === 'service' ? 'pejvak' : 'baran')));
    return soft(sid) || PH.software[0];
  };
  const render = () => {
    $$('.wpane').forEach(p => p.classList.toggle('active', +p.dataset.wpane === wiz.step));
    $$('.wstep').forEach((s, i) => { s.classList.toggle('active', i === wiz.step); s.classList.toggle('done', i < wiz.step); if (i < wiz.step) s.querySelector('.n').innerHTML = Icons.check; else s.querySelector('.n').textContent = ['۱', '۲', '۳', '۴'][i]; });
    $('#wBack').style.visibility = wiz.step === 0 || wiz.step === 4 ? 'hidden' : 'visible';
    $('#wNext').style.display = wiz.step === 4 ? 'none' : '';
    $('#wNext').innerHTML = wiz.step === 3 ? 'مشاهده پیشنهاد من' : 'ادامه <span data-icon="arrowLeft"></span>';
    hydrateIcons($('#wNext'));
    if (wiz.step === 4) { const s = rec(); if (s) $('#wRecBox').innerHTML = `<div class="soft-mono" style="background:${s.color}1A;color:${s.color};border:1.5px solid ${s.color}45">${s.mono}</div><div><b>نرم‌افزار ${s.name}</b><span>${s.tag}</span></div><a class="btn btn-primary btn-sm" style="margin-inline-start:auto" href="${linkSoftware(s.id)}">مشاهده نرم‌افزار</a>`; }
  };
  document.addEventListener('click', e => {
    const p = e.target.closest('[data-pick]'); if (!p || !p.closest('#wizard')) return;
    const grp = p.dataset.pick;
    $$(`[data-pick="${grp}"]`).forEach(x => x.classList.remove('selected')); p.classList.add('selected');
    wiz[grp] = p.dataset.val;
  });
  $('#wNext').addEventListener('click', () => {
    const need = [['biz', 'نوع کسب‌وکار'], ['users', 'تعداد کاربران'], ['volume', 'حجم فروش'], ['warehouse', 'نیاز انبارداری']][wiz.step];
    if (need && !wiz[need[0]]) { showToast(`لطفاً ${need[1]} را انتخاب کنید`, 'error'); return; }
    if (wiz.step < 4) { wiz.step++; render(); }
  });
  $('#wBack').addEventListener('click', () => { if (wiz.step > 0) { wiz.step--; render(); } });
  $('#wReset')?.addEventListener('click', () => { wiz.step = 0; wiz.biz = wiz.users = wiz.volume = wiz.warehouse = null; $$('#wizard .selected').forEach(x => x.classList.remove('selected')); render(); });
  render();
  /* compare teaser */
  const opts = sel => { const el = $(sel); el.innerHTML = '<option value="">انتخاب محصول...</option>' + PH.products.map(p => `<option value="${p.id}">${p.name}</option>`).join(''); };
  ['#cmp1', '#cmp2', '#cmp3'].forEach(opts);
  $('#cmpGo').addEventListener('click', () => {
    const ids = [$('#cmp1').value, $('#cmp2').value, $('#cmp3').value].filter(Boolean);
    if (ids.length < 2) { showToast('حداقل دو محصول برای مقایسه انتخاب کنید', 'error'); return; }
    store.set('cmp', ids.map(id => 'p:' + id)); location.href = (window.PH_WP && PH_WP.links && PH_WP.links.compare) || 'compare.html';
  });
  /* counters */
  const cio = new IntersectionObserver(es => es.forEach(x => {
    if (!x.isIntersecting) return; cio.unobserve(x.target);
    const el = x.target, end = +el.dataset.count; let t = 0;
    const tick = () => { t += Math.max(1, Math.round(end / 60)); if (t >= end) t = end; el.textContent = faNum(t) + (el.dataset.suffix || ''); if (t < end) requestAnimationFrame(tick); };
    tick();
  }), { threshold: .4 });
  $$('[data-count]').forEach(el => cio.observe(el));
}
/* ---------- shop / category ---------- */
function shopState() { return { cat: q('pcat') || (window.PH_PRECAT || ''), group: q('group') || '', q: q('q') || '', sort: 'new', onlyOff: false, onlyStock: false, brands: [] }; }
function initShop() {
  const st = shopState();
  const grid = $('#shopGrid'), isList = () => $('#shopGrid').classList.contains('p-list');
  if (st.group === 'software') { /* show software cards instead */ }
  const apply = () => {
    let list = [...PH.products];
    if (st.cat) list = list.filter(p => p.cat === st.cat);
    if (st.q) list = list.filter(p => (p.name + p.brand + p.short).includes(st.q));
    if (st.onlyOff) list = list.filter(p => p.old);
    if (st.onlyStock) list = list.filter(p => p.stock !== 'out');
    if (st.brands.length) list = list.filter(p => st.brands.includes(p.brand));
    if (st.sort === 'cheap') list.sort((a, b) => a.price - b.price);
    else if (st.sort === 'exp') list.sort((a, b) => b.price - a.price);
    else if (st.sort === 'pop') list.sort((a, b) => b.reviews - a.reviews);
    $('#shopCount').textContent = `نمایش ${faNum(list.length)} محصول`;
    grid.innerHTML = list.length ? list.map(productCard).join('') : `<div class="empty" style="grid-column:1/-1">${Icons.package}<h3>محصولی یافت نشد</h3><p>فیلترها را تغییر دهید یا عبارت دیگری جستجو کنید.</p></div>`;
    hydrateIcons(grid);
  };
  /* brand filters */
  const brands = [...new Set(PH.products.map(p => p.brand))];
  $('#brandFilters').innerHTML = brands.map(b => `<label class="check"><input type="checkbox" value="${b}">${b}<span class="f-count num">${faNum(PH.products.filter(p => p.brand === b).length)}</span></label>`).join('');
  $$('#brandFilters input').forEach(c => c.addEventListener('change', () => { st.brands = $$('#brandFilters input:checked').map(x => x.value); apply(); }));
  $('#sortSel').addEventListener('change', e => { st.sort = e.target.value; apply(); });
  $('#offOnly').addEventListener('change', e => { st.onlyOff = e.target.checked; apply(); });
  $('#stockOnly').addEventListener('change', e => { st.onlyStock = e.target.checked; apply(); });
  $('#clearFilters').addEventListener('click', () => { st.onlyOff = st.onlyStock = false; st.brands = []; st.q = ''; $$('#brandFilters input').forEach(i => i.checked = false); $('#offOnly').checked = $('#stockOnly').checked = false; const si = $('#shopSearch'); if (si) si.value = ''; apply(); });
  $('#viewGrid')?.addEventListener('click', () => { grid.classList.remove('p-list'); $('#viewGrid').classList.add('active'); $('#viewList').classList.remove('active'); });
  $('#viewList')?.addEventListener('click', () => { grid.classList.add('p-list'); $('#viewList').classList.add('active'); $('#viewGrid').classList.remove('active'); });
  $('#filtersFab')?.addEventListener('click', () => { $('#filters').classList.add('show'); $('#overlay').classList.add('show'); });
  $('#closeFilters')?.addEventListener('click', closeAll);
  const si = $('#shopSearch'); if (si && st.q) si.value = st.q;
  si?.addEventListener('input', e => { st.q = e.target.value.trim(); apply(); });
  if (st.cat) { const c = PH.cats.find(x => x.id === st.cat); if (c) { $('#shopTitle').textContent = c.name; $('#shopDesc').textContent = c.desc; } $$('.shop-cat-chips a').forEach(a => a.classList.toggle('active', a.dataset.cat === st.cat)); }
  if (st.q && !st.cat) $('#shopTitle').textContent = `نتایج جستجو برای «${st.q}»`;
  apply();
}
/* ---------- product detail ---------- */
function initProduct() {
  const p = prod(q('id')) || prod(window.PH_SINGLE_ID || '') || PH.products[0];
  document.title = `${p.name} | پژواک حساب`;
  $('#pdWrap').innerHTML = `
  <div class="pd-layout">
    <div class="gallery"><div class="g-main"><img id="gMain" src="${p.img}" alt="${p.name}">${p.badge ? `<span class="badge badge-accent">${p.badge}</span>` : ''}</div>
      <div class="g-thumbs">${p.gallery.map((g, i) => `<button class="${i === 0 ? 'active' : ''}" data-g="${g}"><img src="${g}" alt=""></button>`).join('')}</div></div>
    <div class="pd-info"><span class="p-brand">${p.brand} · ${catName(p.cat)}</span>
      <h1 class="pd-title">${p.name}</h1>
      <div class="pd-meta"><span style="display:inline-flex;align-items:center;gap:8px">${stars(p.rating)}<b class="num" style="color:var(--ink)">${faNum(p.rating)}</b> <span class="num">(${faNum(p.reviews)} دیدگاه)</span></span><span class="code">کد: ${p.code}</span><span class="stock ${p.stock}">${p.stock === 'in' ? 'موجود در انبار' : p.stock === 'low' ? 'موجودی محدود — سریع‌تر سفارش دهید' : 'ناموجود'}</span></div>
      <p class="pd-short">${p.short}</p>
      <ul class="pd-feats">${p.features.map(f => `<li>${Icons.checkCircle}${f}</li>`).join('')}</ul>
      <div class="pd-buy">
        <div class="pd-price-row"><div class="pd-price">${p.old ? `<div class="old num">${faNum(p.old)} تومان</div>` : ''}<div class="now">${fmtPrice(p.price)}</div></div>${off(p) ? `<span class="p-off num">٪${faNum(off(p))} تخفیف</span>` : ''}</div>
        <div class="pd-actions"><div class="qty"><button id="qPlus">+</button><input id="qVal" value="۱" readonly><button id="qMinus">−</button></div>
        <button class="btn btn-primary btn-lg" id="pdAdd" ${p.stock === 'out' ? 'disabled' : ''}><span data-icon="cart"></span>افزودن به سبد خرید</button></div>
        <div class="pd-secondary"><button class="btn btn-outline" data-consult data-subject="${p.name}"><span data-icon="headset"></span>درخواست مشاوره</button><button class="btn btn-ghost" data-cmp="p:${p.id}"><span data-icon="compare"></span>مقایسه</button><button class="btn btn-ghost" data-wish="${p.id}"><span data-icon="heart"></span>علاقه‌مندی</button></div>
        <div class="pd-trust"><div><span data-icon="shield"></span>ضمانت اصالت</div><div><span data-icon="truck"></span>ارسال سریع</div><div><span data-icon="wrench"></span>نصب و راه‌اندازی</div><div><span data-icon="headset"></span>پشتیبانی فنی</div></div>
      </div></div>
  </div>
  <div class="pd-tabs" data-tabs><div class="tabs">
    <button class="tab-btn active" data-tab="t1">توضیحات</button><button class="tab-btn" data-tab="t2">مشخصات فنی</button><button class="tab-btn" data-tab="t3">دیدگاه‌ها <span class="num">(${faNum(p.reviews)})</span></button><button class="tab-btn" data-tab="t4">سوالات متداول</button>
  </div>
  <div class="tab-pane active" data-pane="t1"><div class="prose"><h3>معرفی ${p.name}</h3><p>${p.short} این محصول با ضمانت اصالت کالا و خدمات پس از فروش پژواک حساب عرضه می‌شود و کارشناسان ما پیش از خرید، سازگاری آن با نرم‌افزار و سایر تجهیزات شما را بررسی می‌کنند.</p><ul>${p.features.map(f => `<li>${f}</li>`).join('')}</ul><h3>نصب و راه‌اندازی</h3><p>راهنمای نصب ریموت برای این محصول رایگان است. در صورت نیاز به نصب حضوری در تهران و شهرستان‌ها، با واحد خدمات هماهنگ می‌شود.</p></div></div>
  <div class="tab-pane" data-pane="t2"><table class="spec-table">${p.specs.map(s => `<tr><td>${s[0]}</td><td>${s[1]}</td></tr>`).join('')}</table></div>
  <div class="tab-pane" data-pane="t3"><div id="pdReviews"></div>
    <div class="co-box" style="margin-top:20px"><h3>ثبت دیدگاه</h3><form id="revForm"><div class="form-row"><div class="field"><label>نام</label><input class="input" required></div><div class="field"><label>امتیاز</label><select class="input"><option>۵ — عالی</option><option>۴ — خوب</option><option>۳ — متوسط</option><option>۲ — ضعیف</option><option>۱ — بد</option></select></div></div><div class="field"><label>دیدگاه شما</label><textarea class="input" required></textarea></div><button class="btn btn-primary">ثبت دیدگاه</button></form></div></div>
  <div class="tab-pane" data-pane="t4"><div class="accordion">${PH.faqs.slice(7, 11).map(f => `<div class="acc-item"><button class="acc-btn">${f.q}${Icons.chevDown}</button><div class="acc-body"><div>${f.a}</div></div></div>`).join('')}</div></div>
  </div>`;
  $('#pdCrumb').textContent = p.name;
  const revList = [['فروشگاه نمونه تهران', 'سوپرمارکت', 'کیفیت عالی و ارسال سریع. نصب هم با راهنمایی تلفنی انجام شد.', 5], ['کافه مرکزی', 'کافی‌شاپ', 'با نرم‌افزار باران بدون هیچ مشکلی کار کرد. پشتیبانی خیلی خوب پاسخ می‌دهد.', 5], ['بوتیک آریا', 'پوشاک', 'بسته‌بندی عالی بود و قیمت نسبت به بازار منصفانه‌تر است.', 4]];
  const paintReviews = list => { $('#pdReviews').innerHTML = list.length ? list.map(r => `<div class="review"><div class="review-head"><span class="review-ava">${(r[0] || 'ک')[0]}</span><div><b>${r[0]}</b><span>${r[1]}</span></div>${stars(r[3])}</div><p>${r[2]}</p></div>`).join('') : '<div class="empty" style="padding:26px"><p>هنوز دیدگاهی ثبت نشده است. اولین نظر را شما بنویسید.</p></div>'; hydrateIcons($('#pdReviews')); };
  if (window.PH_WP) { $('#pdReviews').innerHTML = '<p style="color:var(--muted);font-size:13.5px">در حال بارگذاری دیدگاه‌ها...</p>'; phAjax('ph_get_reviews', { pid: PH_WP.pid || 0 }).then(res => { const d = res && res.success ? res.data : null; paintReviews(d && d.list ? d.list : []); const n = document.querySelector('[data-tab=t3] .num'); if (n && d) n.textContent = '(' + faNum(d.count) + ')'; }).catch(() => paintReviews([])); }
  else paintReviews(revList);
  $('#revForm').addEventListener('submit', e => {
    e.preventDefault();
    const f = e.target;
    if (window.PH_WP) {
      const nm = f.querySelector('input').value.trim(), tx = f.querySelector('textarea').value.trim(), rt = 5 - f.querySelector('select').selectedIndex;
      if (nm.length < 2 || tx.length < 3) { showToast('نام و متن دیدگاه را کامل وارد کنید', 'error'); return; }
      phAjax('ph_submit_review', { pid: PH_WP.pid || 0, name: nm, text: tx, rating: rt }).then(res => { showToast(res && res.success ? 'دیدگاه شما ثبت شد و پس از تأیید نمایش داده می‌شود' : 'خطا در ثبت دیدگاه', res && res.success ? 'success' : 'error'); if (res && res.success) f.reset(); }).catch(() => showToast('خطا در ثبت دیدگاه', 'error'));
    } else { showToast('دیدگاه شما ثبت شد و پس از تأیید نمایش داده می‌شود', 'success'); f.reset(); }
  });
  let qq = 1;
  const faD = n => ['۰', '۱', '۲', '۳', '۴', '۵'][n] || faNum(n);
  $('#qPlus').addEventListener('click', () => { qq = Math.min(5, qq + 1); $('#qVal').value = faD(qq); });
  $('#qMinus').addEventListener('click', () => { qq = Math.max(1, qq - 1); $('#qVal').value = faD(qq); });
  $('#pdAdd').addEventListener('click', () => addToCart(p.id, qq));
  $$('[data-g]').forEach(b => b.addEventListener('click', () => { $$('[data-g]').forEach(x => x.classList.remove('active')); b.classList.add('active'); $('#gMain').src = b.dataset.g; }));
  $('#relGrid').innerHTML = PH.products.filter(x => x.cat === p.cat && x.id !== p.id).concat(PH.products.filter(x => x.cat !== p.cat)).slice(0, 4).map(productCard).join('');
  $('#stickyBuy').innerHTML = `<img src="${p.img}" alt=""><div class="inf"><b>${p.name}</b><span class="num">${fmtShort(p.price)} تومان</span></div><button class="btn btn-primary" id="stAdd">افزودن به سبد</button>`;
  $('#stAdd').addEventListener('click', () => addToCart(p.id, 1));
  hydrateIcons(document);
}
/* ---------- software detail ---------- */
function initSoftware() {
  const s = soft(q('id')) || soft(window.PH_SINGLE_ID || '') || PH.software[0];
  document.title = `نرم‌افزار ${s.name} | پژواک حساب`;
  const phDash = (window.PH_WP && PH_WP.swDash && PH_WP.swDash.length ? PH_WP.swDash : [['248M', 'فروش امروز'], ['1324', 'فاکتور امروز'], ['٪18', 'رشد ماهانه']]);
  const phFaD = v => String(v).replace(/[0-9]/g, d => '۰۱۲۳۴۵۶۷۸۹'[d]);
  $('#swHero').innerHTML = `<div class="container sw-hero-grid">
    <div><div class="breadcrumb-lite"><a href="${linkHome()}">خانه</a><span>/</span><a href="${linkPage('shop', '?group=software')}">نرم‌افزارها</a><span>/</span><span>${s.name}</span></div>
      <div style="display:flex;gap:10px;align-items:center;margin-bottom:14px"><div class="soft-mono" style="background:rgba(255,255,255,.1);color:#fff;border:1.5px solid rgba(255,255,255,.25);margin:0;font-size:26px;width:62px;height:62px">${s.mono}</div>${s.badge ? `<span class="badge badge-accent">${s.badge}</span>` : ''}</div>
      <h1>نرم‌افزار ${s.name}</h1><div class="tag">${s.tag}</div>
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">${stars(s.rating)}<span class="num" style="color:#BCD2D9;font-size:13px">${faNum(s.rating)} · ${faNum(s.reviews)} دیدگاه</span></div>
      <p class="desc">${s.desc}</p>
      <div class="sw-hero-cta"><button class="btn btn-accent btn-lg" data-consult data-subject="دموی نرم‌افزار ${s.name}"><span data-icon="eye"></span>درخواست دمو</button><button class="btn btn-outline-white btn-lg" data-consult data-subject="نرم‌افزار ${s.name}"><span data-icon="headset"></span>مشاوره خرید</button></div></div>
    <div class="sw-dash"><div class="sw-dash-bar"><i></i><i></i><i></i><span style="font-size:12px;color:var(--muted);margin-inline-start:8px">داشبورد مدیریتی ${s.name}</span></div>
      <div class="sw-dash-body">${phDash.map(d => `<div class="sw-stat"><b class="num">${phFaD(d[0])}</b><span>${d[1]}</span></div>`).join('')}
      <div class="sw-chart"><b style="font-size:13px">نمودار فروش هفتگی</b><div class="sw-bars">${[45, 70, 55, 90, 65, 100, 80].map((h, i) => `<i style="height:${h}%;animation-delay:${i * .08}s"></i>`).join('')}</div></div></div></div></div>`;
  $('#swFeats').innerHTML = s.features.map(f => `<div class="feat-card reveal in"><span class="ic"><span data-icon="checkCircle"></span></span><div><h3>${f[0].trim()}</h3><p>${f[1]}</p></div></div>`).join('');
  $('#swFor').innerHTML = s.forWho.map(w => `<span class="badge badge-gray" style="font-size:13px;padding:8px 18px">${w}</span>`).join('');
  $('#swModules').innerHTML = s.modules.map(m => `<div class="feat-card reveal in"><span class="ic"><span data-icon="layers"></span></span><div><h3>${m}</h3><p>ماژول یکپارچه با سایر بخش‌های ${s.name}</p></div></div>`).join('');
  $('#swPlans').innerHTML = s.plans.map((pl, i) => `<div class="plan ${i === 1 ? 'hot' : ''}"><h3>${pl.n}</h3><span class="p-desc">${pl.d}</span><span class="p-price">${pl.p ? `<span class="num">${faNum(pl.p)}</span> <small>تومان / لایسنس دائم</small>` : 'استعلام قیمت'}</span>
    <ul>${pl.f.map(f => f === '—' ? '' : `<li>${Icons.check}${f}</li>`).join('')}</ul>
    <button class="btn ${i === 1 ? 'btn-accent' : 'btn-primary'} btn-block" data-consult data-subject="پلن ${pl.n} نرم‌افزار ${s.name}">انتخاب و مشاوره خرید</button></div>`).join('');
  $('#swReq').innerHTML = [['حداقل سیستم مورد نیاز', s.req[0]], ['پیشنهاد سرور و شبکه', s.req[1]]].map(r => `<div class="req-card"><h3><span data-icon="cpu"></span>${r[0]}</h3><ul>${r[1].map(x => `<li><span>${x[0]}</span><b>${x[1]}</b></li>`).join('')}</ul></div>`).join('');
  $('#swFaq').innerHTML = PH.faqs.slice(3, 7).map(f => `<div class="acc-item"><button class="acc-btn">${f.q}${Icons.chevDown}</button><div class="acc-body"><div>${f.a}</div></div></div>`).join('');
  $('#swOthers').innerHTML = PH.software.filter(x => x.id !== s.id).map(x => softCard(x, false)).join('');
  $('#swOthers').closest('section').style.background = 'var(--bg-soft)';
  $$('#swOthers .soft-card').forEach(c => { c.style.background = '#fff'; c.style.borderColor = 'var(--line)'; const h = c.querySelector('h3'); if (h) h.style.color = 'var(--ink)'; const ps = c.querySelectorAll('p'); ps.forEach(p => p.style.color = 'var(--body)'); /* keep original softCard buttons (correct links) */ });
  /* fix other-cards buttons */
  $$('#swOthers .soft-card').forEach((c, i) => { const o = PH.software.filter(x => x.id !== s.id)[i]; c.querySelector('.soft-btns').innerHTML = `<a class="btn btn-primary" style="flex:1" href="${linkSoftware(o.id)}">مشاهده جزئیات</a><button class="btn btn-outline" style="flex:1" data-consult data-subject="نرم‌افزار ${o.name}">مشاوره</button>`; const f = c.querySelector('.soft-for'); if (f) { f.style.background = 'var(--bg-soft)'; f.style.color = 'var(--muted)'; } const pr = c.querySelector('.soft-price b'); if (pr) pr.style.color = 'var(--brand-strong)'; });
  hydrateIcons(document);
}
/* ---------- compare ---------- */
function initCompare() {
  const box = $('#cmpBox'), empty = $('#cmpEmpty');
  const render = () => {
    const keys = store.get('cmp', []);
    if (!keys.length) { box.innerHTML = ''; empty.style.display = ''; return; }
    empty.style.display = 'none';
    const items = keys.map(k => { const [t, id] = k.split(':'); return t === 's' ? { ...soft(id), _t: 's', img: null } : { ...prod(id), _t: 'p' }; }).filter(i => i && (i.name));
    const rows = [
      ['قیمت', items.map(i => `<b class="num" style="font-size:17px;color:var(--brand-strong)">${i.price ? faNum(i.price) + ' تومان' : 'تماس بگیرید'}</b>`)],
      ['امتیاز', items.map(i => `${stars(i.rating)}<div class="num" style="font-size:12px;color:var(--muted)">${faNum(i.rating)} (${faNum(i.reviews)})</div>`)],
      ['برند / نسخه', items.map(i => i._t === 's' ? i.tag : i.brand)],
      ['گارانتی / پشتیبانی', items.map(() => (window.PH_WP && PH_WP.warranty) || '۱۲ تا ۱۸ ماه + پشتیبانی')],
      ['نصب و آموزش', items.map(() => 'رایگان (ریموت/حضوری)')],
      ['ویژگی کلیدی', items.map(i => (i._t === 's' ? i.features.map(f => f[0].trim()) : i.features).slice(0, 2).join('<br>'))],
    ];
    box.innerHTML = `<div class="cmp-add"><select class="input" id="cmpAddSel"><option value="">+ افزودن محصول یا نرم‌افزار...</option><optgroup label="تجهیزات">${PH.products.map(p => `<option value="p:${p.id}">${p.name}</option>`).join('')}</optgroup><optgroup label="نرم‌افزار">${PH.software.map(s => `<option value="s:${s.id}">نرم‌افزار ${s.name}</option>`).join('')}</optgroup></select><button class="btn btn-danger-soft" id="cmpClear"><span data-icon="trash"></span>حذف همه</button></div>
    <div class="cmp-wrap"><table class="cmp-table"><tr><td>محصول</td>${items.map(i => `<td><button class="icon-btn" style="width:32px;height:32px;margin-bottom:10px" data-cmp="${i._t}:${i.id}" aria-label="حذف">${Icons.x}</button>${i.img ? `<img class="cmp-pimg" src="${i.img}" alt="">` : `<div class="soft-mono" style="background:${i.color}1A;color:${i.color};margin:0 auto 10px;width:72px;height:72px;font-size:30px">${i.mono}</div>`}<b>${i._t === 's' ? 'نرم‌افزار ' + i.name : i.name}</b><div style="margin-top:10px"><a class="btn btn-primary btn-sm" href="${i._t === 's' ? linkSoftware(i.id) : linkProduct(i.id)}">مشاهده</a></div></td>`).join('')}</tr>
    ${rows.map((r, i) => `<tr class="${i % 2 ? 'hl' : ''}"><td>${r[0]}</td>${r[1].map(c => `<td>${c}</td>`).join('')}</tr>`).join('')}</table></div>`;
    hydrateIcons(box);
    $('#cmpAddSel').addEventListener('change', e => { if (!e.target.value) return; const [t, id] = e.target.value.split(':'); toggleCmp(id, t); render(); });
    $('#cmpClear').addEventListener('click', () => { store.set('cmp', []); updateBadges(); render(); });
  };
  render();
}
/* ---------- WP shop config (settings-driven; static fallbacks) ---------- */
const phShipCfg = () => (window.PH_WP && PH_WP.ship) || { std: 350000, exp: 550000, freeOver: 50000000 };
const phCouponCfg = () => (window.PH_WP && PH_WP.coupon) || { code: 'PEJVAK10', off: 10 };
const shipFreeTxt = () => { const f = phShipCfg().freeOver; return f >= 1000000 ? faNum(f / 1000000) + ' میلیون تومان' : faNum(f) + ' تومان'; };
function phShipCost(total) {
  const c = phShipCfg();
  const sel = document.querySelector('input[name=ship]:checked');
  if (!sel || !('price' in sel.dataset)) return total >= c.freeOver ? 0 : c.std;
  if (sel.dataset.free && total >= c.freeOver) return 0;
  return +sel.dataset.price || 0;
}
/* ---------- cart ---------- */
function initCart() {
  const items = cartDetailed(), wrap = $('#cartWrap');
  if (!items.length) { wrap.innerHTML = `<div class="empty" style="grid-column:1/-1">${Icons.cart}<h3>سبد خرید شما خالی است</h3><p>محصولات فروشگاه را ببینید و خرید را شروع کنید.</p><a class="btn btn-primary" style="margin-top:14px" href="${linkPage('shop')}">رفتن به فروشگاه</a></div>`; return; }
  const total = cartTotal(items), ship = phShipCost(total);
  wrap.innerHTML = `<div>${items.map(i => `<div class="cart-item"><a href="${linkProduct(i.id)}"><img src="${i.img}" alt="${i.name}"></a>
    <div><span class="brand">${i.brand}</span><a href="${linkProduct(i.id)}"><h3>${i.name}</h3></a>
    <div class="row"><div class="qty"><button data-cq="${i.id}|1">+</button><input value="${faNum(i.q)}" readonly><button data-cq="${i.id}|-1">−</button></div><button class="remove" data-rm="${i.id}">${Icons.trash}حذف</button></div></div>
    <div class="line-price"><b class="num">${faNum(i.price * i.q)} تومان</b><span class="num">واحد: ${faNum(i.price)}</span></div></div>`).join('')}
    <a class="link-more" href="${linkPage('shop')}">${Icons.arrowLeft} ادامه خرید</a></div>
  <aside class="summary"><h3>خلاصه سفارش</h3>
    <div class="sum-row"><span>جمع کالاها</span><span class="num">${faNum(total)} تومان</span></div>
    <div class="sum-row"><span>هزینه ارسال</span><span class="num">${ship ? faNum(ship) + ' تومان' : 'رایگان'}</span></div>
    <div class="sum-row"><span>تخفیف</span><span class="off num" id="cartOff">۰ تومان</span></div>
    <div class="coupon"><input class="input" id="couponIn" placeholder="کد تخفیف"><button class="btn btn-dark btn-sm" id="couponBtn">اعمال</button></div>
    <div class="sum-row total"><span>مبلغ قابل پرداخت</span><span class="num" id="cartTotal">${faNum(total + ship)} تومان</span></div>
    <a class="btn btn-primary btn-block btn-lg" style="margin-top:16px" href="${linkPage('checkout')}">ادامه و تسویه حساب</a>
    <p class="form-hint" style="text-align:center;margin-top:10px">ارسال برای خرید بالای ${shipFreeTxt()} رایگان است.</p></aside>`;
  $$('[data-cq]').forEach(b => b.addEventListener('click', () => { const [id, d] = b.dataset.cq.split('|'); const it = cartGet().find(x => x.id === id); setQty(id, it.q + (+d)); }));
  $('#couponBtn').addEventListener('click', () => {
    const v = $('#couponIn').value.trim(), CP = phCouponCfg();
    if (v && v === CP.code) { const d = Math.round(total * CP.off / 100), t = total + ship - d; $('#cartOff').textContent = faNum(d) + ' تومان'; $('#cartTotal').textContent = faNum(t) + ' تومان'; store.set('coupon', CP.code); showToast(`کد تخفیف ${faNum(CP.off)}٪ اعمال شد`, 'success'); }
    else showToast('کد تخفیف معتبر نیست', 'error');
  });
}
/* ---------- checkout ---------- */
function paintPayResult(res, code) {
  ['coSteps', 'coBack', 'coNext'].forEach(id => { const el = document.getElementById(id); if (el) el.style.display = 'none'; });
  const box = $('#coMain');
  const ok = res === 'success';
  if (ok) { store.set('cart', []); store.set('coupon', null); updateBadges(); }
  const msg = res === 'success'
    ? ['پرداخت با موفقیت انجام شد', 'سفارش شما ثبت و پرداخت شد. کارشناسان ما برای هماهنگی ارسال با شما تماس می‌گیرند.']
    : res === 'cancelled'
      ? ['پرداخت لغو شد', 'سفارش شما با وضعیت «در انتظار پرداخت» ثبت شد؛ می‌توانید بعداً از حساب کاربری پیگیری کنید.']
      : ['پرداخت ناموفق بود', 'سفارش ثبت شد اما پرداخت تکمیل نشد. لطفاً دوباره تلاش کنید یا با ما تماس بگیرید.'];
  if (box) box.innerHTML = `<div class="success-box"><div class="ok" style="${ok ? '' : 'background:#dc2626'}">${ok ? Icons.check : Icons.x}</div><h2>${msg[0]}</h2><p>${msg[1]}</p>${code ? `<div class="order-code num">کد سفارش: ${code}</div>` : ''}<div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap"><a class="btn btn-primary" href="${linkPage('account')}">پیگیری در حساب کاربری</a><a class="btn btn-outline" href="${linkPage('shop')}">ادامه خرید</a></div></div>`;
  window.scrollTo({ top: 0 });
}
function initCheckout() {
  const payRes = q('pay') || '', payCode = q('code') || '';
  if (window.PH_WP && payRes) { paintPayResult(payRes, payCode); return; }
  const items = cartDetailed();
  if (!items.length && !store.get('lastOrder', null)) { location.href = (window.PH_WP && PH_WP.links && PH_WP.links.cart) || 'cart.html'; return; }
  let step = 0;
  const total = cartTotal(items);
  const coupon = store.get('coupon', null);
  const CP = phCouponCfg();
  const discount = coupon && coupon === CP.code ? Math.round(total * CP.off / 100) : 0;
  let ship = phShipCost(total), grand = total + ship - discount;
  $('#coItems').innerHTML = items.map(i => `<div class="mini-item"><img src="${i.img}" alt=""><div><b>${i.name}</b><span class="num">${faNum(i.q)} × ${faNum(i.price)}</span></div><b class="num" style="font-size:13px">${faNum(i.q * i.price)}</b></div>`).join('');
  const paintTotals = () => {
    ship = phShipCost(total); grand = total + ship - discount;
    $('#coTotal').innerHTML = `<div class="sum-row"><span>جمع کالاها</span><span class="num">${faNum(total)} تومان</span></div><div class="sum-row"><span>ارسال</span><span class="num">${ship ? faNum(ship) + ' تومان' : 'رایگان'}</span></div>${discount ? `<div class="sum-row"><span>تخفیف ${CP.code}</span><span class="off num">${faNum(discount)} تومان</span></div>` : ''}<div class="sum-row total"><span>قابل پرداخت</span><span class="num">${faNum(grand)} تومان</span></div>`;
  };
  paintTotals();
  const render = () => {
    $$('.co-step').forEach((s, i) => s.style.display = i === step ? '' : 'none');
    $$('#coSteps .step').forEach((s, i) => { s.classList.toggle('active', i === step); s.classList.toggle('done', i < step); });
    $('#coBack').style.visibility = step === 0 ? 'hidden' : 'visible';
    $('#coNext').innerHTML = step === 2 ? 'پرداخت و ثبت سفارش' : 'ادامه <span data-icon="arrowLeft"></span>'; hydrateIcons($('#coNext'));
    if (step === 2) $('#coReview').innerHTML = `<div class="sum-row"><span>تحویل‌گیرنده</span><b>${$('#fName').value} — <span class="num">${$('#fPhone').value}</span></b></div><div class="sum-row"><span>آدرس</span><b>${$('#fCity').value}، ${$('#fAddr').value}</b></div><div class="sum-row"><span>ارسال</span><b>${$('input[name=ship]:checked')?.closest('.radio-card').querySelector('b').textContent}</b></div><div class="sum-row"><span>پرداخت</span><b>${$('input[name=pay]:checked')?.closest('.radio-card').querySelector('b').textContent}</b></div>`;
  };
  $('#coNext').addEventListener('click', () => {
    if (step === 0) {
      let ok = true;
      [['fName', v => v.trim().length >= 3], ['fPhone', v => /^0?9\d{9}$/.test(v.trim().replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d)))], ['fCity', v => v.trim().length >= 2], ['fAddr', v => v.trim().length >= 8]].forEach(([id, fn]) => { const el = $('#' + id), good = fn(el.value); el.closest('.field').classList.toggle('invalid', !good); if (!good) ok = false; });
      if (!ok) { showToast('لطفاً اطلاعات را کامل و صحیح وارد کنید', 'error'); return; }
    }
    if (step < 2) { step++; render(); window.scrollTo({ top: 0, behavior: 'smooth' }); }
    else {
      const finish = code => {
        const orders = store.get('orders', []);
        orders.unshift({ code, date: new Date().toLocaleDateString('fa-IR'), total: grand, count: items.reduce((a, i) => a + i.q, 0), status: 'در حال پردازش' });
        store.set('orders', orders); store.set('lastOrder', code);
        store.set('cart', []); store.set('coupon', null); updateBadges();
        $('#coMain').innerHTML = `<div class="success-box"><div class="ok">${Icons.check}</div><h2>سفارش شما با موفقیت ثبت شد</h2><p>کارشناسان ما برای هماهنگی ارسال و نصب با شما تماس می‌گیرند.</p><div class="order-code num">کد سفارش: ${code}</div><div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap"><a class="btn btn-primary" href="${linkPage('account')}">پیگیری در حساب کاربری</a><a class="btn btn-outline" href="${linkPage('shop')}">ادامه خرید</a></div></div>`;
        window.scrollTo({ top: 0, behavior: 'smooth' });
      };
      const randCode = () => 'PH-' + Math.floor(100000 + Math.random() * 900000);
      if (window.PH_WP) {
        $('#coNext').disabled = true;
        const shipSel = document.querySelector('input[name=ship]:checked');
        const shipCode = (shipSel && shipSel.dataset.ship) || 'std';
        const shipName = shipSel?.closest('.radio-card').querySelector('b').textContent || '', payName = document.querySelector('input[name=pay]:checked')?.closest('.radio-card').querySelector('b').textContent || '';
        phAjax('ph_create_order', { name: $('#fName').value, phone: $('#fPhone').value, province: $('#fProv')?.value || '', city: $('#fCity').value, addr: $('#fAddr').value, postcode: $('#fPostcode')?.value || '', note: $('#fNote')?.value || '', ship: shipName, ship_code: shipCode, coupon: coupon || '', pay: payName, pay_code: (document.querySelector('input[name=pay]:checked') || {}).value || '', items: JSON.stringify(items.map(i => ({ id: i.id, q: i.q, price: i.price }))), total: grand }).then(res => { $('#coNext').disabled = false; if (res && res.success && res.data && res.data.code) { if (res.data.pay_url) { location.href = res.data.pay_url; return; } finish(res.data.code); } else showToast((res && res.data) || 'خطا در ثبت سفارش؛ لطفاً دوباره تلاش کنید', 'error'); }).catch(() => { $('#coNext').disabled = false; showToast('خطا در ارتباط با سرور', 'error'); });
      } else finish(randCode());
    }
  });
  $('#coBack').addEventListener('click', () => { if (step > 0) { step--; render(); } });
  $$('input[name=ship],input[name=pay]').forEach(r => r.addEventListener('change', () => { const n = r.name; $$(`input[name=${n}]`).forEach(x => x.closest('.radio-card').classList.remove('active')); r.closest('.radio-card').classList.add('active'); if (n === 'ship' && typeof paintTotals === 'function') paintTotals(); }));
  render();
}
/* ---------- account ---------- */
function initAccount() {
  if (window.PH_WP) return initAccountWP();
  const user = store.get('user', null);
  if (!user) {
    $('#accGuest').style.display = ''; $('#accMain').style.display = 'none';
    $('#otpBoxes').innerHTML = [0, 1, 2, 3, 4].map(() => `<input maxlength="1" inputmode="numeric">`).join('');
    const boxes = $$('#otpBoxes input');
    boxes.forEach((b, i) => { b.addEventListener('input', () => { b.value = b.value.replace(/\D/g, '').slice(-1); if (b.value && i < 4) boxes[i + 1].focus(); }); b.addEventListener('keydown', e => { if (e.key === 'Backspace' && !b.value && i > 0) boxes[i - 1].focus(); }); });
    $('#sendOtp').addEventListener('click', () => {
      const ph = $('#loginPhone').value.trim().replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
      if (!/^0?9\d{9}$/.test(ph)) { showToast('شماره موبایل معتبر وارد کنید', 'error'); return; }
      $('#otpStep').style.display = ''; $('#sendOtp').style.display = 'none'; showToast('کد تأیید ارسال شد (نسخه نمایشی: هر کدی)', 'info'); boxes[0].focus();
    });
    $('#verifyOtp').addEventListener('click', () => {
      const code = boxes.map(b => b.value).join('');
      if (code.length < 5) { showToast('کد ۵ رقمی را کامل وارد کنید', 'error'); return; }
      store.set('user', { name: 'کاربر پژواک', phone: $('#loginPhone').value }); location.reload();
    });
    return;
  }
  $('#accGuest').style.display = 'none'; $('#accMain').style.display = '';
  $('#accUserName').textContent = user.name; $('#accUserPhone').textContent = user.phone;
  const orders = store.get('orders', []), wish = store.get('wish', []).map(prod).filter(Boolean);
  $('#statOrders').textContent = faNum(orders.length); $('#statWish').textContent = faNum(wish.length);
  $('#statTotal').textContent = faNum(orders.reduce((a, o) => a + o.total, 0));
  $('#ordersTable').innerHTML = ordersTableHTML(orders);
  $('#wishGrid').innerHTML = wish.length ? wish.map(productCard).join('') : `<div class="empty">${Icons.heart}<p>لیست علاقه‌مندی‌ها خالی است.</p></div>`;
  hydrateIcons(document);
  $$('.acc-nav button[data-acc]').forEach(b => b.addEventListener('click', () => { $$('.acc-nav button').forEach(x => x.classList.remove('active')); b.classList.add('active'); $$('.acc-panel').forEach(p => p.classList.toggle('active', p.id === 'acc-' + b.dataset.acc)); }));
  $('#btnLogout').addEventListener('click', () => { store.set('user', null); location.reload(); });
  $('#addrForm')?.addEventListener('submit', e => { e.preventDefault(); const gv = id => { const el = document.getElementById(id); return el ? el.value.trim() : ''; }; const tx = e.target.querySelector('textarea'); const a = gv('addrText') || (tx ? tx.value.trim() : ''); if (a.length < 8) { showToast('آدرس را کامل وارد کنید', 'error'); return; } const l = store.get('addrs', []); l.push({ title: gv('addrTitle'), city: gv('addrCity'), addr: a }); store.set('addrs', l); const box = $('#addrList'); if (box) box.innerHTML = l.map(x => `<div class="co-box" style="padding:12px 16px"><b>${x.title || 'آدرس'}</b><p style="font-size:13.5px;margin-top:4px">${x.addr || ''}</p></div>`).join(''); e.target.reset(); showToast('آدرس ذخیره شد', 'success'); });
  $('#passForm')?.addEventListener('submit', e => { e.preventDefault(); const f = document.getElementById('profName') || e.target.querySelector('input'); const nm = f ? f.value.trim() : ''; if (nm) { user.name = nm; store.set('user', user); $('#accUserName').textContent = nm; } showToast('تغییرات ذخیره شد', 'success'); e.target.reset(); });
}
/* ---------- orders table (shared by demo + WordPress) ---------- */
function ordersTableHTML(orders) {
  if (!orders.length) return `<div class="empty">${Icons.package}<p>هنوز سفارشی ثبت نکرده‌اید.</p></div>`;
  const rows = orders.map(o => {
    let track = '<span style="color:var(--faint)">—</span>';
    if (o.tracking) {
      const car = o.carrier ? `<div style="font-size:11.5px;color:var(--muted);margin-top:4px">${o.carrier}</div>` : '';
      const link = o.track_url ? `<a class="track-link" href="${o.track_url}" target="_blank" rel="noopener">رهگیری مرسوله</a>` : '';
      track = `<div><span class="track-code">${o.tracking}</span>${car}${link}</div>`;
    }
    return `<tr><td class="num" style="font-weight:800">${o.code}${o.ref ? `<br><small style="font-weight:400;color:var(--muted)">مرجع پرداخت: ${o.ref}</small>` : ''}</td><td>${o.date}</td><td class="num">${faNum(o.count)}</td><td class="num">${faNum(o.total)} تومان</td><td><span class="badge badge-success">${o.status}</span></td><td>${track}</td></tr>`;
  }).join('');
  return `<div class="table-wrap"><table class="data-table"><tr><th>کد سفارش</th><th>تاریخ</th><th>تعداد</th><th>مبلغ</th><th>وضعیت</th><th>پیگیری مرسوله</th></tr>${rows}</table></div>`;
}
/* ---------- account (WordPress: real user, orders, addresses) ---------- */
function initOtpLogin() {
  if (!document.getElementById('otpBox')) return;
  $('#otpToPw')?.addEventListener('click', () => { $('#otpBox').style.display = 'none'; $('#pwBox').style.display = ''; });
  $('#pwToOtp')?.addEventListener('click', () => { $('#pwBox').style.display = 'none'; $('#otpBox').style.display = ''; });
  const fa2en = v => String(v || '').replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d)).replace(/[^0-9]/g, '');
  const msg = k => ({ 'bad-mobile': 'شماره موبایل معتبر نیست', 'cooldown': 'کمی صبر کنید و دوباره تلاش کنید', 'too-many': 'تعداد تلاش‌ها زیاد شد؛ یک ساعت بعد تلاش کنید', 'expired': 'کد منقضی شد؛ کد جدید بگیرید', 'wrong': 'کد واردشده صحیح نیست', 'bad-code': 'کد را کامل وارد کنید', 'sms-off': 'ورود پیامکی فعال نیست', 'reg-off': 'ثبت‌نام در حال حاضر بسته است', 'nonce': 'خطای امنیتی؛ صفحه را رفرش کنید' }[k] || 'خطا؛ لطفاً دوباره تلاش کنید');
  let timer = null;
  const startTimer = sec => { let s = sec; const r = $('#otpResend'); r.disabled = true; clearInterval(timer); timer = setInterval(() => { s--; if (s <= 0) { clearInterval(timer); r.disabled = false; r.textContent = 'ارسال مجدد کد'; } else { const t = $('#otpTimer'); if (t) t.textContent = faNum(s); } }, 1000); };
  const refill = sec => { const r = $('#otpResend'); r.innerHTML = 'ارسال مجدد (<span class="num" id="otpTimer">' + faNum(sec) + '</span>)'; startTimer(sec); };
  $('#otpSend')?.addEventListener('click', () => {
    const mobile = fa2en($('#otpMobile').value);
    if (!/^09\d{9}$/.test(mobile)) { showToast('شماره موبایل معتبر نیست', 'error'); return; }
    const btn = $('#otpSend'); btn.disabled = true;
    phAjax('ph_otp_send', { mobile }).then(res => { btn.disabled = false; if (res && res.success) { $('#otpStep1').style.display = 'none'; $('#otpStep2').style.display = ''; $('#otpTo').textContent = mobile; refill((res.data && res.data.cooldown) || 120); const f = document.querySelector('#otpInputs input'); if (f) f.focus(); } else showToast(msg(res && res.data), 'error'); }).catch(() => { btn.disabled = false; showToast('خطا در ارتباط با سرور', 'error'); });
  });
  $('#otpResend')?.addEventListener('click', () => {
    phAjax('ph_otp_send', { mobile: fa2en($('#otpMobile').value) }).then(res => { if (res && res.success) refill((res.data && res.data.cooldown) || 120); else showToast(msg(res && res.data), 'error'); }).catch(() => showToast('خطا در ارتباط با سرور', 'error'));
  });
  $('#otpEdit')?.addEventListener('click', () => { clearInterval(timer); $('#otpStep2').style.display = 'none'; $('#otpStep1').style.display = ''; });
  const inputs = $$('#otpInputs input');
  inputs.forEach((inp, i) => {
    inp.addEventListener('input', () => { inp.value = fa2en(inp.value).slice(-1); if (inp.value && inputs[i + 1]) inputs[i + 1].focus(); });
    inp.addEventListener('keydown', e => { if (e.key === 'Backspace' && !inp.value && inputs[i - 1]) inputs[i - 1].focus(); });
  });
  $('#otpVerify')?.addEventListener('click', () => {
    const code = inputs.map(i => i.value).join('');
    if (code.length < 5) { showToast('کد را کامل وارد کنید', 'error'); return; }
    const btn = $('#otpVerify'); btn.disabled = true;
    phAjax('ph_otp_verify', { mobile: fa2en($('#otpMobile').value), code }).then(res => { btn.disabled = false; if (res && res.success) { showToast('خوش آمدید', 'success'); location.href = (res.data && res.data.redirect) || location.pathname; } else showToast(msg(res && res.data), 'error'); }).catch(() => { btn.disabled = false; showToast('خطا در ارتباط با سرور', 'error'); });
  });
}
function initAccountWP() {
  const U = (window.PH_WP && PH_WP.user) || {};
  if (!U.logged) { $('#accGuest').style.display = ''; const m = $('#accMain'); if (m) m.style.display = 'none'; initOtpLogin(); return; }
  $('#accGuest').style.display = 'none'; $('#accMain').style.display = '';
  if (U.name) $('#accUserName').textContent = U.name;
  const wish = store.get('wish', []).map(prod).filter(Boolean);
  $('#statWish').textContent = faNum(wish.length);
  $('#wishGrid').innerHTML = wish.length ? wish.map(productCard).join('') : `<div class="empty">${Icons.heart}<p>لیست علاقه‌مندی‌ها خالی است.</p></div>`;
  $('#ordersTable').innerHTML = '<p style="color:var(--muted);font-size:13.5px">در حال بارگذاری سفارش‌ها...</p>';
  phAjax('ph_my_orders', {}).then(res => {
    const orders = res && res.success ? (res.data || []) : [];
    $('#statOrders').textContent = faNum(orders.length);
    $('#statTotal').textContent = faNum(orders.reduce((a, o) => a + (+o.total || 0), 0));
    $('#ordersTable').innerHTML = ordersTableHTML(orders);
  }).catch(() => { $('#ordersTable').innerHTML = `<div class="empty">${Icons.package}<p>خطا در بارگذاری سفارش‌ها.</p></div>`; });
  const paintAddrs = list => { $('#addrList').innerHTML = list.length ? list.map(a => `<div class="co-box" style="padding:12px 16px"><b>${a.title || 'آدرس'}</b> <span style="color:var(--muted);font-size:13px">${a.city || ''}</span><p style="font-size:13.5px;margin-top:4px">${a.addr || ''}</p></div>`).join('') : '<p style="color:var(--muted);font-size:13.5px">هنوز آدرسی ثبت نشده است.</p>'; };
  phAjax('ph_get_addresses', {}).then(res => paintAddrs(res && res.success ? (res.data || []) : [])).catch(() => paintAddrs([]));
  hydrateIcons(document);
  $$('.acc-nav button[data-acc]').forEach(b => b.addEventListener('click', () => { $$('.acc-nav button').forEach(x => x.classList.remove('active')); b.classList.add('active'); $$('.acc-panel').forEach(p => p.classList.toggle('active', p.id === 'acc-' + b.dataset.acc)); }));
  $('#btnLogout')?.addEventListener('click', () => { location.href = U.logout || '/'; });
  $('#addrForm')?.addEventListener('submit', e => {
    e.preventDefault();
    const t = $('#addrTitle').value.trim(), c = $('#addrCity').value.trim(), a = $('#addrText').value.trim();
    if (a.length < 8) { showToast('آدرس را کامل وارد کنید', 'error'); return; }
    phAjax('ph_save_address', { title: t, city: c, addr: a }).then(res => { if (res && res.success) { paintAddrs(res.data || []); e.target.reset(); showToast('آدرس ذخیره شد', 'success'); } else showToast((res && res.data) || 'خطا در ذخیره آدرس', 'error'); }).catch(() => showToast('خطا در ذخیره آدرس', 'error'));
  });
  $('#passForm')?.addEventListener('submit', e => {
    e.preventDefault();
    const n = $('#profName').value.trim(), em = $('#profEmail').value.trim(), ps = $('#profPass').value;
    phAjax('ph_update_profile', { name: n, email: em, pass: ps }).then(res => { if (res && res.success) { if (res.data && res.data.name) $('#accUserName').textContent = res.data.name; e.target.reset(); showToast('تغییرات ذخیره شد', 'success'); } else showToast((res && res.data) || 'خطا در ذخیره', 'error'); }).catch(() => showToast('خطا در ذخیره', 'error'));
  });
}
/* ---------- misc pages ---------- */
function initFaq() {
  const cats = [...new Set(PH.faqs.map(f => f.c))];
  $('#faqCats').innerHTML = [`<button class="active" data-c="">همه موضوعات</button>`, ...cats.map(c => `<button data-c="${c}">${c}</button>`)].join('');
  const render = (c, term) => {
    const list = PH.faqs.filter(f => (!c || f.c === c) && (!term || (f.q + f.a).includes(term)));
    $('#faqList').innerHTML = list.length ? `<div class="accordion">${list.map(f => `<div class="acc-item"><button class="acc-btn"><span>${f.q}</span>${Icons.chevDown}</button><div class="acc-body"><div><span class="badge badge-gray" style="margin-bottom:8px">${f.c}</span><br>${f.a}</div></div></div>`).join('')}</div>` : `<div class="empty">${Icons.message}<p>سؤالی یافت نشد.</p></div>`;
  };
  let cur = '';
  $('#faqCats').addEventListener('click', e => { const b = e.target.closest('button'); if (!b) return; $$('#faqCats button').forEach(x => x.classList.remove('active')); b.classList.add('active'); cur = b.dataset.c; render(cur, $('#faqSearch').value.trim()); });
  $('#faqSearch').addEventListener('input', e => render(cur, e.target.value.trim()));
  render('', '');
}
function initBlog() {
  const paint = c => { const list = !c ? PH.posts : PH.posts.filter(p => p.cat === c); $('#blogGrid').innerHTML = list.length ? list.map(postCard).join('') : '<div class="empty" style="grid-column:1/-1"><p>مطلبی در این دسته نیست.</p></div>'; hydrateIcons(document); };
  $('#blogChips')?.addEventListener('click', e => { const a = e.target.closest('a[data-bcat]'); if (!a) return; e.preventDefault(); $$('#blogChips a').forEach(x => x.classList.remove('active')); a.classList.add('active'); paint(a.dataset.bcat); });
  paint('');
}
function initArticle() {
  const p = PH.posts.find(x => x.id === q('id')) || PH.posts.find(x => x.id === (window.PH_SINGLE_ID || '')) || PH.posts[0];
  document.title = `${p.title} | مجله پژواک حساب`;
  $('#artWrap').innerHTML = `<article class="article"><img class="article-cover" src="${p.img}" alt="${p.title}"><div class="article-body">
    <span class="badge badge-brand">${p.cat}</span><h1>${p.title}</h1>
    <div class="article-meta"><span><span data-icon="user"></span>تحریریه پژواک حساب</span><span><span data-icon="cal"></span>${p.date}</span><span><span data-icon="clock"></span>زمان مطالعه: ${p.read}</span></div>
    <div class="prose"><p>${p.excerpt} در این راهنما به‌صورت کاربردی و قدم‌به‌قدم، مهم‌ترین نکاتی را بررسی می‌کنیم که قبل از تصمیم‌گیری باید بدانید؛ نکاتی که از تجربه واقعی استقرار در صدها فروشگاه به دست آمده است.</p>
    <h3>۱. نیازسنجی دقیق قبل از خرید</h3><p>اولین قدم، شناخت دقیق نیاز کسب‌وکار شماست: حجم فروش روزانه، تعداد کاربران، نیاز به انبارداری و اتصال به تجهیزات. انتخابی که بدون نیازسنجی انجام شود، معمولاً یا پرهزینه است یا ناکارآمد.</p>
    <h3>۲. معیارهای مهم انتخاب</h3><ul><li>سرعت و پایداری در ساعات اوج فروش</li><li>سازگاری با تجهیزات فروشگاهی موجود</li><li>گزارش‌های مدیریتی کاربردی و لحظه‌ای</li><li>آموزش، پشتیبانی و خدمات پس از فروش واقعی</li></ul>
    <h3>۳. اشتباهات رایج خریداران</h3><p>خرید بر اساس قیمتِ صرف، نادیده گرفتن هزینه‌های پنهان مثل آموزش و پشتیبانی، و عدم تست عملیاتی قبل از خرید، سه اشتباه پرتکرار است. پیشنهاد ما همیشه تست دمو و مشاوره با کارشناس مستقل است.</p>
    <h3>جمع‌بندی</h3><p>اگر برای انتخاب گزینه مناسب مطمئن نیستید، کارشناسان پژواک حساب آماده‌اند رایگان راهنمایی‌تان کنند تا بهترین تصمیم را برای کسب‌وکارتان بگیرید.</p></div>
    <div class="article-tags"><a href="blog.html">${p.cat}</a><a href="blog.html">راهنمای خرید</a><a href="${linkPage('shop')}">تجهیزات فروشگاهی</a></div></div></article>`;
  $('#relPosts').innerHTML = PH.posts.filter(x => x.id !== p.id).slice(0, 3).map(postCard).join('');
  hydrateIcons(document);
}
const BIZ = {
  supermarket: { n: 'سوپرمارکت و هایپرمارکت', i: 'store', img: 'assets/img/img-supermarket.jpg', d: 'سرعت در صندوق، مدیریت هزاران کالا و کنترل انبار؛ راهکار کامل سوپرمارکت‌ها.', needs: ['صدور فاکتور زیر ۱۰ ثانیه', 'اتصال ترازو و بارکدخوان', 'انبار چندگانه و کسری خودکار', 'باشگاه مشتریان و تخفیف'], soft: 'baran', prods: ['pos-aio-p15', 'receipt-xp80', 'scan-hh120'] },
  clothing: { n: 'فروشگاه پوشاک', i: 'shirt', img: 'assets/img/img-supermarket.jpg', d: 'ماتریس سایز و رنگ، مدیریت شعب و کمپین‌های فصلی برای بوتیک‌ها و برندها.', needs: ['تعریف سایز/رنگ', 'مدیریت چندشعبه', 'تخفیف فصلی و کوپن', 'تسویه صندوق چندشیفته'], soft: 'zafaran', prods: ['pos-dual-d17', 'label-dt420', 'scan-wl200'] },
  mobile: { n: 'فروشگاه موبایل', i: 'mobile', img: 'assets/img/img-pos-allinone.jpg', d: 'سریال‌دار کردن کالا، گارانتی، اقساط و خدمات پس از فروش تخصصی موبایل.', needs: ['ردیابی سریال کالا', 'فروش اقساطی', 'مدیریت گارانتی و مرجوعی', 'چاپ فاکتور رسمی'], soft: 'baran', prods: ['pos-aio-p15', 'label-dt420', 'drawer-m5'] },
  home: { n: 'لوازم خانگی', i: 'home', img: 'assets/img/img-ready-system.jpg', d: 'فروش حجیم، ارسال و نصب، اقساط و حسابداری دقیق برای فروشگاه‌های بزرگ.', needs: ['فاکتور و پیش‌فاکتور', 'مدیریت ارسال و نصب', 'فروش اقساطی و چک', 'انبار حجیم'], soft: 'pejvak', prods: ['ready-market-pro', 'receipt-xp80', 'display-vfd220'] },
  restaurant: { n: 'رستوران و کافه', i: 'coffee', img: 'assets/img/img-restaurant.jpg', d: 'مدیریت میز، آشپزخانه، پیک و منوی دیجیتال؛ همه‌چیز برای فروش بیشتر.', needs: ['چیدمان سالن و میز', 'ارسال سفارش به آشپزخانه', 'منوی دیجیتال QR', 'مدیریت پیک'], soft: 'pos-suite', prods: ['ready-cafe-lite', 'receipt-bt58', 'drawer-m5'] },
  chain: { n: 'فروشگاه زنجیره‌ای', i: 'layers', img: 'assets/img/img-supermarket.jpg', d: 'مدیریت متمرکز شعب، قیمت‌گذاری یکپارچه و داشبورد مقایسه عملکرد.', needs: ['همگام‌سازی لحظه‌ای شعب', 'قیمت‌گذاری متمرکز', 'انتقال بین شعب', 'داشبورد مدیریتی'], soft: 'zafaran', prods: ['pos-dual-d17', 'scan-wl200', 'label-ind300'] },
  wholesale: { n: 'عمده‌فروشی', i: 'package', img: 'assets/img/img-ready-system.jpg', d: 'فاکتور حجیم، اعتبار مشتریان، چک و خزانه‌داری برای عمده‌فروشان.', needs: ['فاکتور سریع حجیم', 'سقف اعتبار مشتری', 'مدیریت چک و تسویه', 'انبار چندگانه'], soft: 'pejvak', prods: ['ready-market-pro', 'scan-wl200', 'label-ind300'] },
  service: { n: 'کسب‌وکارهای خدماتی', i: 'briefcase', img: 'assets/img/img-restaurant.jpg', d: 'صدور فاکتور خدماتی، قراردادها، دریافتی‌ها و حسابداری تمیز.', needs: ['فاکتور خدماتی و قرارداد', 'یادآوری سررسید', 'مدیریت دریافتی', 'گزارش سود خدمات'], soft: 'pejvak', prods: ['pos-aio-p15', 'receipt-xp80', 'drawer-m5'] }
};
function initSolution() {
  const key = q('biz') || 'supermarket';
  const b = ((window.PH && PH.solutions && PH.solutions[key]) || BIZ[key]) || BIZ.supermarket;
  const s = soft(b.soft) || PH.software[0];
  document.title = `راهکار ${b.n} | پژواک حساب`;
  $('#solHero').innerHTML = `<div class="container sw-hero-grid">
    <div><div class="breadcrumb-lite"><a href="${linkHome()}">خانه</a><span>/</span><a href="${linkHome('#solutions')}">راهکارها</a><span>/</span><span>${b.n}</span></div>
    <span class="eyebrow amber"><span class="dot"></span>راهکار تخصصی ${b.n}</span>
    <h1>راهکار یکپارچه ${b.n}</h1><p class="desc" style="margin-top:12px">${b.d}</p>
    <ul class="check-list" style="color:#D7E6EA">${b.needs.map(n => `<li style="color:#D7E6EA">${Icons.checkCircle}${n}</li>`).join('')}</ul>
    <div class="sw-hero-cta"><button class="btn btn-accent btn-lg" data-consult data-subject="راهکار ${b.n}"><span data-icon="headset"></span>مشاوره رایگان راه‌اندازی</button>${s ? `<a class="btn btn-outline-white btn-lg" href="${linkSoftware(s.id)}">نرم‌افزار پیشنهادی: ${s.name}</a>` : ''}</div></div>
    <div><img src="${b.img}" alt="${b.n}" style="border-radius:20px;box-shadow:var(--shadow-lg);height:380px;object-fit:cover;width:100%"></div></div>`;
  $('#solSteps').innerHTML = ((window.PH_WP && PH_WP.solSteps && PH_WP.solSteps.length ? PH_WP.solSteps : [['مشاوره و نیازسنجی', 'شناخت دقیق کسب‌وکار و پیشنهاد بهینه‌ترین ترکیب نرم‌افزار و تجهیزات.'], ['تأمین و آماده‌سازی', 'تأمین تجهیزات اصلی، نصب نرم‌افزار و تست کامل قبل از ارسال.'], ['نصب و آموزش', 'نصب در محل، ورود اطلاعات پایه و آموزش کامل پرسنل.'], ['پشتیبانی مستمر', 'پشتیبانی فنی، به‌روزرسانی و خدمات پس از فروش دائمی.']])).map((x, i) => `<div class="svc-card"><span class="step-n num">${faNum(i + 1)}</span><h3>${x[0]}</h3><p>${x[1]}</p></div>`).join('');
  $('#solProds').innerHTML = (b.prods || []).map(prod).filter(Boolean).map(productCard).join('');
  hydrateIcons(document);
}
function initHardware() {
  $('#hwCats').innerHTML = PH.cats.filter(c => c.id !== 'software').map(c => `<a class="cat-card reveal in" href="${linkCat(c.id)}"><span class="cat-ic"><span data-icon="${c.icon}"></span></span><div><h3>${c.name}</h3><p>${c.desc}</p></div><span class="go"><span data-icon="arrowLeft"></span></span></a>`).join('');
  $('#hwGrid').innerHTML = [...PH.products].sort((a, b) => (b.rating * b.reviews) - (a.rating * a.reviews)).slice(0, 6).map(productCard).join('');
  hydrateIcons(document);
}
function initContact() {
  $('#cForm').addEventListener('submit', e => {
    e.preventDefault();
    const f = e.target;
    if (window.PH_WP) {
      const ins = f.querySelectorAll('input');
      phAjax('ph_message', { name: (ins[0] && ins[0].value || '').trim(), phone: (ins[1] && ins[1].value || '').trim(), subject: f.querySelector('select').value, msg: f.querySelector('textarea').value.trim() }).then(res => { if (res && res.success) { showToast('پیام شما ارسال شد؛ به‌زودی پاسخ می‌دهیم', 'success'); f.reset(); } else showToast('خطا در ارسال پیام', 'error'); }).catch(() => showToast('خطا در ارسال پیام', 'error'));
    } else { showToast('پیام شما ارسال شد؛ به‌زودی پاسخ می‌دهیم', 'success'); f.reset(); }
  });
}
/* ---------- WordPress link rewriter (no-op on static site) ---------- */
function rewriteLinks(root) {
  if (!window.PH_WP || !PH_WP.links) return;
  const L = PH_WP.links;
  (root instanceof Element ? root : document).querySelectorAll('a[href]:not([data-rw])').forEach(a => {
    const h = a.getAttribute('href'); if (!h) return;
    let m, nu = null;
    if ((m = h.match(/^(?:\.\/)?product\.html\?id=([\w-]+)/))) { const u = (L.product || {})[m[1]]; if (u) nu = u; }
    else if ((m = h.match(/^(?:\.\/)?software\.html\?id=([\w-]+)/))) { const u = (L.software || {})[m[1]]; if (u) nu = u; }
    else if ((m = h.match(/^(?:\.\/)?article\.html\?id=([\w-]+)/))) { const u = (L.article || {})[m[1]]; if (u) nu = u; }
    else if ((m = h.match(/^(?:\.\/)?category\.html\?cat=([\w-]+)/))) nu = L.shop ? L.shop + '?pcat=' + m[1] : null;
    else if ((m = h.match(/^(?:\.\/)?solution\.html\?biz=([\w-]+)/))) nu = L.solution ? L.solution + '?biz=' + m[1] : null;
    else if ((m = h.match(/^(?:\.\/)?(shop|compare|cart|checkout|account|services|about|contact|blog|faq|hardware)\.html([?#].*)?$/))) { if (L[m[1]]) nu = L[m[1]] + (m[2] || ''); }
    else if (h === 'index.html' || h === './index.html') nu = PH_WP.home || '/';
    else if ((m = h.match(/^(?:\.\/)?index\.html(#[A-Za-z0-9_-]+)$/))) nu = (PH_WP.home || '/') + m[1];
    if (nu) { a.setAttribute('href', nu); a.setAttribute('data-rw', '1'); }
    else if (!/\.html([?#]|$)/.test(h)) a.setAttribute('data-rw', '1');
  });
}
function observeDynamic() {
  rewriteLinks(document);
  if (!window.MutationObserver) return;
  let t;
  new MutationObserver(() => { clearTimeout(t); t = setTimeout(() => { hydrateIcons(document); rewriteLinks(document); }, 40); }).observe(document.body, { childList: true, subtree: true });
}
/* ---------- boot ---------- */
document.addEventListener('DOMContentLoaded', () => {
  const page = document.body.dataset.page || 'home';
  const navMap = { home: 'home', shop: 'hardware', category: 'hardware', product: 'hardware', hardware: 'hardware', software: 'software', solution: 'solutions', services: 'services', about: 'about', contact: 'contact', blog: '', compare: '', cart: '', checkout: '', account: '', faq: '', article: '' };
  renderHeader(navMap[page] || '');
  renderFooter();
  renderShells();
  hydrateIcons(document);
  bindGlobal();
  if (window.PH_WP) observeDynamic();
  updateBadges(); renderMiniCart();
  ({ home: initHome, shop: initShop, category: initShop, product: initProduct, software: initSoftware, compare: initCompare, cart: initCart, checkout: initCheckout, account: initAccount, faq: initFaq, blog: initBlog, article: initArticle, solution: initSolution, hardware: initHardware, contact: initContact })[page]?.();
  /* open first acc on faq/product */
  setTimeout(() => { const f = $('.acc-item .acc-btn'); }, 0);
});
