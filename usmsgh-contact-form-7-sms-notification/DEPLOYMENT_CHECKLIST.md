# USMS-GH Contact Form 7 SMS Notification v1.1.0 - Deployment Checklist

## Pre-Deployment Checks

### Environment Verification
- [ ] **PHP Version:** Verify server is running PHP 7.4 or higher
  ```bash
  # Check PHP version
  php -v
  ```
- [ ] **WordPress Version:** Confirm WordPress 5.8 or higher
- [ ] **Contact Form 7:** Confirm Contact Form 7 plugin is installed and active
- [ ] **Backup Status:** Complete backup of entire site created

### Plugin Preparation
- [ ] Export current plugin settings (take screenshots)
- [ ] Note down API Token and Sender ID
- [ ] Document SMS template configurations
- [ ] Review SMS history (export if needed)
- [ ] Test current SMS functionality before upgrade

## Deployment Steps

### Stage 1: Backup & Safety
1. [ ] Create full site backup (files + database)
2. [ ] Download backup to local machine
3. [ ] Test backup restoration (optional but recommended)
4. [ ] Enable maintenance mode (optional)

### Stage 2: Plugin Update
1. [ ] Deactivate current plugin
   - Navigate to: Plugins → Installed Plugins
   - Find "USMS-GH Contact Form 7 SMS Notification"
   - Click "Deactivate"

2. [ ] Update plugin files
   - Option A: Replace via FTP/SFTP
   - Option B: Delete and re-upload (recommended)
   
3. [ ] Activate updated plugin
   - Click "Activate" on plugin listing

4. [ ] Check for any error messages

### Stage 3: Configuration Verification
1. [ ] **Settings Page**
   - [ ] Navigate to: Contact → Sms Integration → Settings
   - [ ] Verify API Token is present
   - [ ] Verify Sender ID is correct
   - [ ] Check country selection
   - [ ] Save settings to test nonce functionality

2. [ ] **History Page**
   - [ ] Navigate to: Contact → Sms Integration → History
   - [ ] Verify SMS history displays correctly
   - [ ] Test pagination (if applicable)

3. [ ] **Contact Form 7 Integration**
   - [ ] Open a Contact Form 7 form
   - [ ] Check USMS-GH tab is visible
   - [ ] Verify SMS template settings are preserved
   - [ ] Review admin phone number
   - [ ] Review visitor number field mapping

## Post-Deployment Testing

### Functional Testing

#### Test 1: Settings Save
- [ ] Navigate to: Contact → Sms Integration → Settings
- [ ] Modify a setting (e.g., sender ID)
- [ ] Click "Save Changes"
- [ ] Verify success message
- [ ] Reload page to confirm setting saved

#### Test 2: Form Submission - Admin SMS
- [ ] Open a test Contact Form 7 form
- [ ] Configure admin SMS notification in USMS-GH tab
- [ ] Save form
- [ ] Submit form on frontend
- [ ] Verify admin receives SMS
- [ ] Check history page for logged message

#### Test 3: Form Submission - Visitor SMS
- [ ] Configure visitor SMS notification in USMS-GH tab
- [ ] Ensure visitor phone field is mapped correctly
- [ ] Save form
- [ ] Submit form with valid phone number
- [ ] Verify visitor receives SMS
- [ ] Check history page for logged message

#### Test 4: SMS History Management
- [ ] Navigate to history page
- [ ] Test delete single entry (if AJAX is working)
- [ ] Verify entry is removed
- [ ] Test empty all history (with caution)

### Technical Testing

#### Security Checks
- [ ] Verify nonce fields present in forms
- [ ] Test form submission without nonce (should fail)
- [ ] Verify only admins can access settings
- [ ] Check AJAX calls include security parameter

#### Error Handling
- [ ] Test form with empty API token (should show error)
- [ ] Test form with invalid data
- [ ] Check for PHP warnings/errors in debug log
- [ ] Verify error messages display correctly

### Performance Testing
- [ ] Check page load times
- [ ] Verify no PHP errors in debug log
- [ ] Test with multiple form submissions
- [ ] Monitor server resources

## Troubleshooting Guide

### Common Issues

