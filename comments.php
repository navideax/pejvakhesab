<?php
defined('ABSPATH') || exit;
if (post_password_required()) return;
if (!function_exists('ph_comment_cb')) {
    function ph_comment_cb($comment, $args, $depth) {
        $initial = mb_substr(get_comment_author($comment), 0, 1);
        ?>
        <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
          <div class="review">
            <div class="review-head">
              <span class="review-ava"><?php echo esc_html($initial); ?></span>
              <div><b><?php comment_author(); ?></b><span><?php echo esc_html(ph_fa_date(get_comment_date('U'))); ?></span></div>
            </div>
            <p><?php comment_text(); ?></p>
            <?php comment_reply_link(array_merge($args, ['depth' => $depth, 'reply_text' => 'پاسخ'])); ?>
          </div>
        <?php
    }
}
?>
<div class="co-box" id="comments" style="margin-top:28px">
  <h3>دیدگاه‌ها (<?php echo esc_html(get_comments_number()); ?>)</h3>
  <?php if (have_comments()) : ?>
  <ol class="comment-list"><?php wp_list_comments(['style' => 'ol', 'short_ping' => true, 'callback' => 'ph_comment_cb']); ?></ol>
  <?php endif; ?>
  <?php
  comment_form([
      'title_reply' => 'ثبت دیدگاه',
      'comment_field' => '<div class="field"><label>دیدگاه شما</label><textarea class="input" name="comment" required></textarea></div>',
      'fields' => [
          'author' => '<div class="form-row"><div class="field"><label>نام</label><input class="input" name="author" required></div>',
          'email' => '<div class="field"><label>ایمیل</label><input class="input" name="email" type="email" dir="ltr"></div></div>',
      ],
      'class_submit' => 'btn btn-primary',
      'label_submit' => 'ثبت دیدگاه',
  ]);
  ?>
</div>
