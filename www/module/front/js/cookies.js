// obtain plugin
var cc = initCookieConsent();

// run plugin with your configuration
cc.run({
    current_lang: 'cs',
    autoclear_cookies: true,                   // default: false
    page_scripts: true,                        // default: false

    // mode: 'opt-in'                          // default: 'opt-in'; value: 'opt-in' or 'opt-out'
    // delay: 0,                               // default: 0
    // auto_language: null                     // default: null; could also be 'browser' or 'document'
    // autorun: true,                          // default: true
    // force_consent: false,                   // default: false
    // hide_from_bots: false,                  // default: false
    // remove_cookie_tables: false             // default: false
    // cookie_name: 'cc_cookie',               // default: 'cc_cookie'
    // cookie_expiration: 182,                 // default: 182 (days)
    // cookie_necessary_only_expiration: 182   // default: disabled
    // cookie_domain: location.hostname,       // default: current domain
    // cookie_path: '/',                       // default: root
    // cookie_same_site: 'Lax',                // default: 'Lax'
    // use_rfc_cookie: false,                  // default: false
    // revision: 0,                            // default: 0

    onFirstAction: function(user_preferences, cookie){
        // callback triggered only once
    },

    onAccept: function (cookie) {
        // ...
    },

    onChange: function (cookie, changed_preferences) {
        // ...
    },

    languages: {
        'cs': {
            consent_modal: {
                title: 'Souhlas s využitím cookies',
                description: 'Pro zlepšení poskytnutí našich služeb využíváme cookies. Nově od 1.1.2022 potřebujeme Váš souhlas. <button type="button" data-cc="c-settings" class="cc-link">Nastavení</button>',
                primary_btn: {
                    text: 'Přijmout vše',
                    role: 'accept_all'              // 'accept_selected' or 'accept_all'
                },
                secondary_btn: {
                    text: 'Nastavení',
                    role: 'settings'        // 'settings' or 'accept_necessary'
                }
            },
            settings_modal: {
                title: 'Nastavení cookies',
                save_settings_btn: 'Uložit nastavení',
                accept_all_btn: 'Povolit vše',
                reject_all_btn: 'Odmítnout vše',
                close_btn_label: 'Close',
                blocks: [
                    {
                        title: 'Využití cookies',
                        description: 'Náš web využívá soubory cookies, které jsou nezbytné pro správnou funkčnost webu nebo dokážou zpříjemnit Váš nákup. <br> Od 1.1.2022 se změnily podmínky pro používání cookies souborů. Potřebujeme tedy Váš souhlas.'
                    }, {
                        title: 'Technické cookies - nutné',
                        description: 'Nezbytné cookies pro funkčnost našeho webu.',
                        toggle: {
                            value: 'necessary',
                            enabled: true,
                            readonly: true          // cookie categories with readonly=true are all treated as "necessary cookies"
                        }
                    }, {
                        title: 'Analytické cookies',
                        description: 'Cookies pro sledování aktivity našich zákazníků na webu. Tyto soubory jsou anonnymní.',
                        toggle: {
                            value: 'analytics',     // your cookie category
                            enabled: false,
                            readonly: false
                        },
                    }, {
                        title: 'Marketingové cookies',
                        description: 'Cookies pro pohodlnější nákup, díky kterému Vám dokážeme nabídnout relativnější sortiment.',
                        toggle: {
                            value: 'targeting',
                            enabled: false,
                            readonly: false
                        }
                    }, {
                        title: 'Kontakt',
                        description: 'Pokud se chcete dozvědět více o využití cookies na naší stránce, kontaktujte nás na adrese <a class="cc-link" href="mailto:info@mopedauto.cz">info@mopedauto.cz</a>.',
                    }
                ]
            }
        }
    },
    gui_options: {
        consent_modal: {
            layout: 'cloud',               // box/cloud/bar
            position: 'bottom center',     // bottom/middle/top + left/right/center
            transition: 'slide',           // zoom/slide
            swap_buttons: false            // enable to invert buttons
        },
        settings_modal: {
            layout: 'box',                 // box/bar
            // position: 'left',           // left/right
            transition: 'slide'            // zoom/slide
        }
    }
});