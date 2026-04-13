<?php
/**
 * Carevee Pharmacy — functions.php
 * Updated: April 2026
 */

// Boost local performance for XAMPP development
if (defined('WP_DEBUG') && WP_DEBUG) {
    @set_time_limit(300);
}

// ─── SMTP CONFIGURATION ───────────────────────
add_action('phpmailer_init', function($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'cs2.rcnoc.com';
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 465;
    $phpmailer->SMTPSecure = 'ssl';
    $phpmailer->Username   = 'sales@careveekenya.co.ke';
    $phpmailer->Password   = 'Carevee@2026';
    $phpmailer->From       = 'sales@careveekenya.co.ke';
    $phpmailer->FromName   = get_bloginfo('name') . ' Website';
    $phpmailer->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
        ],
    ];
});

// ─── THEME SETUP ──────────────────────────────
function medicare_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('html5', ['search-form','comment-form','gallery','caption','style','script']);
    add_theme_support('custom-logo', ['width'=>46,'height'=>46,'flex-square'=>true]);
    register_nav_menus(['primary' => 'Primary Menu']);
}
add_action('after_setup_theme', 'medicare_setup');

// ─── ENQUEUE ──────────────────────────────────
function medicare_enqueue() {
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;500;600;700&display=swap', [], null);
    wp_enqueue_style('medicare-style', get_stylesheet_uri(), [], '1.0.2');
    wp_enqueue_script('medicare-main', get_template_directory_uri() . '/assets/js/main.js', ['jquery'], '1.0.2', true);

    $shop_url = home_url('/shop');
    if ( class_exists('WooCommerce') && function_exists('wc_get_page_id') ) {
        $shop_page_id = wc_get_page_id('shop');
        if ( $shop_page_id > 0 ) {
            $shop_url = get_permalink($shop_page_id);
        }
    }

    wp_localize_script('medicare-main', 'medicareData', [
        'ajaxUrl'    => admin_url('admin-ajax.php'),
        'nonce'      => wp_create_nonce('medicare_nonce'),
        'orderNonce' => wp_create_nonce('carevee_order_nonce'),
        'waNumber'   => esc_js(medicare_wa()),
        'shopUrl'    => esc_url($shop_url),
    ]);
}
add_action('wp_enqueue_scripts', 'medicare_enqueue');

// ─── HELPERS ──────────────────────────────────
function medicare_wa()      { return get_option('medicare_wa',      '254790007616'); }
function medicare_phone()   { return get_option('medicare_phone',   '+254 790 007 616'); }
function medicare_address() { return get_option('medicare_address', 'Nairobi, Kenya'); }
function medicare_tagline() { return get_option('medicare_tagline', 'Trusted Care, Delivered Daily'); }
function medicare_email()   { return get_option('medicare_email',   'sales@careveekenya.co.ke'); }

// ─── FORCE PAGE TEMPLATES BY SLUG ─────────────
function medicare_page_template( $template ) {
    if ( is_page() ) {
        $slug   = get_post_field('post_name', get_queried_object_id());
        $custom = get_template_directory() . '/page-' . $slug . '.php';
        if ( file_exists($custom) ) return $custom;
    }
    return $template;
}
add_filter('template_include', 'medicare_page_template', 99);

// ─── WA URL HELPER ────────────────────────────
function medicare_get_wa_url( $product_id ) {
    $product = wc_get_product($product_id);
    if ( !$product ) return '';
    $wa    = medicare_wa();
    $name  = $product->get_name();
    $price = strip_tags($product->get_price_html());
    $url   = get_permalink($product_id);
    $msg   = urlencode("Hello " . get_bloginfo('name') . "! I'd like to order: *{$name}* Price: {$price} Link: {$url} Please confirm availability. Thank you!");
    return "https://wa.me/" . esc_attr($wa) . "?text=" . $msg;
}

// ─── WC WRAPPERS ──────────────────────────────
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10);
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

add_action('woocommerce_before_main_content', function() { echo '<div class="wc-outer">'; }, 10);
add_action('woocommerce_after_main_content',  function() { echo '</div>'; }, 10);

