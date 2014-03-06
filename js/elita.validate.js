/**
 * Descripción de elita.validate
 *
 * @copyright       Copyright 2013 wroque
 * @author          Wladimir Roque <wladimir.roque@gmail.com>
 * @description     validación de formularios, mediante el evento submit.
 * @version         v0.2 Beta
 */

var elita = {
    version: '0.2'
};

(function(e, d) {

    e.prototype = {
        closest: function(elem, selector) {
            var matchesSelector = elem.matches || elem.webkitMatchesSelector || elem.mozMatchesSelector || elem.msMatchesSelector;
            while (elem) {
                if (matchesSelector.bind(elem)(selector)) {
                    return elem;
                } else {
                    elem = elem.parentNode;
                }
            }
            return false;
        },
        merge: function(a, b) {
            for (var key in a) {
                b[key] = b[key] ? (b[key][this.maxkey(b) + 1] = a[key], b[key]) : a[key];
            }
            return b;
        },
        maxkey: function(b) {
            var max;
            for (var key in b) {
                max = key;
            }
            return max;
        },
        replaceAll: function(str, subject, replace) {
            while (str.toString().indexOf(subject) !== -1) {
                str = str.toString().replace(subject, replace);
            }
            return str;
        },
        in_array: function(search, array) {
            for (var i = 0; i <= array.length; i++) {
                if (array[i] === search) {
                    return true;
                }
            }
            return false;
        },
        required: function(elem, arg) {
            var str = elem.value.replace(/^\s+|\s+$/g, '');
            if ((str.length > 0) === arg) {
                return true;
            }
            return false;
        },
        email: function(elem, arg) {
            var email = /^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$/;
            if (email.test(elem.value) === arg) {
                return true;
            }
            return false;
        },
        numb: function(elem, arg) {
            var numb = /^(-)?[0-9]*$/;
            if (numb.test(elem.value) === arg) {
                return true;
            }
            return false;
        },
        rut: function(elem, arg) {
            var M = 0, S = 1;
            var rut = elem.value.split('-');
            rut[0] = this.replaceAll(rut[0], '.', '');
            for (; rut[0]; rut[0] = Math.floor(rut[0] / 10)) {
                S = (S + rut[0] % 10 * (9 - M++ % 6)) % 11;
            }
            S = S ? S - 1 : 'k';
            if (rut[1] === String(S) && elem.value.length > 7) {
                return true;
            }
            return false;
        },
        equal: function(elem, equal) {
            if (elem.value === document.getElementsByName(equal)[0].value) {
                return true;
            }
            return false;
        },
        min: function(elem, min) {
            if (min <= elem.value.length) {
                return true;
            }
            return false;
        }
    };

    e.validate = function(options) {

        options = e.prototype.merge({
            form: '#form',
            alert: '#alert',
            class: {
                error: 'error',
                success: 'success',
                help: 'help-inline',
                alert: 'alert alert-error'
            },
            messages: {
                alert: 'No se han cumplido las validaciones.',
                required: 'Este campo es requerido.',
                email: 'Este email no es valido.',
                numb: 'Este campo debe ser numerico.',
                rut: 'Este R.U.T. no es valido.',
                equal: 'Fallo la confirmacion, vuelva intentarlo.',
                min: 'El valor minimo para este campo es de %.'
            }
        }, options);

        var alert = function(form) {
            var alert = d.querySelector(options.alert);
            if (form.getElementsByClassName(options.class.error).length > 0) {
                if (alert !== null) {
                    alert.setAttribute('style', 'display:block');
                    var button = document.createElement('button');
                    alert.setAttribute('class', options.class.alert);
                    alert.textContent = options.messages.alert;
                    button.setAttribute('class', 'close');
                    button.setAttribute('type', 'button');
                    button.textContent = '×';
                    button.addEventListener('click', function() {
                        alert.setAttribute('style', 'display:none');
                    });
                    alert.appendChild(button);
                }
                return true;
            } else {
                if (alert !== null) {
                    alert.setAttribute('style', 'display:none');
                }
                return false;
            }
        },
                message = function(elem, name) {
                    /* bootstrap 2
                     var parent = e.prototype.closest(elem, '.controls');
                     */
                    var parent = elem.parentNode;
                    var help = parent.getElementsByClassName(options.class.help)[0];
                    if (typeof name === 'string') {
                        if (help === undefined) {
                            help = d.createElement('span');
                            help.setAttribute('class', options.class.help);
                            parent.appendChild(help);
                        }
                        help.textContent = options.messages[name].replace('%', options.data[elem.name][name]);
                        style(elem, 'error');
                    } else {
                        if (help !== undefined) {
                            parent.removeChild(help);
                        }
                        style(elem, 'success');
                    }
                },
                style = function(elem, name) {
                    /* bootstrap 2
                     var parent = e.prototype.closest(elem, '.control-group');
                     parent.setAttribute('class', 'control-group ' + options.class[name]);
                     */
                    var parent = elem.parentNode;
                    parent.setAttribute('class', options.class[name]);
                },
                check = function(elem, rules) {
                    if (typeof rules === 'string') {
                        rules = JSON.parse('{"' + rules + '": true }');
                    }
                    if (rules.required === false) {
                        if (e.prototype.required(elem, false)) {
                            message(elem, false);
                            return;
                        }
                    }
                    for (var i in rules) {
                        if (e.prototype[i](elem, rules[i])) {
                            message(elem, false);
                        } else {
                            if (rules[i] !== false) {
                                message(elem, i);
                                return;
                            }
                        }
                    }
                };

        var form = d.querySelector(options.form);

        if (form !== null) {

            form.setAttribute('novalidate', 'true');

            for (var i in options.data) {
                form[i].addEventListener('blur', function() {
                    check(this, options.data[this.name]);
                    alert(form);
                });
            }

            form.addEventListener('submit', function(event) {
                for (var i in options.data) {
                    check(this[i], options.data[i]);
                }
                if (alert(form)) {
                    event.preventDefault();
                }
            });
        }

    };

}(elita, document));
