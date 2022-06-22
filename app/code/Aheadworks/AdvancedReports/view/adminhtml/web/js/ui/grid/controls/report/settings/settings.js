/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'underscore',
    'mageUtils',
    'uiCollection'
], function (_, utils, Collection) {
    'use strict';

    return Collection.extend({
        defaults: {
            applied: {},
            settings: {},
            statefull: {
                applied: true
            },
            listens: {
                applied: 'initSettings',
                settings: 'initAppliedByDefault'
            },
            exports: {
                applied: '${ $.provider }:params.report_settings'
            }
        },

        /**
         * Initializes settings component
         *
         * @returns {Settings} Chainable
         */
        initialize: function () {
            this._super();

            return this;
        },

        /**
         * Sets settings data to the applied state
         *
         * @returns {Settings} Chainable
         */
        apply: function () {
            this.set('applied', utils.copy(this.settings));

            return this;
        },

        /**
         * Initialize settings variable to the applied state
         *
         * @returns {Settings} Chainable
         */
        initSettings: function () {
            this.set('settings', utils.copy(this.applied));

            return this;
        },

        /**
         * Initialize applied variable default values (from settings)
         *
         * @returns {Settings} Chainable
         */
        initAppliedByDefault: function () {
            if (_.keys(this.applied).length != _.keys(this.settings).length) {
                this.set('applied', utils.copy(this.settings));
            }

            return this;
        }
    });
});
