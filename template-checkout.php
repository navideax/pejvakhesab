<?php
/**
 * Template Name: تسویه حساب
 * Pejvak Hesab Theme (generated from static design)
 */
defined('ABSPATH') || exit;
get_header();
?>
<main>
<nav class="breadcrumb"><div class="container"><ol><li><a href="<?php echo esc_url(home_url('/')); ?>">خانه</a></li><li><a href="<?php echo esc_url(ph_url('cart')); ?>">سبد خرید</a></li><li><span aria-current="page">تسویه حساب</span></li></ol></div></nav>
<div class="section" style="padding-top:40px"><div class="container" id="coMain">
  <div class="steps" id="coSteps"><div class="step active"><span class="n">۱</span>اطلاعات ارسال</div><div class="step-line"></div><div class="step"><span class="n">۲</span>ارسال و پرداخت</div><div class="step-line"></div><div class="step"><span class="n">۳</span>بازبینی و ثبت</div></div>
  <div class="cart-layout">
    <div>
      <div class="co-step"><div class="co-box"><h3><span class="n">۱</span>اطلاعات تحویل‌گیرنده</h3>
        <div class="form-row"><div class="field"><label>نام و نام خانوادگی <span class="req">*</span></label><input class="input" id="fName" placeholder="مثلاً علی محمدی"><div class="form-error">نام را کامل وارد کنید</div></div><div class="field"><label>شماره موبایل <span class="req">*</span></label><input class="input num" id="fPhone" inputmode="tel" placeholder="۰۹۱۲۰۰۰۰۰۰۰"><div class="form-error">شماره موبایل معتبر وارد کنید</div></div></div>
        <div class="form-row"><div class="field"><label>استان <span class="req">*</span></label><select class="input" id="fProv"><?php $ph_pr = ph_lines(ph_opt('ph_provinces', '')); if (!$ph_pr) $ph_pr = ['تهران']; foreach ($ph_pr as $p) echo '<option>' . esc_html($p) . '</option>'; ?></select></div><div class="field"><label>شهر <span class="req">*</span></label><input class="input" id="fCity" placeholder="مثلاً تهران"><div class="form-error">شهر را وارد کنید</div></div></div>
        <div class="field"><label>آدرس دقیق <span class="req">*</span></label><textarea class="input" id="fAddr" style="min-height:70px" placeholder="خیابان، کوچه، پلاک، واحد..."></textarea><div class="form-error">آدرس را کامل وارد کنید</div></div>
        <div class="field"><label>کد پستی</label><input class="input num" id="fPostcode" inputmode="numeric" placeholder="۱۰ رقمی (اختیاری)"></div>
        <div class="field"><label>توضیحات سفارش</label><textarea class="input" id="fNote" style="min-height:60px" placeholder="مثلاً هماهنگی نصب، ساعت تحویل... (اختیاری)"></textarea></div>
      </div></div>
      <div class="co-step"><div class="co-box"><h3><span class="n">۲</span>روش ارسال</h3>
        <div class="ship-grid"><?php $ph_sh = ph_ship_config(); $ph_fm = $ph_sh['freeOver'] >= 1000000 ? strtr((string) ($ph_sh['freeOver'] / 1000000), '0123456789', '۰۱۲۳۴۵۶۷۸۹') . ' میلیون' : strtr(number_format($ph_sh['freeOver']), '0123456789,', '۰۱۲۳۴۵۶۷۸۹٬') . ' تومان'; ?>
<label class="radio-card active"><input type="radio" name="ship" checked data-ship="std" data-price="<?php echo $ph_sh['std']; ?>" data-free="1"><span><b><?php echo esc_html(ph_opt('ph_ship_std_t', 'ارسال عادی (۲ تا ۴ روز)')); ?></b><br><small style="color:var(--muted)"><?php echo esc_html(ph_opt('ph_ship_std_d', 'بیمه + بسته‌بندی ایمن')); ?> · <span class="num"><?php echo esc_html(strtr(number_format($ph_sh['std']), '0123456789,', '۰۱۲۳۴۵۶۷۸۹٬')); ?> تومان</span><?php if ($ph_sh['freeOver'] > 0) echo ' (بالای ' . esc_html($ph_fm) . ' رایگان)'; ?></small></span></label>
<label class="radio-card"><input type="radio" name="ship" data-ship="exp" data-price="<?php echo $ph_sh['exp']; ?>"><span><b><?php echo esc_html(ph_opt('ph_ship_exp_t', 'ارسال فوری تهران (۲۴ ساعته)')); ?></b><br><small style="color:var(--muted)"><?php echo esc_html(ph_opt('ph_ship_exp_d', 'فقط تهران و کرج')); ?> · <span class="num"><?php echo esc_html(strtr(number_format($ph_sh['exp']), '0123456789,', '۰۱۲۳۴۵۶۷۸۹٬')); ?> تومان</span></small></span></label>
<label class="radio-card"><input type="radio" name="ship" data-ship="pick" data-price="0"><span><b><?php echo esc_html(ph_opt('ph_ship_pick_t', 'تحویل حضوری + نصب')); ?></b><br><small style="color:var(--muted)"><?php echo esc_html(ph_opt('ph_ship_pick_d', 'هماهنگی نصب در محل · رایگان')); ?></small></span></label>
</div></div>
      <div class="co-box"><h3><span class="n">۳</span>روش پرداخت</h3>
        <div class="pay-methods"><label class="radio-card active"><input type="radio" name="pay" value="online" checked><span><b>پرداخت آنلاین</b><br><small style="color:var(--muted)">درگاه امن بانکی</small></span></label><label class="radio-card"><input type="radio" name="pay" value="card"><span><b>کارت به کارت</b><br><small style="color:var(--muted)">هماهنگی تلفنی</small></span></label><label class="radio-card"><input type="radio" name="pay" value="installment"><span><b>خرید اقساطی</b><br><small style="color:var(--muted)">با پیش‌پرداخت و چک</small></span></label></div></div></div>
      <div class="co-step"><div class="co-box"><h3><span class="n">۴</span>بازبینی سفارش</h3><div id="coReview"></div><label class="check" style="margin-top:14px"><input type="checkbox" checked>قوانین و مقررات خرید و گارانتی را مطالعه کرده و می‌پذیرم.</label></div></div>
      <div style="display:flex;gap:12px;justify-content:space-between;margin-top:6px"><button class="btn btn-ghost" id="coBack">مرحله قبل</button><button class="btn btn-primary btn-lg" id="coNext">ادامه</button></div>
    </div>
    <aside class="summary"><h3>سفارش شما</h3><div id="coItems" style="max-height:300px;overflow-y:auto;margin-bottom:10px"></div><div id="coTotal"></div><div class="pd-trust" style="grid-template-columns:1fr 1fr;margin-top:16px"><div><span data-icon="shield"></span>پرداخت امن</div><div><span data-icon="truck"></span>ارسال بیمه‌شده</div></div></aside>
  </div>
</div></div>
</main>
<?php get_footer();
