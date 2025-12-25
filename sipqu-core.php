<?php
/**
 * Plugin Name: SIPQU Core
 * Plugin URI:  https://sipqu.id
 * Description: Core System SIPQU (Sistem Informasi Manajemen Pendidikan Qur'an).
 * Version:     1.0.0
 * Author:      Arrazi
 * Author URI:  https://arrazi.com
 * Text Domain: sipqu-core
 * Domain Path: /languages
 * Requires PHP: 7.4 or higher
 *
 * @package SIPQU_CORE
 */

// Cegah akses langsung ke file ini demi keamanan
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

// ============================================================
// 1. DEFINISI KONSTANTA GLOBAL (SISTEM)
// ============================================================

// Versi Plugin Core (Digunakan untuk cache busting assets)
if ( ! defined( 'SIPQU_CORE_VERSION' ) ) {
    define( 'SIPQU_CORE_VERSION', '1.0.0' );
}

// Versi Database (Digunakan untuk Migration/Upgrade logic)
// Jika angka ini berubah, sistem akan menjalankan script upgrader.php
if ( ! defined( 'SIPQU_DB_VERSION' ) ) {
    define( 'SIPQU_DB_VERSION', '1.0.0' );
}

// Path absolut ke file utama plugin ini
if ( ! defined( 'SIPQU_CORE_FILE' ) ) {
    define( 'SIPQU_CORE_FILE', __FILE__ );
}

// Path absolut ke direktori plugin (dengan trailing slash)
if ( ! defined( 'SIPQU_CORE_PATH' ) ) {
    define( 'SIPQU_CORE_PATH', dirname( SIPQU_CORE_FILE ) . '/' );
}

// URL lengkap ke direktori plugin (untuk load assets CSS/JS)
// Note: plugin_dir_url() is defined by WordPress, defined here after plugins_loaded
if ( ! defined( 'SIPQU_CORE_URL' ) ) {
    define( 'SIPQU_CORE_URL', rtrim( ( function_exists( 'plugin_dir_url' ) ? plugin_dir_url( __FILE__ ) : plugins_url( '', __FILE__ ) ), '/' ) . '/' );
}

// Plugin basename (dipakai untuk load textdomain dan referensi plugin)
if ( ! defined( 'SIPQU_CORE_BASENAME' ) ) {
    define( 'SIPQU_CORE_BASENAME', plugin_basename( SIPQU_CORE_FILE ) );
}

// Path prefix untuk include folder
// Note: Folder 'includes' tidak digunakan dalam struktur modular saat ini,
// tapi didefinisikan untuk kompatibilitas jika diperlukan di masa depan.
// if ( ! defined( 'SIPQU_CORE_INC' ) ) {
//    define( 'SIPQU_CORE_INC', SIPQU_CORE_PATH . 'includes/' );
// }

// Prefix database (Default wp_, tapi akan diset dinamis via helper)
if ( ! defined( 'SIPQU_DB_PREFIX' ) ) {
    global $wpdb;
    define( 'SIPQU_DB_PREFIX', $wpdb->prefix . 'sipqu_' );
}

// ============================================================
// 2. PENGECEKAN VERSI PHP (KEAMATAN)
// ============================================================

if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
    /**
     * Tampilkan pesan error di admin jika versi PHP tidak memenuhi syarat.
     * SIPQU membutuhkan fitur modern PHP 7.4+ (Typed properties, arrow functions, etc).
     */
    add_action( 'admin_notices', function() {
        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP version 3: Required PHP version */
            esc_html__( '%1$s membutuhkan PHP versi %2$s atau lebih tinggi. Versi PHP Anda saat ini adalah %3$s. Silakan upgrade versi PHP Anda.', 'sipqu-core' ),
            '<strong>SIPQU Core</strong>',
            '7.4',
            PHP_VERSION
        );
        printf( '<div class="error"><p>%1$s</p></div>', wp_kses_post( $message ) );
    } );

    // Hentikan eksekusi plugin agar tidak error fatal
    return;
}

// ============================================================
// 3. BOOTSTRAP LOADER (INITIALISASI)
// ============================================================

/**
 * Memuat file loader yang akan mengatur require semua file class penting.
 * Ini memisahkan logika inisialisasi dari file header ini agar tetap bersih.
 */
require_once SIPQU_CORE_PATH . 'bootstrap/loader.php';

// ============================================================
// 4. HOOK AKTIVASI / DEAKTIVASI
// ============================================================

// Registrasi hook saat plugin diaktifkan (di-handle di activator.php via loader)
if ( function_exists( 'register_activation_hook' ) ) {
    register_activation_hook( __FILE__, array( 'SIPQU_Core_Activator', 'activate' ) );
}

// Registrasi hook saat plugin dinonaktifkan (di-handle di deactivator.php via loader)
if ( function_exists( 'register_deactivation_hook' ) ) {
if ( function_exists( 'register_deactivation_hook' ) ) {
    register_deactivation_hook( __FILE__, array( 'SIPQU_Core_Deactivator', 'deactivate' ) );
}
}

// ============================================================
// 5. KETERANGAN TAMBAHAN
// ============================================================

/**
 * Catatan untuk Developer:
 * 
 * - Jangan menulis logika bisnis langsung di file ini.
 * - Gunakan file ini hanya untuk definisi konstanta dan pemanggilan loader.
 * - Seluruh Class (Auth, Tenant, Audit) akan di-autoload via loader.php.
 * - Modul lain (sipqu-sws, sipqu-finance) akan mengecek konstanta SIPQU_CORE_VERSION
 *   sebelum mereka berjalan.
 */