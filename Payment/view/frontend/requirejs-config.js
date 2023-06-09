/**
 * Copyright © Rollpix. All rights reserved.
 * See LICENSE for license details.
 */
var config = {
    map: {
        '*': {
            rollpixUi: 'Rollpix_Payment/js/view/ui'
        }
    },
    shim: {
        'rollpix-js' : {
            'exports': 'rollpix'
        }
    },
    paths: {
        'rollpix-js': 'https://cdn.rollpix.io/v1/js/rollpix'
    }
};