// ─── WHATSAPP ORDER BUTTON (Single Product) ───
function medicare_wa_button() {
    if ( !class_exists('WooCommerce') ) return;
    global $product;
    if ( !$product || !is_a($product, 'WC_Product') ) return;

    $product_id = $product->get_id();
    $wa_url     = medicare_get_wa_url($product_id);
    ?>
    <button
        type="button"
        class="wc-wa-btn carevee-wa-order-btn"
        data-product-id="<?php echo esc_attr($product_id); ?>"
        data-wa-url="<?php echo esc_url($wa_url); ?>"
        data-nonce="<?php echo esc_attr(wp_create_nonce('carevee_order_nonce')); ?>"
    >
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
        Buy Via WhatsApp
    </button>

    <script>
    (function($){
        $(document).on('click', '.carevee-wa-order-btn', function(e){
            e.preventDefault();
            var $btn      = $(this);
            var productId = $btn.data('product-id');
            var waUrl     = $btn.data('wa-url');
            var nonce     = $btn.data('nonce');
            var qty       = parseInt($('input.qty').val()) || 1;

            $btn.prop('disabled', true).text('Processing…');

            $.ajax({
                url  : (typeof medicareData !== 'undefined' ? medicareData.ajaxUrl : '/wp-admin/admin-ajax.php'),
                type : 'POST',
                data : {
                    action             : 'carevee_wa_order',
                    carevee_order_nonce: nonce,
                    product_id         : productId,
                    qty                : qty,
                    phone              : '',
                    customer_name      : 'WhatsApp Customer',
                },
                complete: function(){
                    $btn.prop('disabled', false).html(
                        '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg> Buy Via WhatsApp'
                    );
                    window.open(waUrl, '_blank');
                }
            });
        });
    })(jQuery);
    </script>
    <?php
}
add_action('woocommerce_single_product_summary', 'medicare_wa_button', 35);

// ─── CASH ON DELIVERY ONLY ────────────────────
add_filter('woocommerce_payment_gateways', function($gateways) {
    if ( isset($gateways['cod']) ) {
        return ['cod' => $gateways['cod']];
    }
    return $gateways;
});

// ─── PRICE RANGE FILTER ───────────────────────
add_action('pre_get_posts', function($query) {
    if ( is_admin() || !$query->is_main_query() ) return;
    if ( !function_exists('is_shop') ) return;
    if ( !is_shop() && !is_product_category() && !is_search() ) return;
    if ( !isset($_GET['min_price']) && !isset($_GET['max_price']) ) return;

    $min = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
    $max = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 999999999;

    $existing   = (array) $query->get('meta_query');
    $existing[] = [
        'key'     => '_price',
        'value'   => [$min, $max],
        'compare' => 'BETWEEN',
        'type'    => 'NUMERIC',
    ];
    $query->set('meta_query', $existing);
    $query->set('post_type', 'product');
});

// ─── CAREVEE WHATSAPP ORDER: CART CLEAR ───────
add_action('template_redirect', function() {
    if ( ! isset($_GET['carevee_order_sent']) || $_GET['carevee_order_sent'] !== '1' ) return;
    if ( ! function_exists('WC') || ! WC()->cart ) return;

    WC()->cart->empty_cart();

    if ( WC()->session ) {
        WC()->session->set('cart', []);
        WC()->session->set('cart_totals', null);
    }

    if ( function_exists('wc_clear_notices') ) {
        wc_clear_notices();
    }

    $shop_url = function_exists('wc_get_page_id')
        ? get_permalink(wc_get_page_id('shop'))
        : home_url('/shop');

    wp_redirect(esc_url_raw($shop_url));
    exit;
});

