;(function (window) {
    const objAttr = {writable: false, configurable: false};

    const application = {};
    const endpoints = {};
    const config = {};
    const assets = {};

    Object.defineProperty(this, 'helpers', {...objAttr, value: {}});

    // application

    Object.defineProperty(application, 'name', {
        ...objAttr,
        value: 'zentry'
    });
    Object.defineProperty(application, 'theme', {
        ...objAttr,
        value: null
    });
    Object.defineProperty(this, 'application', {...objAttr, value: application});

    // endpoints

    Object.defineProperty(endpoints, 'api', {
        ...objAttr,
        value: 'http://localhost:8080/'
    });
    Object.defineProperty(endpoints, 'echo', {
        ...objAttr,
        value: 'http://php-frm:6001/'
    });
    Object.defineProperty(this, 'endpoints', {...objAttr, value: endpoints});

    // config
    Object.defineProperty(config, 'production', {
        ...objAttr,
        value: false
    });
    Object.defineProperty(config, 'services', {
        ...objAttr,
        value: {
            oAuth: {
                google: {
                    clientId: '994334294962-pftjjpsm6tsirc74uao2vvunajein5ia.apps.googleusercontent.com'
                }
            },
            agm: {
                apiKey: 'AIzaSyCEYZ8dkNG7mAKXDw4eR7aQvdlFCyxUCVo'
            },
            kloudless: {
                appId: 'MsTfTraYMe_egLe5ViGfmtD56C9g_K1wZHjgocgrtKi8GV_R'
            }
        }
    });
    Object.defineProperty(this, 'config', {...objAttr, value: config});

    // assets
    Object.defineProperty(assets, 'widgets', {
        ...objAttr,
        value: {
            macOS: '',
            windows: '',
        }
    });

    Object.defineProperty(this, 'assets', {...objAttr, value: assets});
})(window);