#### Issue: SMS Not Sending
**Solutions:**
- [ ] Verify API credentials are correct
- [ ] Check SMS balance on UsmsGH account
- [ ] Review debug log for errors
- [ ] Test API connection manually
- [ ] Ensure Contact Form 7 is active

#### Issue: PHP Version Error
**Solution:**
- [ ] Contact hosting provider
- [ ] Request PHP 7.4+ upgrade
- [ ] Test site compatibility after upgrade

#### Issue: Settings Not Saving
**Solutions:**
- [ ] Check file permissions (644 for files, 755 for directories)
- [ ] Clear WordPress cache
- [ ] Disable conflicting plugins temporarily
- [ ] Check PHP error logs
- [ ] Verify user has `manage_options` capability

#### Issue: AJAX Not Working
**Solutions:**
- [ ] Clear browser cache
- [ ] Check browser console for JavaScript errors
- [ ] Verify WordPress AJAX URL is correct
- [ ] Check if nonce is being passed correctly
- [ ] Test in different browser

#### Issue: History Not Displaying
**Solutions:**
- [ ] Check database for `wpcf7is_history` option
- [ ] Verify user has proper capabilities
- [ ] Clear all caches
- [ ] Check for JavaScript errors

## Rollback Procedure

If critical issues occur:

1. [ ] Immediately restore from backup
2. [ ] Deactivate the updated plugin
3. [ ] Install previous version (1.0.1)
4. [ ] Restore plugin settings from screenshots
5. [ ] Document the issue encountered
6. [ ] Contact support with error details

## Success Criteria

All items must be checked:
- [ ] Plugin activates without errors
- [ ] Settings page loads correctly
- [ ] API credentials are preserved
- [ ] Test form submission sends SMS to admin
- [ ] Test form submission sends SMS to visitor
- [ ] SMS history displays correctly
- [ ] History management functions work
- [ ] No PHP errors in debug log
- [ ] Site performance remains normal
- [ ] All Contact Form 7 forms work normally

## Documentation Updates

- [ ] Update internal documentation
- [ ] Inform team members of upgrade
- [ ] Document any configuration changes
- [ ] Note any new features or changes

## Monitoring

### First 24 Hours
- [ ] Monitor error logs hourly
- [ ] Check SMS sending success rate
- [ ] Monitor form submissions
- [ ] Review customer feedback
- [ ] Check history log growth

### First Week
- [ ] Daily error log reviews
- [ ] Monitor SMS delivery rates
- [ ] Track any support requests
- [ ] Verify all forms sending SMS correctly
- [ ] Regular backup verification

### Ongoing
- [ ] Weekly log reviews
- [ ] Monthly SMS usage analysis
- [ ] Quarterly compatibility checks
- [ ] Regular backup verification

## Additional Notes

### For Multi-Form Setups
- [ ] Test SMS for each form individually
- [ ] Verify form-specific SMS templates work
- [ ] Check that each form's settings are preserved
- [ ] Test different sender configurations per form

### For Multi-Language Sites
- [ ] Test SMS in different languages
- [ ] Verify character encoding in SMS
- [ ] Check special characters display correctly

## Contact Information

### Support Resources
- **Plugin Support:** https://usmsgh.com/contact-support/
- **WordPress Support:** https://wordpress.org/support
- **Contact Form 7 Support:** https://contactform7.com/support/

### Emergency Contacts
- Hosting Provider: [Add your hosting support contact]
- WordPress Developer: [Add your developer contact]
- Site Administrator: [Add admin contact]

## Sign-Off

### Pre-Deployment
- [ ] Technical Lead Approval: _________________ Date: _______
- [ ] Site Owner Approval: _________________ Date: _______

### Post-Deployment
- [ ] Testing Completed: _________________ Date: _______
- [ ] Deployment Successful: _________________ Date: _______
- [ ] Documentation Updated: _________________ Date: _______

---

**Deployment Version:** 1.1.0  
**Deployment Date:** _______________  
**Deployed By:** _______________  
**Status:** [ ] Success [ ] Failed [ ] Rolled Back

## Notes
_Use this space to document any issues, observations, or special considerations during deployment:_

---
---
---
