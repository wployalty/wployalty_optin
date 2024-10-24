const {registerPlugin} = wp.plugins;
const {
    ExperimentalOrderMeta,
    ExperimentalOrderShippingPackages,
    ExperimentalDiscountsMeta,
    registerCheckoutFilters,
    registerCheckoutBlock, ValidatedTextInput
} = wc.blocksCheckout;
const {addAction} = wp.hooks;
const {__} = wp.i18n;
const {useState, useCallback} = wp.element;

function getSettings() {
    return wc.wcSettings.getSetting("wp-loyalty-optin-message_data");
}

function getSetting($key, value = '') {
    let settings = getSettings();
    return settings[$key] ? settings[$key] : value;
}

let is_enable_optin_field = getSetting('is_enable_optin_field', false);
if (is_enable_optin_field) {
    const OptinBlock = ({children, checkoutExtensionData}) => {
        const [wlrOptin, setWlrOptin] = useState(getSetting('user_optin', false));
        const {setExtensionData} = checkoutExtensionData;
        const onCheckboxChange = useCallback((event) => {
            const value = event.target.checked;
            setExtensionData('wlopt_checkout_block', 'wpl_optin', value);
            setWlrOptin(value);
        }, [setWlrOptin, setExtensionData]);
        return (
            <div className={'wlr-optin-field'}>
                <input
                    id="wlr_optin"
                    type="checkbox"
                    checked={wlrOptin}
                    onChange={onCheckboxChange}
                    className={'wlr-optin-checkbox'}
                />
                <label htmlFor={"wpl_optin"}>
                    {__('Check this to become member of WPLoyalty', 'wp-loyalty-optin')}
                </label>
            </div>
        );
    };
    const optin_metadata = {
        "$schema": "https://schemas.wp.org/trunk/block.json",
        "apiVersion": 2,
        "name": "wlr_checkout_optin_block",
        "version": "1.0.0",
        "title": "Opt-in Checkbox",
        "category": "woocommerce",
        "parent": ["woocommerce/checkout-billing-address-block"],
        "attributes": {
            "lock": {
                "type": "object",
                "default": {
                    "remove": true,
                    "move": true
                }
            }
        }
    };
    optin_metadata.parent = getSetting('optin_parent_block', ["woocommerce/checkout-billing-address-block"]);

    const options = {
        metadata: optin_metadata,
        component: OptinBlock
    };

    registerCheckoutBlock(options);
}