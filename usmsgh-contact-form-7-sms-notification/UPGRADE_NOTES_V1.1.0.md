# USMS-GH Contact Form 7 SMS Notification - Upgrade to Version 1.1.0

## Overview
This document outlines all the changes made to upgrade the plugin to version 1.1.0, ensuring full compatibility with the latest WordPress and Contact Form 7 versions.

## Version Information
- **Previous Version:** 1.0.1
- **New Version:** 1.1.0
- **Release Date:** December 2024

## Compatibility Updates

### WordPress
- **Previous:** Tested up to 6.3.1
- **Current:** Tested up to 6.7
- **Minimum Required:** 5.8 (updated from 3.4)

### Contact Form 7
- **Current:** Compatible with latest Contact Form 7 versions
- **Dependency:** Plugin now declares Contact Form 7 as required

### PHP
- **Previous:** No minimum specified
- **Current:** 7.4 minimum requirement
- **Reason:** PHP 7.4+ provides better performance, security, and modern features

## Major Changes

### 1. Enhanced Security 🔒

#### Nonce Verification
Added comprehensive nonce verification to prevent CSRF attacks:

**Files Modified:**
- `admin/class-admin-init.php` - Settings form nonce verification
- `admin/class-contact-form-integration.php` - AJAX actions nonce verification
- `template/cf7-settings.php` - Added nonce field to form

**Code Example:**
```php
// Before (vulnerable to CSRF)
if (isset($_POST['save_api_settings'])) {
    $api_token = sanitize_text_field($_POST['api_token']);
    // ... save settings
}

// After (CSRF protected)
if (isset($_POST['save_api_settings'])) {
    // Capability check
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'Insufficient permissions.' ) );
    }
    
    // Nonce verification
    if ( ! wp_verify_nonce( $_POST['cf7isi_settings_nonce'], 'cf7isi_save_settings' ) ) {
        wp_die( __( 'Security check failed.' ) );
    }
    
    $api_token = sanitize_text_field( wp_unslash( $_POST['api_token'] ) );
    // ... save settings
}
```

#### Capability Checks
Added proper capability checks for all admin functions:

**Changes:**
- Settings save requires `manage_options` capability
- AJAX history deletion requires `manage_options` capability
- Contact form SMS settings save requires `wpcf7_edit_contact_form` capability

#### Input Sanitization
Improved input sanitization throughout the plugin:

**Key Changes:**
- All `$_POST` and `$_GET` access now wrapped with `wp_unslash()` and proper sanitization
- Tab parameter validation with whitelist approach
- All output properly escaped with `esc_html()`, `esc_attr()`, `esc_url()`
- Array handling improved with proper validation

### 2. AJAX Security Improvements

**Files Modified:**
- `admin/class-contact-form-integration.php`

**Changes:**
- Added nonce verification for history deletion AJAX call
- Added nonce verification for empty history AJAX call
- Added capability checks before processing AJAX requests

**Implementation:**
```php
// AJAX handlers now include security checks
public function delete_cf7sms_history() {
    // Security check
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'Insufficient permissions.' ) );
    }
    
    // Verify nonce
    check_ajax_referer( 'cf7isi_ajax_nonce', 'security' );
    
    // Process deletion
    // ...
}
```

### 3. Template Security Enhancements

**File:** `template/cf7-conf-header.php`

**Changes:**
- Tab parameter sanitized with `sanitize_key()`
- Tab value validated against allowed tabs (whitelist)
- All output properly escaped
- Removed direct `$_REQUEST` access in template logic

**Before:**
```php
if(isset($_REQUEST['tab'])){
    if($_REQUEST['tab'] == 'history'){
        // ... load history
    }
}
```

**After:**
```php
$current_tab = isset($_REQUEST['tab']) ? sanitize_key( wp_unslash( $_REQUEST['tab'] ) ) : '';
$current_tab = array_key_exists($current_tab, $menus) ? $current_tab : '';

if( !empty($current_tab) ){
    if($current_tab == 'history'){
        // ... load history
    }
}
```

## Detailed File Changes

### Core Plugin File
**File:** `index.php`
- Updated plugin version to 1.1.0
- Added WordPress version requirements in header (5.8 - 6.7)
- Added PHP 7.4 minimum requirement
- Added `Requires Plugins: contact-form-7` directive
- Updated internal version property

### Admin Settings
**File:** `admin/class-admin-init.php`
- Added nonce verification to `save_settings()` method
- Added capability check (`manage_options` permission)
- Improved input sanitization with `wp_unslash()`
- Enhanced error messages with proper i18n
- Changed error handling from `_e()` to `wp_die()` for better UX

### Contact Form Integration
**File:** `admin/class-contact-form-integration.php`
- Added AJAX nonce verification for history management
- Added capability checks to AJAX handlers
- Improved `save_form()` method with capability check
- Enhanced array handling with proper validation
- Added `wp_unslash()` to all `$_POST` access

### Settings Template
**File:** `template/cf7-settings.php`
- Added `wp_nonce_field()` for form security
- Nonce field integrates with server-side verification

### Header Template
**File:** `template/cf7-conf-header.php`
- Sanitized tab parameter with `sanitize_key()`
- Validated tab against allowed values
- Added proper output escaping (`esc_attr()`, `esc_html()`, `esc_url()`)
- Improved code organization and readability

### Documentation
**File:** `readme.txt`
- Updated version to 1.1.0
- Updated WordPress/Contact Form 7 compatibility versions
- Updated PHP requirement
- Added comprehensive changelog

## Breaking Changes

