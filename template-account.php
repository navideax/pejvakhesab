<?php
/**
 * Template Name: حساب کاربری
 * Pejvak Hesab Theme (generated from static design)
 */
defined('ABSPATH') || exit;
get_header();
?>
<main>
<div class="page-hero"><div class="container"><div class="breadcrumb-lite"><a href="<?php echo home_url('/'); ?>">خانه</a><span>/</span><span><?php the_title(); ?></span></div><h1><?php the_title(); ?></h1><p><?php echo esc_html(get_the_excerpt() ?: 'سفارش‌ها، علاقه‌مندی‌ها و اطلاعات خود را مدیریت کنید'); ?></p></div></div>
<div class="section"><div class="container">
  <div id="accGuest"<?php if (is_user_logged_in()) echo ' style="display:none"'; ?>><div class="auth-wrap">
    <h2>ورود به حساب کاربری</h2><p>برای مشاهده سفارش‌ها و مدیریت حساب خود وارد شوید.</p>
    <?php if (function_exists('ph_sms_enabled') && ph_sms_enabled()) : ?>
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
    <style>#accGuest .login-username label,#accGuest .login-password label{display:block;font-size:13px;font-weight:700;margin-bottom:6px}#accGuest .login-username input,#accGuest .login-password input{width:100%;padding:12px 14px;border:1.5px solid var(--line);border-radius:12px;font-family:inherit;font-size:14px}#accGuest #wp-submit{width:100%;padding:14px;background:var(--brand);color:#fff;border:none;border-radius:12px;font-family:inherit;font-size:15px;font-weight:800;cursor:pointer}#accGuest #wp-submit:hover{background:var(--brand-strong)}</style>
    <div id="pwBox"<?php if (function_exists('ph_sms_enabled') && ph_sms_enabled()) echo ' style="display:none"'; ?>>
    <?php wp_login_form(['redirect' => get_permalink(), 'label_username' => 'نام کاربری یا ایمیل', 'label_password' => 'رمز عبور', 'label_remember' => 'مرا به خاطر بسپار', 'label_log_in' => 'ورود']); ?>
    <?php if (get_option('users_can_register')) : ?><p class="form-hint" style="text-align:center;margin-top:14px">حساب ندارید؟ <a href="<?php echo esc_url(wp_registration_url()); ?>">ثبت‌نام کنید</a></p><?php endif; ?>
    <?php if (function_exists('ph_sms_enabled') && ph_sms_enabled()) : ?><p class="form-hint" style="text-align:center;margin-top:10px"><button type="button" class="link-more" id="pwToOtp">ورود با کد پیامکی</button></p><?php endif; ?>
    </div>
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
