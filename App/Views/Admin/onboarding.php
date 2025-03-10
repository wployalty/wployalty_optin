<?php
defined( "ABSPATH" ) or die();
?>
<div id="wlopt-popup" class="wlopt-popup">
    <div class="wlopt-popup-head">
        <h3><?php _e( 'Update Preference', 'wp-loyalty-optin' ) ?></h3>
    </div>
    <div class="wlopt-popup-body">
        <div>
            <div>
                <label for="update-preference"><?php _e( 'Loyalty acceptance preference for existing users', 'wp-loyalty-optin' ); ?></label>
            </div>
            <div>
                <select name="update-preference" id="update-preference" class="wlopt-multi-select">
                    <option value="yes"><?php _e( 'Opt in', 'wp-loyalty-optin' ); ?></option>
                    <option value="no"><?php _e( 'Opt out', 'wp-loyalty-optin' ); ?></option>
                </select>
            </div>
        </div>
    </div>
    <div class="wlopt-popup-foot">
        <button type="button" id="wlopt-onboard-submit"><?php _e( 'Proceed' ); ?></button>
    </div>
</div>
<div id="wlopt-overlay-section" class="wlopt-overlay-section active">
    <div class="wlopt-overlay"></div>
</div>