### PHP Version Requirement
**Impact:** Sites running PHP < 7.4 will not be able to use this version.

**Action Required:**
- Upgrade PHP to version 7.4 or higher before updating the plugin
- Check with your hosting provider for PHP upgrade options

### AJAX Calls Now Require Nonce
**Impact:** Any custom AJAX calls to history management functions will fail without proper nonce.

**Action Required:**
- If you have custom code making AJAX calls to this plugin, update to include nonce:
```javascript
// Add nonce to AJAX calls
data: {
    action: 'Contact_FormISISMSHISTORYDELETE',
    security: cf7isi_ajax_nonce, // Add this
    deleteID: id
}
```

## What's Preserved

✅ **All existing functionality works exactly as before:**
- SMS notifications on Contact Form 7 submissions
- Admin SMS notifications
- Visitor SMS notifications
- Message template customization with CF7 tags
- SMS history logging
- Country selection
- Sender ID configuration
- All settings and configurations

**No breaking changes to existing SMS features!**

## Testing Recommendations

### Before Deployment
1. **Backup Your Site:** Always backup before upgrading
2. **Test Environment:** Test in staging environment first
3. **PHP Version:** Verify PHP 7.4+ is available
4. **Contact Form 7:** Ensure Contact Form 7 is installed and active

### After Deployment
1. **Test Settings Page:**
   - Navigate to: Contact → Sms Integration
   - Verify API Token and Sender ID settings load correctly
   - Save settings and verify success

2. **Test Form Submission:**
   - Create a test Contact Form 7 form
   - Configure SMS notification in USMS-GH tab
   - Submit form and verify SMS is sent
   - Check SMS history for logged message

3. **Test History Management:**
   - View SMS history
   - Test delete single history entry
   - Test empty history function

4. **Check AJAX Functionality:**
   - Verify history deletion works
   - Check browser console for any JavaScript errors

## Migration Guide

### Step 1: Pre-Update Checklist
- [ ] Backup entire WordPress site
- [ ] Export plugin settings (screenshot)
- [ ] Note current PHP version
- [ ] Verify Contact Form 7 is active
- [ ] Test current SMS functionality

### Step 2: PHP Upgrade (if needed)
If running PHP < 7.4:
1. Contact hosting provider
2. Request PHP 7.4 or higher
3. Test site on new PHP version
4. Verify Contact Form 7 compatibility

### Step 3: Update Plugin
1. Deactivate current plugin version
2. Delete old plugin files (keep backup!)
3. Upload new plugin version
4. Activate plugin
5. Verify settings are intact

### Step 4: Testing
Follow the "Testing Recommendations" section above.

## Known Issues & Limitations

### None Currently Identified
The upgrade has been designed to maintain backward compatibility with existing functionality while adding new security features.

## Support & Documentation

### Getting Help
If you encounter issues after upgrading:

1. **Check Settings:** Verify all plugin settings are correct
2. **Enable Debug Mode:** Set `WP_DEBUG` to `true` in wp-config.php
3. **Check Logs:** Review error logs for any issues
4. **Test Form:** Ensure Contact Form 7 forms are working
5. **Contact Support:** Reach out to plugin support with:
   - WordPress version
   - Contact Form 7 version
   - PHP version
   - Error messages (if any)

### Useful Resources
- [WordPress Debug Mode](https://wordpress.org/support/article/debugging-in-wordpress/)
- [Contact Form 7 Documentation](https://contactform7.com/docs/)
- [USMS-GH Support](https://usmsgh.com/contact-support/)

## Security Improvements Summary

### Added
- Nonce verification for all form submissions
- Capability checks for admin functions
- AJAX nonce verification
- Enhanced input sanitization
- Output escaping in templates
- Tab parameter validation

### Changed
- All `$_POST` access now uses `wp_unslash()`
- Error handling improved with `wp_die()`
- Better validation for user inputs

### Fixed
- CSRF vulnerability in settings save
- CSRF vulnerability in AJAX handlers
- XSS vulnerability in tab parameter
- Unescaped output in templates

## Changelog Summary

### Added
- Nonce verification throughout
- Capability checks for sensitive operations
- Enhanced security for AJAX calls
- Better error handling

### Changed
- WordPress compatibility: 6.3.1 → 6.7
- PHP requirement: None → 7.4
- Security improvements across all forms
- Input sanitization enhanced

### Fixed
- Security vulnerabilities (CSRF, XSS)
- Input validation issues
- Output escaping in templates

### Security
- Added nonce verification to prevent CSRF attacks
- Improved input sanitization throughout
- Added capability checks for admin functions
- Enhanced AJAX security

## Developer Notes

### For Theme/Plugin Developers
If you have custom code that interacts with this plugin:

1. **AJAX Calls:** Must now include nonce in `security` parameter
2. **Direct Function Calls:** Ensure user has proper capabilities
3. **Hooks:** All existing hooks remain unchanged
4. **Filters:** All existing filters remain functional

### Code Standards
This upgrade follows:
- WordPress Coding Standards
- Contact Form 7 Development Best Practices
- PHPCS (PHP CodeSniffer) guidelines
- Security best practices (OWASP)

## Conclusion

This upgrade ensures the USMS-GH Contact Form 7 SMS Notification plugin remains compatible with the latest WordPress and Contact Form 7 versions while maintaining all existing functionality. The plugin now includes enhanced security features and follows modern WordPress development standards.

**Upgrade Status:** ✅ Complete and Production-Ready

---

**Version:** 1.1.0  
**Date:** December 2024  
**Compatibility:** WordPress 5.8 - 6.7, Contact Form 7 (latest), PHP 7.4+