// ─── AJAX: FILTER PRODUCTS BY CATEGORY ───────
function medicare_filter_products() {
    if ( !class_exists('WooCommerce') ) wp_send_json_error('WooCommerce not active');

    $cat  = sanitize_text_field($_POST['cat'] ?? '');
    $args = [
        'post_type'      => 'product',
        'posts_per_page' => 8,
        'post_status'    => 'publish',
    ];
    if ( $cat ) {
        $args['tax_query'] = [
            ['taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $cat]
        ];
    }

    $q = new WP_Query($args);
    ob_start();

    if ( $q->have_posts() ) {
        while ( $q->have_posts() ) {
            $q->the_post();
            $product_id = get_the_ID();
            $product    = wc_get_product($product_id);
            if ( !$product ) continue;

            $sale      = $product->is_on_sale();
            $is_new    = (time() - strtotime($product->get_date_created())) < (30 * DAY_IN_SECONDS);
            $price_cur = number_format((float)$product->get_price(), 2);
            $price_reg = $product->get_regular_price();
            $img       = get_the_post_thumbnail_url($product_id, 'woocommerce_thumbnail');
            $cart_url  = $product->is_type('simple') ? '?add-to-cart=' . $product_id : get_permalink($product_id);

            $cats    = get_the_terms($product_id, 'product_cat');
            $cat_n   = ($cats && !is_wp_error($cats)) ? $cats[0]->name : '';
            $cat_lnk = ($cats && !is_wp_error($cats)) ? get_term_link($cats[0]) : '#';
            $brands  = get_the_terms($product_id, 'product_brand');
            if ( !$brands || is_wp_error($brands) ) $brands = get_the_terms($product_id, 'pa_brand');
            $brand_n = ($brands && !is_wp_error($brands)) ? $brands[0]->name : '';

            $wa_product_url = medicare_get_wa_url($product_id);
            $wa_nonce       = wp_create_nonce('carevee_order_nonce');
            ?>
            <div class="p-card">
              <a href="<?php the_permalink(); ?>" class="p-img-link">
                <div class="p-img">
                  <?php if ($img): ?>
                    <img src="<?php echo esc_url($img); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
                  <?php else: ?>
                    <svg style="width:48px;height:48px;opacity:.2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M10.5 3.5a6 6 0 018 8l-8.5 8.5a6 6 0 01-8-8l8.5-8.5z"/></svg>
                  <?php endif; ?>
                  <button class="p-wish" aria-label="Wishlist" onclick="event.preventDefault();event.stopPropagation();">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                  </button>
                  <?php if ($sale && $price_reg): ?>
                    <div class="p-badge-sale">-<?php echo round((($price_reg - $product->get_price()) / $price_reg) * 100); ?>% OFF</div>
                  <?php elseif ($is_new): ?>
                    <div class="p-badge-new">NEW</div>
                  <?php endif; ?>
                </div>
              </a>
              <div class="p-body">
                <?php if ($cat_n || $brand_n): ?>
                  <div class="p-card-meta">
                    <?php if ($cat_n): ?><a href="<?php echo esc_url($cat_lnk); ?>" class="p-card-cat"><?php echo esc_html($cat_n); ?></a><?php endif; ?>
                    <?php if ($brand_n): ?><span class="p-card-brand"><?php echo esc_html($brand_n); ?></span><?php endif; ?>
                  </div>
                <?php endif; ?>
                <div class="p-name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
                <div class="p-price-wrap">
                  <?php if ($sale && $price_reg): ?>
                    <div class="p-price-old">KES <?php echo number_format($price_reg, 2); ?></div>
                  <?php endif; ?>
                  <div class="p-price-cur">KES <?php echo $price_cur; ?></div>
                </div>
                <div class="p-btns">
                  <a href="<?php echo esc_url($cart_url); ?>" class="p-btn-cart"
                     <?php if ($product->is_type('simple')): ?>data-product_id="<?php echo $product_id; ?>" data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" rel="nofollow"<?php endif; ?>>
                    <span class="p-btn-ico">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 001.97 1.61h9.72a2 2 0 001.97-1.67L23 6H6"/></svg>
                    </span>
                    Add to Cart
                  </a>
                  <button
                    type="button"
                    class="p-btn-wa carevee-wa-order-btn"
                    data-product-id="<?php echo esc_attr($product_id); ?>"
                    data-wa-url="<?php echo esc_url($wa_product_url); ?>"
                    data-nonce="<?php echo esc_attr($wa_nonce); ?>"
                  >
                    <span class="p-btn-ico">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </span>
                    Buy Via WhatsApp
                  </button>
                </div>
              </div>
            </div>
            <?php
        }
        wp_reset_postdata();
    }

    wp_send_json_success(ob_get_clean());
}
add_action('wp_ajax_medicare_filter_products',        'medicare_filter_products');
add_action('wp_ajax_nopriv_medicare_filter_products', 'medicare_filter_products');

// ─── GLOBAL JS FOR WA ORDER BUTTON ────────────
add_action('wp_footer', function() {
    if ( ! is_product() && ! is_shop() && ! is_product_category() && ! is_search() ) return;
    ?>
    <script>
    (function($){
        if (typeof careveeWaBound !== 'undefined') return;
        window.careveeWaBound = true;

        $(document).on('click', '.carevee-wa-order-btn', function(e){
            e.preventDefault();
            var $btn      = $(this);
            var productId = $btn.data('product-id');
            var waUrl     = $btn.data('wa-url');
            var nonce     = $btn.data('nonce');
            var qty       = 1;

            var $qtyInput = $('input.qty');
            if ($qtyInput.length) qty = parseInt($qtyInput.val()) || 1;

            $btn.prop('disabled', true);

            $.ajax({
                url  : (typeof medicareData !== 'undefined' ? medicareData.ajaxUrl : '/wp-admin/admin-ajax.php'),
                type : 'POST',
                data : {
                    action             : 'carevee_wa_order',
                    carevee_order_nonce: nonce,
                    product_id         : productId,
                    qty                : qty,
                    phone              : '',
                    customer_name      : 'WhatsApp Customer',
                },
                complete: function(){
                    $btn.prop('disabled', false);
                    window.open(waUrl, '_blank');
                }
            });
        });
    })(jQuery);
    </script>
    <?php
});

// ─── CONTACT FORM AJAX ────────────────────────
function medicare_contact() {
    ob_clean();

    if ( !wp_verify_nonce($_POST['nonce'] ?? '', 'medicare_nonce') ) {
        wp_send_json_error(['msg' => 'Security check failed. Please refresh and try again.']);
    }

    $name  = sanitize_text_field($_POST['contact_name']    ?? '');
    $email = sanitize_email($_POST['contact_email']        ?? '');
    $phone = sanitize_text_field($_POST['contact_phone']   ?? '');
    $dept  = sanitize_text_field($_POST['contact_dept']    ?? '');
    $msg   = sanitize_textarea_field($_POST['contact_msg'] ?? '');

    if ( empty($name) || strlen($name) < 2 )
        wp_send_json_error(['msg' => 'Please enter your full name.']);
    if ( empty($email) || !is_email($email) )
        wp_send_json_error(['msg' => 'Please enter a valid email address.']);
    if ( empty($phone) || strlen(preg_replace('/\s/', '', $phone)) < 9 )
        wp_send_json_error(['msg' => 'Please enter a valid phone number.']);
    if ( empty($dept) )
        wp_send_json_error(['msg' => 'Please select a department.']);
    if ( empty($msg) || strlen(trim($msg)) < 5 )
        wp_send_json_error(['msg' => 'Please enter your message (at least 5 characters).']);

    $to      = medicare_email();
    $subject = "[CareVee Contact] {$dept} - {$name}";
    $body    = "New contact form submission from careveekenya.co.ke\n\n";
    $body   .= "Name:        {$name}\n";
    $body   .= "Email:       {$email}\n";
    $body   .= "Phone:       {$phone}\n";
    $body   .= "Department:  {$dept}\n\n";
    $body   .= "Message:\n{$msg}\n\n";
    $body   .= "Sent via CareVee Pharmacy website contact form.\n";

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        "Reply-To: {$name} <{$email}>",
    ];

    $sent = wp_mail($to, $subject, $body, $headers);

    if ( $sent ) {
        wp_send_json_success(['msg' => 'Message sent successfully!']);
    } else {
        global $phpmailer;
        $smtp_err = ( isset($phpmailer) && !empty($phpmailer->ErrorInfo) )
            ? $phpmailer->ErrorInfo
            : 'Unknown mail error.';
        wp_send_json_error(['msg' => 'Mail failed: ' . $smtp_err]);
    }
}
add_action('wp_ajax_medicare_contact',        'medicare_contact');
add_action('wp_ajax_nopriv_medicare_contact', 'medicare_contact');

