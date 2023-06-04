(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.radicati_sitenotice = {
    attach: function (context, settings) {

      // Get all notices on the page
      $('.site-notice__block').once().each(function (i, item) {

        var id = $(item).attr('data-site-notice-id');
        var cookieid = "site-notice--" + id;

        // Show notices if the cookie isn't set
        if( !Cookies.get(cookieid) ) {
          $(item).show();
        }
      });

      // On click, set the cookie so that this isn't shown again.
      jQuery('.sitenotice__close__button').on('click', function() {
        var $parent = $(this).closest('.site-notice__block');
        var notice_id = $parent.attr('data-site-notice-id');

        var attributes = {};

        if($parent.attr('data-expiration-days') !== undefined) {
          attributes.expires = Number($parent.attr('data-expiration-days'));
        }

        // Not setting expires automatically makes it a session cookie.
        Cookies.set('site-notice--'+notice_id, 1, attributes);


        $(this).closest('.site-notice__block').hide();
      });
    }
  };

  /*! js-cookie v3.0.5 | MIT */
  ;
  (function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
        typeof define === 'function' && define.amd ? define(factory) :
            (global = typeof globalThis !== 'undefined' ? globalThis : global || self, (function () {
              var current = global.Cookies;
              var exports = global.Cookies = factory();
              exports.noConflict = function () { global.Cookies = current; return exports; };
            })());
  })(this, (function () { 'use strict';

    /* eslint-disable no-var */
    function assign (target) {
      for (var i = 1; i < arguments.length; i++) {
        var source = arguments[i];
        for (var key in source) {
          target[key] = source[key];
        }
      }
      return target
    }
    /* eslint-enable no-var */

    /* eslint-disable no-var */
    var defaultConverter = {
      read: function (value) {
        if (value[0] === '"') {
          value = value.slice(1, -1);
        }
        return value.replace(/(%[\dA-F]{2})+/gi, decodeURIComponent)
      },
      write: function (value) {
        return encodeURIComponent(value).replace(
            /%(2[346BF]|3[AC-F]|40|5[BDE]|60|7[BCD])/g,
            decodeURIComponent
        )
      }
    };
    /* eslint-enable no-var */

    /* eslint-disable no-var */

    function init (converter, defaultAttributes) {
      function set (name, value, attributes) {
        if (typeof document === 'undefined') {
          return
        }

        attributes = assign({}, defaultAttributes, attributes);

        if (typeof attributes.expires === 'number') {
          attributes.expires = new Date(Date.now() + attributes.expires * 864e5);
        }
        if (attributes.expires) {
          attributes.expires = attributes.expires.toUTCString();
        }

        name = encodeURIComponent(name)
            .replace(/%(2[346B]|5E|60|7C)/g, decodeURIComponent)
            .replace(/[()]/g, escape);

        var stringifiedAttributes = '';
        for (var attributeName in attributes) {
          if (!attributes[attributeName]) {
            continue
          }

          stringifiedAttributes += '; ' + attributeName;

          if (attributes[attributeName] === true) {
            continue
          }

          // Considers RFC 6265 section 5.2:
          // ...
          // 3.  If the remaining unparsed-attributes contains a %x3B (";")
          //     character:
          // Consume the characters of the unparsed-attributes up to,
          // not including, the first %x3B (";") character.
          // ...
          stringifiedAttributes += '=' + attributes[attributeName].split(';')[0];
        }

        return (document.cookie =
            name + '=' + converter.write(value, name) + stringifiedAttributes)
      }

      function get (name) {
        if (typeof document === 'undefined' || (arguments.length && !name)) {
          return
        }

        // To prevent the for loop in the first place assign an empty array
        // in case there are no cookies at all.
        var cookies = document.cookie ? document.cookie.split('; ') : [];
        var jar = {};
        for (var i = 0; i < cookies.length; i++) {
          var parts = cookies[i].split('=');
          var value = parts.slice(1).join('=');

          try {
            var found = decodeURIComponent(parts[0]);
            jar[found] = converter.read(value, found);

            if (name === found) {
              break
            }
          } catch (e) {}
        }

        return name ? jar[name] : jar
      }

      return Object.create(
          {
            set,
            get,
            remove: function (name, attributes) {
              set(
                  name,
                  '',
                  assign({}, attributes, {
                    expires: -1
                  })
              );
            },
            withAttributes: function (attributes) {
              return init(this.converter, assign({}, this.attributes, attributes))
            },
            withConverter: function (converter) {
              return init(assign({}, this.converter, converter), this.attributes)
            }
          },
          {
            attributes: { value: Object.freeze(defaultAttributes) },
            converter: { value: Object.freeze(converter) }
          }
      )
    }

    var api = init(defaultConverter, { path: '/' });
    /* eslint-enable no-var */

    return api;

  }));

})(jQuery, Drupal);


