<?php
/**
 * Template Name: حساب کاربری
 * Pejvak Hesab Theme (generated from static design)
 */
defined('ABSPATH') || exit;
get_header();
?>
<main>
<div class="page-hero"><div class="container"><div class="breadcrumb-lite"><a href="<?php echo esc_url(home_url('/')); ?>">خانه</a><span>/</span><span><?php the_title(); ?></span></div><h1><?php the_title(); ?></h1><p><?php echo esc_html(get_the_excerpt() ?: 'سفارش‌ها، علاقه‌مندی‌ها و اطلاعات خود را مدیریت کنید'); ?></p></div></div>
<div class="section"><div class="container">
  <div id="accGuest"<?php if (is_user_logged_in()) echo ' style="display:none"'; ?>><div class="auth-wrap">
    <?php
    /* لینک بازنشانی رمز (از ایمیل فراموشی) به همین صفحه با rp_key/rp_login برمی‌گردد */
    $ph_rp_key = isset($_GET['rp_key']) ? sanitize_text_field(wp_unslash($_GET['rp_key'])) : '';
    $ph_rp_login = isset($_GET['rp_login']) ? sanitize_user(wp_unslash($_GET['rp_login']), true) : '';
    $ph_rp = ($ph_rp_key !== '' && $ph_rp_login !== '');
    $ph_sms_on = function_exists('ph_sms_enabled') && ph_sms_enabled();
    $ph_can_reg = (bool) get_option('users_can_register');
    ?>
    <h2>ورود و عضویت</h2><p>برای مشاهده سفارش‌ها و مدیریت حساب خود وارد شوید یا حساب جدید بسازید.</p>
    <style>#accGuest .login-username label,#accGuest .login-password label{display:block;font-size:13px;font-weight:700;margin-bottom:6px}#accGuest .login-username input,#accGuest .login-password input{width:100%;padding:12px 14px;border:1.5px solid var(--line);border-radius:12px;font-family:inherit;font-size:14px}#accGuest #wp-submit{width:100%;padding:14px;background:var(--brand);color:#fff;border:none;border-radius:12px;font-family:inherit;font-size:15px;font-weight:800;cursor:pointer}#accGuest #wp-submit:hover{background:var(--brand-strong)}.auth-tabs{display:flex;gap:8px;margin:18px 0 22px;background:var(--bg-soft);padding:6px;border-radius:14px}.auth-tabs button{flex:1;padding:11px;border:0;background:transparent;border-radius:10px;font-family:inherit;font-size:14px;font-weight:800;color:var(--muted);cursor:pointer}.auth-tabs button.active{background:#fff;color:var(--brand-strong);box-shadow:0 2px 8px rgba(7,36,45,.08)}</style>
    <div class="auth-tabs" id="authTabs"<?php if ($ph_rp) echo ' style="display:none"'; ?>>
      <button type="button" id="authTabLogin" class="active">ورود</button>
      <?php if ($ph_can_reg) : ?><button type="button" id="authTabReg">عضویت</button><?php endif; ?>
    </div>
    <!-- ورود -->
    <div id="authLoginPane"<?php if ($ph_rp) echo ' style="display:none"'; ?>>
    <?php if ($ph_sms_on) : ?>
    <div id="otpBox">
      <div id="otpStep1">
        <div class="field"><label>شماره موبایل</label><input class="input num" id="otpMobile" inputmode="numeric" dir="ltr" placeholder="09123456789" maxlength="11"></div>
        <button class="btn btn-primary btn-block" id="otpSend" type="button">ارسال کد تأیید</button>
      </div>
      <div id="otpStep2" style="display:none">
        <p class="form-hint">کد ۵ رقمی ارسال‌شده به <b class="num" id="otpTo"></b> را وارد کنید.</p>
        <div class="otp-inputs" id="otpInputs"><input inputmode="numeric" maxlength="1" aria-label="رقم ۱"><input inputmode="numeric" maxlength="1" aria-label="رقم ۲"><input inputmode="numeric" maxlength="1" aria-label="رقم ۳"><input inputmode="numeric" maxlength="1" aria-label="رقم ۴"><input inputmode="numeric" maxlength="1" aria-label="رقم ۵"></div>
        <button class="btn btn-primary btn-block" id="otpVerify" type="button">ورود</button>
        <p class="form-hint" style="text-align:center;margin-top:10px"><button type="button" class="link-more" id="otpResend" disabled>ارسال مجدد</button> · <button type="button" class="link-more" id="otpEdit">ویرایش شماره</button></p>
      </div>
      <p class="form-hint" style="text-align:center;margin:14px 0 0"><button type="button" class="link-more" id="otpToPw">ورود با رمز عبور</button></p>
    </div>
    <?php endif; ?>
    <div id="pwBox"<?php if ($ph_sms_on) echo ' style="display:none"'; ?>>
    <?php wp_login_form(['redirect' => get_permalink(), 'label_username' => 'نام کاربری یا ایمیل', 'label_password' => 'رمز عبور', 'label_remember' => 'مرا به خاطر بسپار', 'label_log_in' => 'ورود']); ?>
    <p class="form-hint" style="text-align:center;margin-top:14px"><button type="button" class="link-more" id="gotoForgot">رمز عبورم را فراموش کرده‌ام</button><?php if ($ph_sms_on) : ?> · <button type="button" class="link-more" id="pwToOtp">ورود با کد پیامکی</button><?php endif; ?></p>
    </div>
    </div>
    <!-- عضویت (داخل قالب) -->
    <?php if ($ph_can_reg) : ?>
    <div id="authRegPane" style="display:none">
      <form id="regForm">
        <div class="field"><label>نام و نام خانوادگی <span class="req">*</span></label><input class="input" id="regName" placeholder="مثلاً علی محمدی"></div>
        <div class="form-row"><div class="field"><label>ایمیل <span class="req">*</span></label><input class="input" id="regEmail" type="email" dir="ltr" placeholder="you@mail.com"></div><div class="field"><label>شماره موبایل</label><input class="input num" id="regMobile" inputmode="tel" dir="ltr" placeholder="09123456789 (اختیاری)"></div></div>
        <div class="field"><label>رمز عبور <span class="req">*</span></label><input class="input" id="regPass" type="password" dir="ltr" placeholder="حداقل ۶ کاراکتر"></div>
        <button class="btn btn-primary btn-block" type="submit">ایجاد حساب کاربری</button>
        <?php if ($ph_sms_on) : ?><p class="form-hint" style="text-align:center;margin-top:12px">میان‌بر: در تب «ورود» با شماره موبایل و کد پیامکی، حساب شما به‌صورت خودکار ساخته می‌شود.</p><?php endif; ?>
      </form>
    </div>
    <?php endif; ?>
    <!-- فراموشی رمز عبور -->
    <div id="authForgotPane" style="display:none">
      <form id="forgotForm">
        <p class="form-hint">ایمیل یا نام کاربری حساب خود را وارد کنید؛ لینک تنظیم رمز جدید برای شما ارسال می‌شود.</p>
        <div class="field"><label>ایمیل یا نام کاربری</label><input class="input" id="fpLogin" dir="ltr" placeholder="you@mail.com"></div>
        <button class="btn btn-primary btn-block" type="submit">ارسال لینک بازنشانی</button>
        <p class="form-hint" style="text-align:center;margin-top:12px"><button type="button" class="link-more" id="backToLogin1">بازگشت به ورود</button></p>
      </form>
      <?php if ($ph_sms_on) : ?><p class="form-hint" style="text-align:center;margin-top:4px">نکته: اگر شماره موبایل شما در سایت ثبت است، بدون رمز عبور هم می‌توانید با «ورود با کد پیامکی» وارد شوید.</p><?php endif; ?>
    </div>
    <!-- رمز جدید (از لینک ایمیل) -->
    <?php if ($ph_rp) : ?>
    <div id="authResetPane">
      <form id="resetForm">
        <p class="form-hint">برای حساب <b dir="ltr"><?php echo esc_html($ph_rp_login); ?></b> رمز عبور جدید تعیین کنید.</p>
        <input type="hidden" id="rpKey" value="<?php echo esc_attr($ph_rp_key); ?>">
        <input type="hidden" id="rpLogin" value="<?php echo esc_attr($ph_rp_login); ?>">
        <div class="field"><label>رمز عبور جدید</label><input class="input" id="rpPass" type="password" dir="ltr" placeholder="حداقل ۶ کاراکتر"></div>
        <div class="field"><label>تکرار رمز عبور جدید</label><input class="input" id="rpPass2" type="password" dir="ltr"></div>
        <button class="btn btn-primary btn-block" type="submit">ذخیره رمز جدید</button>
        <p class="form-hint" style="text-align:center;margin-top:12px"><button type="button" class="link-more" id="backToLogin2">بازگشت به ورود</button></p>
      </form>
    </div>
    <?php endif; ?>
  </div></div>
  <div id="accMain" class="acc-layout"<?php if (!is_user_logged_in()) echo ' style="display:none"'; ?>>
    <aside class="acc-side"><div class="acc-user"><div class="ava" id="accAva">ک</div><b id="accUserName"><?php echo is_user_logged_in() ? esc_html(wp_get_current_user()->display_name) : 'کاربر'; ?></b><span id="accUserPhone" class="num"><?php echo is_user_logged_in() ? esc_html(get_user_meta(get_current_user_id(), 'ph_phone', true)) : ''; ?></span></div>
      <nav class="acc-nav"><button class="active" data-acc="dash"><span data-icon="grid"></span>داشبورد</button><button data-acc="orders"><span data-icon="package"></span>سفارش‌ها</button><button data-acc="wish"><span data-icon="heart"></span>علاقه‌مندی‌ها</button><button data-acc="addr"><span data-icon="pin"></span>آدرس‌ها</button><button data-acc="settings"><span data-icon="edit"></span>تنظیمات</button><button class="logout" id="btnLogout"><span data-icon="logout"></span>خروج از حساب</button></nav></aside>
    <div>
      <div class="acc-panel active" id="acc-dash"><h3>داشبورد</h3><div class="stat-cards"><div class="stat-card"><b class="num" id="statOrders">۰</b><span>سفارش‌ها</span></div><div class="stat-card"><b class="num" id="statWish">۰</b><span>علاقه‌مندی‌ها</span></div><div class="stat-card"><b class="num" id="statTotal">۰</b><span>جمع خرید (تومان)</span></div><div class="stat-card"><b style="color:var(--success)">فعال</b><span>گارانتی و پشتیبانی</span></div></div><p style="font-size:14px">به پژواک حساب خوش آمدید. از منوی کنار می‌توانید سفارش‌ها را پیگیری و علاقه‌مندی‌های خود را ببینید.</p></div>
      <div class="acc-panel" id="acc-orders"><h3>سفارش‌های من</h3><div id="ordersTable"></div></div>
      <div class="acc-panel" id="acc-wish"><h3>علاقه‌مندی‌ها</h3><div class="p-grid c2" id="wishGrid"></div></div>
      <div class="acc-panel" id="acc-addr"><h3>آدرس‌های من</h3><div id="addrList" style="display:grid;gap:10px;margin-bottom:18px"></div><form id="addrForm"><div class="form-row"><div class="field"><label>عنوان آدرس</label><input class="input" id="addrTitle" placeholder="مثلاً فروشگاه مرکزی"></div><div class="field"><label>شهر</label><input class="input" id="addrCity"></div></div><div class="field"><label>آدرس کامل</label><textarea class="input" id="addrText" style="min-height:70px"></textarea></div><button class="btn btn-primary">ذخیره آدرس</button></form></div>
      <div class="acc-panel" id="acc-settings"><h3>تنظیمات حساب</h3><form id="passForm"><div class="form-row"><div class="field"><label>نام و نام خانوادگی</label><input class="input" id="profName" placeholder="نام شما"></div><div class="field"><label>ایمیل</label><input class="input" id="profEmail" type="email" dir="ltr" placeholder="you@mail.com"></div></div><div class="field"><label>رمز عبور جدید</label><input class="input" id="profPass" type="password" placeholder="••••••••"></div><button class="btn btn-primary">ذخیره تغییرات</button></form></div>
    </div>
  </div>
</div></div>
</main>
<?php get_footer();
