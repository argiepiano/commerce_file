<?php

/**
 * File license type.
 */
class CommerceLicenseFile extends CommerceLicenseBase  {

  /**
   * Implements CommerceLicenseInterface::isConfigurable().
   */
  public function isConfigurable() {
    return FALSE;
  }

  /**
   * Implements CommerceLicenseInterface::accessDetails().
   */
  public function accessDetails() {
    // Display the files.
    $product = $this->wrapper->product->value();
    // Retrieve the instance of commerce_file field in this particular product type and bundle
    $commerce_file_field = field_info_instance($this->wrapper->getPropertyInfo()['product']['type'], 'commerce_file', $this->wrapper->product->type->value());
    // Retrieve the display type for the commerce_file field 
    $display_type = $commerce_file_field['display']['default']['type'];
    $display = array(
      'label' => 'hidden',
      'type' => $display_type,
      'settings' => array(
        // The access check confirms that the product has a license.
        // Since we're calling this formatter from a license, there is no
        // point in performing that check.
        'check_access' => FALSE,
      ),
    );
    $output = field_view_field('commerce_product', $product, 'commerce_file', $display);
    return drupal_render($output);
  }

  /**
   * Implements CommerceLicenseInterface::checkoutCompletionMessage().
   */
  public function checkoutCompletionMessage() {
    // Store the uid in the session. The file access function will use it
    // if the user is anonymous, allowing the download to proceed.
    $_SESSION['commerce_license_uid'] = $this->uid;

    $product = $this->wrapper->product->value();
    $message = t('Thank you for purchasing %product.', array('%product' => $product->title)) . '<br />';
    $message .= t('Download now:');
    return $message . $this->accessDetails();
  }

  /**
   * Implements CommerceLicenseInterface::renew().
   */
  public function renew($expires) {
    parent::renew($expires);

    // Clear the download log in order to reset download limits.
    commerce_file_download_log_clear(array('license_id' => $this->license_id));
  }
}
