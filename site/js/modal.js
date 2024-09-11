/* * YouTube Gallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://www.joomlaboat.com
 * @GNU General Public License
 **/

/**
 * YG behavior for editor modal gallery selector
 */

(function (window, document) {
    'use strict';

    window.YG = {
        initialize: function () {
            const o = this.getUriObject(window.self.location.href),
                q = this.getQueryObject(o.query);

            this.frameurl = location.href;

            this.editor = q.e_name;

        },


        showMessage: function (text) {
            const $message = $('#message');

            $message.find('>:first-child').remove();
            $message.append(text);
            $('#messages').css('display', 'block');
        },


        getQueryObject: function (q) {
            const rs = {};

            (q || '').split(/[&;]/).forEach(function (val) {
                const keys = val.split('=');

                rs[decodeURIComponent(keys[0])] = keys.length === 2 ? decodeURIComponent(keys[1]) : null;
            });

            return rs;
        },

        getUriObject: function (u) {
            const bitsAssociate = {},
                bits = u.match(/^(?:([^:\/?#.]+):)?(?:\/\/)?(([^:\/?#]*)(?::(\d*))?)((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[\?#]|$)))*\/?)?([^?#\/]*))?(?:\?([^#]*))?(?:#(.*))?/);

            ['uri', 'scheme', 'authority', 'domain', 'port', 'path', 'directory', 'file', 'query', 'fragment'].forEach(function (key, index) {
                bitsAssociate[index] = (!!bits && !!bits[key]) ? bits[key] : '';
            });

            return bitsAssociate;
        }
    };

    document.addEventListener("DOMContentLoaded", function () {
        window.YG.initialize();
    });

})(window, document);