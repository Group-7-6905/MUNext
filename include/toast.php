<?php
/**
 * ==================== TOAST NOTIFICATION SYSTEM ====================
 * Handles toast notifications across page redirects using sessions
 */

class Toast {
    const TYPE_SUCCESS = 'success';
    const TYPE_ERROR = 'error';
    const TYPE_WARNING = 'warning';
    const TYPE_INFO = 'info';
    
    /**
     * Set a toast message
     */
    public static function set($message, $type = self::TYPE_INFO) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['toast'] = [
            'message' => $message,
            'type' => $type,
            'timestamp' => time()
        ];
    }
    
    /**
     * Set success toast
     */
    public static function success($message) {
        self::set($message, self::TYPE_SUCCESS);
    }
    
    /**
     * Set error toast
     */
    public static function error($message) {
        self::set($message, self::TYPE_ERROR);
    }
    
    /**
     * Set warning toast
     */
    public static function warning($message) {
        self::set($message, self::TYPE_WARNING);
    }
    
    /**
     * Set info toast
     */
    public static function info($message) {
        self::set($message, self::TYPE_INFO);
    }
    
    /**
     * Get and clear toast message
     */
    public static function get() {
        if (session_status() === PHP_SESSION_NONE) {
            // session_start();
        }
        
        if (isset($_SESSION['toast'])) {
            $toast = $_SESSION['toast'];
            unset($_SESSION['toast']);
            return $toast;
        }
        return null;
    }
    
    /**
     * Check if toast exists
     */
    public static function has() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['toast']);
    }
    
    /**
     * Render toast JavaScript
     */
    public static function render() {
        $toast = self::get();
        if ($toast === null) {
            return '';
        }
        
        $message = htmlspecialchars($toast['message'], ENT_QUOTES, 'UTF-8');
        $type = htmlspecialchars($toast['type'], ENT_QUOTES, 'UTF-8');
        
        return "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof showToast === 'function') {
                showToast(" . json_encode($message) . ", " . json_encode($type) . ");
            }
        });
        </script>";
    }
}
?>