// ─── PRESCRIPTION FORM AJAX ───────────────────
function carevee_submit_prescription() {
    ob_clean();

    if ( !wp_verify_nonce($_POST['rx_nonce'] ?? '', 'rx_nonce_action') ) {
        wp_send_json_error(['msg' => 'Security check failed. Please refresh and try again.']);
    }

    $fname     = sanitize_text_field($_POST['rx_fname']     ?? '');
    $lname     = sanitize_text_field($_POST['rx_lname']     ?? '');
    $age       = sanitize_text_field($_POST['rx_age']       ?? '');
    $gender    = sanitize_text_field($_POST['rx_gender']    ?? '');
    $phone     = sanitize_text_field($_POST['rx_phone']     ?? '');
    $email     = sanitize_email($_POST['rx_email']          ?? '');
    $location  = sanitize_text_field($_POST['rx_location']  ?? '');
    $allergies = sanitize_text_field($_POST['rx_allergies'] ?? '');
    $notes     = sanitize_textarea_field($_POST['rx_notes'] ?? '');

    if ( empty($fname) )    wp_send_json_error(['msg' => 'First name is required.']);
    if ( empty($lname) )    wp_send_json_error(['msg' => 'Last name is required.']);
    if ( empty($age) )      wp_send_json_error(['msg' => 'Age is required.']);
    if ( empty($gender) )   wp_send_json_error(['msg' => 'Please select your gender.']);
    if ( empty($phone) )    wp_send_json_error(['msg' => 'Phone number is required.']);
    if ( empty($location) ) wp_send_json_error(['msg' => 'Delivery address is required.']);

    // Handle file upload
    $attachment = [];
    if ( !empty($_FILES['rx_file']['name']) ) {
        $allowed_types = [
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
        $file_type = $_FILES['rx_file']['type'];
        if ( !in_array($file_type, $allowed_types) ) {
            wp_send_json_error(['msg' => 'Invalid file type. Please upload an image, PDF or Word document.']);
        }
        if ( $_FILES['rx_file']['size'] > 10 * 1024 * 1024 ) {
            wp_send_json_error(['msg' => 'File too large. Maximum size is 10MB.']);
        }
        $upload = wp_upload_bits($_FILES['rx_file']['name'], null, file_get_contents($_FILES['rx_file']['tmp_name']));
        if ( $upload['error'] ) {
            wp_send_json_error(['msg' => 'File upload failed: ' . $upload['error']]);
        }
        $attachment[] = $upload['file'];
    } else {
        wp_send_json_error(['msg' => 'Please attach your prescription file.']);
    }

    $to      = 'sales@careveekenya.co.ke';
    $subject = '[CareVee Prescription] ' . $fname . ' ' . $lname . ' — ' . $phone;
    $body    = "New Prescription Submission from careveekenya.co.ke\n\n";
    $body   .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $body   .= "PATIENT DETAILS\n";
    $body   .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $body   .= "Name:            " . $fname . ' ' . $lname . "\n";
    $body   .= "Age:             " . $age . "\n";
    $body   .= "Gender:          " . $gender . "\n";
    $body   .= "Phone:           " . $phone . "\n";
    $body   .= "Email:           " . ($email ?: 'Not provided') . "\n";
    $body   .= "Delivery Address:" . $location . "\n";
    $body   .= "Allergies:       " . ($allergies ?: 'None mentioned') . "\n";
    $body   .= "Notes:           " . ($notes ?: 'None') . "\n";
    $body   .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $body   .= "Prescription file is attached to this email.\n";
    $body   .= "Please review and contact the patient via phone or WhatsApp.\n\n";
    $body   .= "Submitted: " . current_time('mysql') . "\n";

    $headers = ['Content-Type: text/plain; charset=UTF-8'];
    if ( $email ) {
        $headers[] = "Reply-To: {$fname} {$lname} <{$email}>";
    }

    $sent = wp_mail($to, $subject, $body, $headers, $attachment);

    // Clean up uploaded file after sending
    if ( !empty($attachment[0]) && file_exists($attachment[0]) ) {
        @unlink($attachment[0]);
    }

    if ( $sent ) {
        wp_send_json_success(['msg' => 'Your prescription was successfully received! Our professional pharmacist will review it and get back to you soon.']);
    } else {
        global $phpmailer;
        $err = (isset($phpmailer) && !empty($phpmailer->ErrorInfo)) ? $phpmailer->ErrorInfo : 'Unknown error.';
        wp_send_json_error(['msg' => 'Failed to send: ' . $err]);
    }
}
add_action('wp_ajax_carevee_submit_prescription',        'carevee_submit_prescription');
add_action('wp_ajax_nopriv_carevee_submit_prescription', 'carevee_submit_prescription');

// ─── ADMIN SETTINGS PAGE ──────────────────────
function medicare_admin_menu() {
    add_menu_page('Carevee Settings', 'Carevee Settings', 'manage_options', 'medicare-settings', 'medicare_settings_page', 'dashicons-heart', 60);
}
add_action('admin_menu', 'medicare_admin_menu');

function medicare_settings_page() {
    if ( isset($_POST['medicare_save']) ) {
        check_admin_referer('medicare_settings_save');
        foreach (['medicare_wa','medicare_phone','medicare_address','medicare_tagline','medicare_email'] as $k) {
            update_option($k, sanitize_text_field($_POST[$k] ?? ''));
        }
        echo '<div class="updated"><p>✅ Settings saved!</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>⚕️ Carevee Settings</h1>
        <form method="post">
            <?php wp_nonce_field('medicare_settings_save'); ?>
            <table class="form-table">
                <tr>
                    <th>WhatsApp Number</th>
                    <td>
                        <input type="text" name="medicare_wa" value="<?php echo esc_attr(medicare_wa()); ?>" class="regular-text">
                        <p class="description">Numbers only, no spaces or + e.g. <strong>254790007616</strong></p>
                    </td>
                </tr>
                <tr>
                    <th>Display Phone</th>
                    <td>
                        <input type="text" name="medicare_phone" value="<?php echo esc_attr(medicare_phone()); ?>" class="regular-text">
                        <p class="description">Displayed in header/footer e.g. <strong>+254 790 007 616</strong></p>
                    </td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td><input type="text" name="medicare_address" value="<?php echo esc_attr(medicare_address()); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th>Tagline</th>
                    <td><input type="text" name="medicare_tagline" value="<?php echo esc_attr(medicare_tagline()); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th>Display Email</th>
                    <td><input type="text" name="medicare_email" value="<?php echo esc_attr(medicare_email()); ?>" class="regular-text"></td>
                </tr>
            </table>
            <?php submit_button('Save Settings', 'primary', 'medicare_save'); ?>
        </form>
    </div>
    <?php
}

// ─── SCHEMA MARKUP ────────────────────────────
function medicare_schema() {
    if ( is_admin() ) return;
    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Pharmacy',
        'name'        => get_bloginfo('name'),
        'description' => get_bloginfo('description'),
        'url'         => home_url('/'),
        'telephone'   => medicare_phone(),
        'address'     => [
            '@type'           => 'PostalAddress',
            'addressLocality' => medicare_address(),
            'addressCountry'  => 'KE',
        ],
        'contactPoint' => [
            '@type'       => 'ContactPoint',
            'contactType' => 'customer service',
            'telephone'   => medicare_phone(),
        ],
    ];
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}
add_action('wp_head', 'medicare_schema');

// ─── MISC ─────────────────────────────────────
add_filter('excerpt_length', function() { return 18; }, 999);
add_filter('excerpt_more',   function() { return '...'; });

// Remove WooCommerce default breadcrumb
add_action('init', function() {
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
});
