/*! jQuery Migrate v1.1.1 | (c) 2005, 2013 jQuery Foundation, Inc. and other contributors | jquery.org/license */
jQuery.migrateMute === void 0 && (jQuery.migrateMute = !0),
function (e, t, n) {
    function r(n) {
        o[n] || (o[n] = !0, e.migrateWarnings.push(n), t.console && console.warn && !e.migrateMute && (console.warn("JQMIGRATE: " + n), e.migrateTrace && console.trace && console.trace()))
    }

    function a(t, a, o, i) {
        if (Object.defineProperty) try {
            return Object.defineProperty(t, a, {
                configurable: !0,
                enumerable: !0,
                get: function () {
                    return r(i), o
                },
                set: function (e) {
                    r(i), o = e
                }
            }), n
        } catch (s) {}
        e._definePropertyBroken = !0, t[a] = o
    }
    var o = {};
    e.migrateWarnings = [], !e.migrateMute && t.console && console.log && console.log("JQMIGRATE: Logging is active"), e.migrateTrace === n && (e.migrateTrace = !0), e.migrateReset = function () {
        o = {}, e.migrateWarnings.length = 0
    }, "BackCompat" === document.compatMode && r("jQuery is not compatible with Quirks Mode");
    var i = e("<input/>", {
        size: 1
    }).attr("size") && e.attrFn,
        s = e.attr,
        u = e.attrHooks.value && e.attrHooks.value.get || function () {
            return null
        }, c = e.attrHooks.value && e.attrHooks.value.set || function () {
            return n
        }, l = /^(?:input|button)$/i,
        d = /^[238]$/,
        p = /^(?:autofocus|autoplay|async|checked|controls|defer|disabled|hidden|loop|multiple|open|readonly|required|scoped|selected)$/i,
        f = /^(?:checked|selected)$/i;
    a(e, "attrFn", i || {}, "jQuery.attrFn is deprecated"), e.attr = function (t, a, o, u) {
        var c = a.toLowerCase(),
            g = t && t.nodeType;
        return u && (4 > s.length && r("jQuery.fn.attr( props, pass ) is deprecated"), t && !d.test(g) && (i ? a in i : e.isFunction(e.fn[a]))) ? e(t)[a](o) : ("type" === a && o !== n && l.test(t.nodeName) && t.parentNode && r("Can't change the 'type' of an input or button in IE 6/7/8"), !e.attrHooks[c] && p.test(c) && (e.attrHooks[c] = {
            get: function (t, r) {
                var a, o = e.prop(t, r);
                return o === !0 || "boolean" != typeof o && (a = t.getAttributeNode(r)) && a.nodeValue !== !1 ? r.toLowerCase() : n
            },
            set: function (t, n, r) {
                var a;
                return n === !1 ? e.removeAttr(t, r) : (a = e.propFix[r] || r, a in t && (t[a] = !0), t.setAttribute(r, r.toLowerCase())), r
            }
        }, f.test(c) && r("jQuery.fn.attr('" + c + "') may use property instead of attribute")), s.call(e, t, a, o))
    }, e.attrHooks.value = {
        get: function (e, t) {
            var n = (e.nodeName || "").toLowerCase();
            return "button" === n ? u.apply(this, arguments) : ("input" !== n && "option" !== n && r("jQuery.fn.attr('value') no longer gets properties"), t in e ? e.value : null)
        },
        set: function (e, t) {
            var a = (e.nodeName || "").toLowerCase();
            return "button" === a ? c.apply(this, arguments) : ("input" !== a && "option" !== a && r("jQuery.fn.attr('value', val) no longer sets properties"), e.value = t, n)
        }
    };
    var g, h, v = e.fn.init,
        m = e.parseJSON,
        y = /^(?:[^<]*(<[\w\W]+>)[^>]*|#([\w\-]*))$/;
    e.fn.init = function (t, n, a) {
        var o;
        return t && "string" == typeof t && !e.isPlainObject(n) && (o = y.exec(t)) && o[1] && ("<" !== t.charAt(0) && r("$(html) HTML strings must start with '<' character"), n && n.context && (n = n.context), e.parseHTML) ? v.call(this, e.parseHTML(e.trim(t), n, !0), n, a) : v.apply(this, arguments)
    }, e.fn.init.prototype = e.fn, e.parseJSON = function (e) {
        return e || null === e ? m.apply(this, arguments) : (r("jQuery.parseJSON requires a valid JSON string"), null)
    }, e.uaMatch = function (e) {
        e = e.toLowerCase();
        var t = /(chrome)[ \/]([\w.]+)/.exec(e) || /(webkit)[ \/]([\w.]+)/.exec(e) || /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(e) || /(msie) ([\w.]+)/.exec(e) || 0 > e.indexOf("compatible") && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(e) || [];
        return {
            browser: t[1] || "",
            version: t[2] || "0"
        }
    }, e.browser || (g = e.uaMatch(navigator.userAgent), h = {}, g.browser && (h[g.browser] = !0, h.version = g.version), h.chrome ? h.webkit = !0 : h.webkit && (h.safari = !0), e.browser = h), a(e, "browser", e.browser, "jQuery.browser is deprecated"), e.sub = function () {
        function t(e, n) {
            return new t.fn.init(e, n)
        }
        e.extend(!0, t, this), t.superclass = this, t.fn = t.prototype = this(), t.fn.constructor = t, t.sub = this.sub, t.fn.init = function (r, a) {
            return a && a instanceof e && !(a instanceof t) && (a = t(a)), e.fn.init.call(this, r, a, n)
        }, t.fn.init.prototype = t.fn;
        var n = t(document);
        return r("jQuery.sub() is deprecated"), t
    }, e.ajaxSetup({
        converters: {
            "text json": e.parseJSON
        }
    });
    var b = e.fn.data;
    e.fn.data = function (t) {
        var a, o, i = this[0];
        return !i || "events" !== t || 1 !== arguments.length || (a = e.data(i, t), o = e._data(i, t), a !== n && a !== o || o === n) ? b.apply(this, arguments) : (r("Use of jQuery.fn.data('events') is deprecated"), o)
    };
    var j = /\/(java|ecma)script/i,
        w = e.fn.andSelf || e.fn.addBack;
    e.fn.andSelf = function () {
        return r("jQuery.fn.andSelf() replaced by jQuery.fn.addBack()"), w.apply(this, arguments)
    }, e.clean || (e.clean = function (t, a, o, i) {
        a = a || document, a = !a.nodeType && a[0] || a, a = a.ownerDocument || a, r("jQuery.clean() is deprecated");
        var s, u, c, l, d = [];
        if (e.merge(d, e.buildFragment(t, a).childNodes), o)
            for (c = function (e) {
                return !e.type || j.test(e.type) ? i ? i.push(e.parentNode ? e.parentNode.removeChild(e) : e) : o.appendChild(e) : n
            }, s = 0; null != (u = d[s]); s++) e.nodeName(u, "script") && c(u) || (o.appendChild(u), u.getElementsByTagName !== n && (l = e.grep(e.merge([], u.getElementsByTagName("script")), c), d.splice.apply(d, [s + 1, 0].concat(l)), s += l.length));
        return d
    });
    var Q = e.event.add,
        x = e.event.remove,
        k = e.event.trigger,
        N = e.fn.toggle,
        C = e.fn.live,
        S = e.fn.die,
        T = "ajaxStart|ajaxStop|ajaxSend|ajaxComplete|ajaxError|ajaxSuccess",
        M = RegExp("\\b(?:" + T + ")\\b"),
        H = /(?:^|\s)hover(\.\S+|)\b/,
        A = function (t) {
            return "string" != typeof t || e.event.special.hover ? t : (H.test(t) && r("'hover' pseudo-event is deprecated, use 'mouseenter mouseleave'"), t && t.replace(H, "mouseenter$1 mouseleave$1"))
        };
    e.event.props && "attrChange" !== e.event.props[0] && e.event.props.unshift("attrChange", "attrName", "relatedNode", "srcElement"), e.event.dispatch && a(e.event, "handle", e.event.dispatch, "jQuery.event.handle is undocumented and deprecated"), e.event.add = function (e, t, n, a, o) {
        e !== document && M.test(t) && r("AJAX events should be attached to document: " + t), Q.call(this, e, A(t || ""), n, a, o)
    }, e.event.remove = function (e, t, n, r, a) {
        x.call(this, e, A(t) || "", n, r, a)
    }, e.fn.error = function () {
        var e = Array.prototype.slice.call(arguments, 0);
        return r("jQuery.fn.error() is deprecated"), e.splice(0, 0, "error"), arguments.length ? this.bind.apply(this, e) : (this.triggerHandler.apply(this, e), this)
    }, e.fn.toggle = function (t, n) {
        if (!e.isFunction(t) || !e.isFunction(n)) return N.apply(this, arguments);
        r("jQuery.fn.toggle(handler, handler...) is deprecated");
        var a = arguments,
            o = t.guid || e.guid++,
            i = 0,
            s = function (n) {
                var r = (e._data(this, "lastToggle" + t.guid) || 0) % i;
                return e._data(this, "lastToggle" + t.guid, r + 1), n.preventDefault(), a[r].apply(this, arguments) || !1
            };
        for (s.guid = o; a.length > i;) a[i++].guid = o;
        return this.click(s)
    }, e.fn.live = function (t, n, a) {
        return r("jQuery.fn.live() is deprecated"), C ? C.apply(this, arguments) : (e(this.context).on(t, this.selector, n, a), this)
    }, e.fn.die = function (t, n) {
        return r("jQuery.fn.die() is deprecated"), S ? S.apply(this, arguments) : (e(this.context).off(t, this.selector || "**", n), this)
    }, e.event.trigger = function (e, t, n, a) {
        return n || M.test(e) || r("Global events are undocumented and deprecated"), k.call(this, e, t, n || document, a)
    }, e.each(T.split("|"), function (t, n) {
        e.event.special[n] = {
            setup: function () {
                var t = this;
                return t !== document && (e.event.add(document, n + "." + e.guid, function () {
                    e.event.trigger(n, null, t, !0)
                }), e._data(this, n, e.guid++)), !1
            },
            teardown: function () {
                return this !== document && e.event.remove(document, n + "." + e._data(this, n)), !1
            }
        }
    })
}(jQuery, window);

/*
 jQuery Tools dev - The missing UI library for the Web

 dateinput/dateinput.js
 overlay/overlay.js
 overlay/overlay.apple.js
 rangeinput/rangeinput.js
 scrollable/scrollable.js
 scrollable/scrollable.autoscroll.js
 scrollable/scrollable.navigator.js
 tabs/tabs.js
 toolbox/toolbox.expose.js
 toolbox/toolbox.history.js
 toolbox/toolbox.mousewheel.js
 tooltip/tooltip.js
 tooltip/tooltip.slide.js

 NO COPYRIGHTS OR LICENSES. DO WHAT YOU LIKE.

 http://flowplayer.org/tools/

 jquery.event.wheel.js - rev 1
 Copyright (c) 2008, Three Dub Media (http://threedubmedia.com)
 Liscensed under the MIT License (MIT-LICENSE.txt)
 http://www.opensource.org/licenses/mit-license.php
 Created: 2008-07-01 | Updated: 2008-07-14

 -----

*/
(function (a, u) {
    function n(a, b) {
        a = "" + a;
        for (b = b || 2; a.length < b;) a = "0" + a;
        return a
    }

    function f(a, b, c, d) {
        var g = b.getDate(),
            e = b.getDay(),
            k = b.getMonth(),
            f = b.getFullYear(),
            g = {
                d: g,
                dd: n(g),
                ddd: m[d].shortDays[e],
                dddd: m[d].days[e],
                m: k + 1,
                mm: n(k + 1),
                mmm: m[d].shortMonths[k],
                mmmm: m[d].months[k],
                yy: String(f).slice(2),
                yyyy: f
            };
        a = h[a](c, b, g, d);
        return q.html(a).html()
    }

    function d(a) {
        return parseInt(a, 10)
    }

    function c(a, b) {
        return a.getFullYear() === b.getFullYear() && a.getMonth() == b.getMonth() && a.getDate() == b.getDate()
    }

    function b(a) {
        if (a !==
            u) {
            if (a.constructor == Date) return a;
            if ("string" == typeof a) {
                var b = a.split("-");
                if (3 == b.length) return new Date(d(b[0]), d(b[1]) - 1, d(b[2]));
                if (!/^-?\d+$/.test(a)) return;
                a = d(a)
            }
            b = new Date;
            b.setDate(b.getDate() + a);
            return b
        }
    }

    function e(e, h) {
        function k(b, c, d) {
            e.attr("readonly") ? l.hide(d) : (C = b, K = b.getFullYear(), M = b.getMonth(), L = b.getDate(), d || (d = a.Event("api")), "click" != d.type || a.browser.msie || e.focus(), d.type = "beforeChange", N.trigger(d, [b]), d.isDefaultPrevented() || (e.val(f(c.formatter, b, c.format, c.lang)),
                d.type = "change", N.trigger(d), e.data("date", b), l.hide(d)))
        }

        function v(b) {
            b.type = "onShow";
            N.trigger(b);
            a(document).on("keydown.d", function (b) {
                if (b.ctrlKey) return !0;
                var c = b.keyCode;
                if (8 == c || 46 == c) return e.val(""), l.hide(b);
                if (27 == c || 9 == c) return l.hide(b);
                if (0 <= a(r).index(c)) {
                    if (!F) return l.show(b), b.preventDefault();
                    var d = a("#" + p.weeks + " a"),
                        h = a("." + p.focus),
                        g = d.index(h);
                    h.removeClass(p.focus);
                    if (74 == c || 40 == c) g += 7;
                    else if (75 == c || 38 == c) g -= 7;
                    else if (76 == c || 39 == c) g += 1;
                    else if (72 == c || 37 == c) g -= 1;
                    41 < g ? (l.addMonth(),
                        h = a("#" + p.weeks + " a:eq(" + (g - 42) + ")")) : 0 > g ? (l.addMonth(-1), h = a("#" + p.weeks + " a:eq(" + (g + 42) + ")")) : h = d.eq(g);
                    h.addClass(p.focus);
                    return b.preventDefault()
                }
                if (34 == c) return l.addMonth();
                if (33 == c) return l.addMonth(-1);
                if (36 == c) return l.today();
                13 == c && (a(b.target).is("select") || a("." + p.focus).click());
                return 0 <= a([16, 17, 18, 9]).index(c)
            });
            a(document).on("click.d", function (b) {
                var c = b.target;
                c.id == p.root || a(c).parents("#" + p.root).length || (c == e[0] || x && c == x[0]) || l.hide(b)
            })
        }
        var l = this,
            q = new Date,
            t = q.getFullYear(),
            p = h.css,
            n = m[h.lang],
            w = a("#" + p.root),
            E = w.find("#" + p.title),
            x, G, H, K, M, L, C = e.attr("data-value") || h.value || e.val(),
            z = e.attr("min") || h.min,
            D = e.attr("max") || h.max,
            F, O;
        0 === z && (z = "0");
        C = b(C) || q;
        z = b(z || new Date(t + h.yearRange[0], 1, 1));
        D = b(D || new Date(t + h.yearRange[1] + 1, 1, -1));
        if (!n) throw "Dateinput: invalid language: " + h.lang;
        "date" == e.attr("type") && (O = e.clone(), t = O.wrap("<div/>").parent().html(), t = a(t.replace(/type/i, "type=text data-orig-type")), h.value && t.val(h.value), e.replaceWith(t), e = t);
        e.addClass(p.input);
        var N = e.add(l);
        if (!w.length) {
            w = a("<div><div><a/><div/><a/></div><div><div/><div/></div></div>").hide().css({
                position: "absolute"
            }).attr("id", p.root);
            w.children().eq(0).attr("id", p.head).end().eq(1).attr("id", p.body).children().eq(0).attr("id", p.days).end().eq(1).attr("id", p.weeks).end().end().end().find("a").eq(0).attr("id", p.prev).end().eq(1).attr("id", p.next);
            E = w.find("#" + p.head).find("div").attr("id", p.title);
            if (h.selectors) {
                var I = a("<select/>").attr("id", p.month),
                    J = a("<select/>").attr("id", p.year);
                E.html(I.add(J))
            }
            for (var t = w.find("#" + p.days), R = 0; 7 > R; R++) t.append(a("<span/>").text(n.shortDays[(R + h.firstDay) % 7]));
            a("body").append(w)
        }
        h.trigger && (x = a("<a/>").attr("href", "#").addClass(p.trigger).click(function (a) {
            h.toggle ? l.toggle() : l.show();
            return a.preventDefault()
        }).insertAfter(e));
        var P = w.find("#" + p.weeks),
            J = w.find("#" + p.year),
            I = w.find("#" + p.month);
        a.extend(l, {
            show: function (b) {
                if (!e.attr("disabled") && !F && (b = a.Event(), b.type = "onBeforeShow", N.trigger(b), !b.isDefaultPrevented())) {
                    a.each(g, function () {
                        this.hide()
                    });
                    F = !0;
                    I.off("change").change(function () {
                        l.setValue(d(J.val()), d(a(this).val()))
                    });
                    J.off("change").change(function () {
                        l.setValue(d(a(this).val()), d(I.val()))
                    });
                    G = w.find("#" + p.prev).off("click").click(function (a) {
                        G.hasClass(p.disabled) || l.addMonth(-1);
                        return !1
                    });
                    H = w.find("#" + p.next).off("click").click(function (a) {
                        H.hasClass(p.disabled) || l.addMonth();
                        return !1
                    });
                    l.setValue(C);
                    var c = e.offset();
                    /iPad/i.test(navigator.userAgent) && (c.top -= a(window).scrollTop());
                    w.css({
                        top: c.top + e.outerHeight(!0) + h.offset[0],
                        left: c.left + h.offset[1]
                    });
                    h.speed ? w.show(h.speed, function () {
                        v(b)
                    }) : (w.show(), v(b));
                    return l
                }
            },
            setValue: function (g, e, v) {
                var f = -1 <= d(e) ? new Date(d(g), d(e), d(v == u || isNaN(v) ? 1 : v)) : g || C;
                f < z ? f = z : f > D && (f = D);
                "string" == typeof g && (f = b(g));
                g = f.getFullYear();
                e = f.getMonth();
                v = f.getDate(); - 1 == e ? (e = 11, g--) : 12 == e && (e = 0, g++);
                if (!F) return k(f, h), l;
                M = e;
                K = g;
                L = v;
                v = (new Date(g, e, 1 - h.firstDay)).getDay();
                var s = (new Date(g, e + 1, 0)).getDate(),
                    r = (new Date(g, e - 1 + 1, 0)).getDate(),
                    t;
                if (h.selectors) {
                    I.empty();
                    a.each(n.months, function (b,
                        c) {
                        z < new Date(g, b + 1, 1) && D > new Date(g, b, 0) && I.append(a("<option/>").html(c).attr("value", b))
                    });
                    J.empty();
                    for (var f = q.getFullYear(), m = f + h.yearRange[0]; m < f + h.yearRange[1]; m++) z < new Date(m + 1, 0, 1) && D > new Date(m, 0, 0) && J.append(a("<option/>").text(m));
                    I.val(e);
                    J.val(g)
                } else E.html(n.months[e] + " " + g);
                P.empty();
                G.add(H).removeClass(p.disabled);
                for (var m = v ? 0 : -7, w, x; m < (v ? 42 : 35); m++) w = a("<a/>"), 0 === m % 7 && (t = a("<div/>").addClass(p.week), P.append(t)), m < v ? (w.addClass(p.off), x = r - v + m + 1, f = new Date(g, e - 1, x)) : m >= v + s ?
                    (w.addClass(p.off), x = m - s - v + 1, f = new Date(g, e + 1, x)) : (x = m - v + 1, f = new Date(g, e, x), c(C, f) ? w.attr("id", p.current).addClass(p.focus) : c(q, f) && w.attr("id", p.today)), z && f < z && w.add(G).addClass(p.disabled), D && f > D && w.add(H).addClass(p.disabled), w.attr("href", "#" + x).text(x).data("date", f), t.append(w);
                P.find("a").click(function (b) {
                    var c = a(this);
                    c.hasClass(p.disabled) || (a("#" + p.current).removeAttr("id"), c.attr("id", p.current), k(c.data("date"), h, b));
                    return !1
                });
                p.sunday && P.find("." + p.week).each(function () {
                    var b = h.firstDay ?
                        7 - h.firstDay : 0;
                    a(this).children().slice(b, b + 1).addClass(p.sunday)
                });
                return l
            },
            setMin: function (a, c) {
                z = b(a);
                c && C < z && l.setValue(z);
                return l
            },
            setMax: function (a, c) {
                D = b(a);
                c && C > D && l.setValue(D);
                return l
            },
            today: function () {
                return l.setValue(q)
            },
            addDay: function (a) {
                return this.setValue(K, M, L + (a || 1))
            },
            addMonth: function (a) {
                a = M + (a || 1);
                var b = (new Date(K, a + 1, 0)).getDate();
                return this.setValue(K, a, L <= b ? L : b)
            },
            addYear: function (a) {
                return this.setValue(K + (a || 1), M, L)
            },
            destroy: function () {
                e.add(document).off("click.d keydown.d");
                w.add(x).remove();
                e.removeData("dateinput").removeClass(p.input);
                O && e.replaceWith(O)
            },
            hide: function (b) {
                if (F) {
                    b = a.Event();
                    b.type = "onHide";
                    N.trigger(b);
                    if (b.isDefaultPrevented()) return;
                    a(document).off("click.d keydown.d");
                    w.hide();
                    F = !1
                }
                return l
            },
            toggle: function () {
                return l.isOpen() ? l.hide() : l.show()
            },
            getConf: function () {
                return h
            },
            getInput: function () {
                return e
            },
            getCalendar: function () {
                return w
            },
            getValue: function (a) {
                return a ? f(h.formatter, C, a, h.lang) : C
            },
            isOpen: function () {
                return F
            }
        });
        a.each(["onBeforeShow",
            "onShow", "change", "onHide"
        ], function (b, c) {
            if (a.isFunction(h[c])) a(l).on(c, h[c]);
            l[c] = function (b) {
                if (b) a(l).on(c, b);
                return l
            }
        });
        h.editable || e.on("focus.d click.d", l.show).keydown(function (b) {
            var c = b.keyCode;
            if (!F && 0 <= a(r).index(c)) return l.show(b), b.preventDefault();
            8 != c && 46 != c || e.val("");
            return b.shiftKey || b.ctrlKey || b.altKey || 9 == c ? !0 : b.preventDefault()
        });
        b(e.val()) && k(C, h)
    }
    a.tools = a.tools || {
        version: "1.2.8-dev"
    };
    var g = [],
        h = {}, k, r = [75, 76, 38, 39, 74, 72, 40, 37],
        m = {};
    k = a.tools.dateinput = {
        conf: {
            format: "mm/dd/yy",
            formatter: "default",
            selectors: !1,
            yearRange: [-5, 5],
            lang: "en",
            offset: [0, 0],
            speed: 0,
            firstDay: 0,
            min: u,
            max: u,
            trigger: 0,
            toggle: 0,
            editable: 0,
            css: {
                prefix: "cal",
                input: "date",
                root: 0,
                head: 0,
                title: 0,
                prev: 0,
                next: 0,
                month: 0,
                year: 0,
                days: 0,
                body: 0,
                weeks: 0,
                today: 0,
                current: 0,
                week: 0,
                off: 0,
                sunday: 0,
                focus: 0,
                disabled: 0,
                trigger: 0
            }
        },
        addFormatter: function (a, b) {
            h[a] = b
        },
        localize: function (b, c) {
            a.each(c, function (a, b) {
                c[a] = b.split(",")
            });
            m[b] = c
        }
    };
    k.localize("en", {
        months: "January,February,March,April,May,June,July,August,September,October,November,December",
        shortMonths: "Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec",
        days: "Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday",
        shortDays: "Sun,Mon,Tue,Wed,Thu,Fri,Sat"
    });
    var q = a("<a/>");
    k.addFormatter("default", function (a, b, c, d) {
        return a.replace(/d{1,4}|m{1,4}|yy(?:yy)?|"[^"]*"|'[^']*'/g, function (a) {
            return a in c ? c[a] : a
        })
    });
    k.addFormatter("prefixed", function (a, b, c, d) {
        return a.replace(/%(d{1,4}|m{1,4}|yy(?:yy)?|"[^"]*"|'[^']*')/g, function (a, b) {
            return b in c ? c[b] : a
        })
    });
    a.expr[":"].date = function (b) {
        var c =
            b.getAttribute("type");
        return c && "date" == c || !! a(b).data("dateinput")
    };
    a.fn.dateinput = function (b) {
        if (this.data("dateinput")) return this;
        b = a.extend(!0, {}, k.conf, b);
        a.each(b.css, function (a, c) {
            c || "prefix" == a || (b.css[a] = (b.css.prefix || "") + (c || a))
        });
        var c;
        this.each(function () {
            var d = new e(a(this), b);
            g.push(d);
            d = d.getInput().data("dateinput", d);
            c = c ? c.add(d) : d
        });
        return c ? c : this
    }
})(jQuery);
(function (a) {
    function u(d, c) {
        var b = this,
            e = d.add(b),
            g = a(window),
            h, k, r, m = a.tools.expose && (c.mask || c.expose),
            q = Math.random().toString().slice(10);
        m && ("string" == typeof m && (m = {
            color: m
        }), m.closeOnClick = m.closeOnEsc = !1);
        var s = c.target || d.attr("rel");
        k = s ? a(s) : d;
        if (!k.length) throw "Could not find Overlay: " + s;
        d && -1 == d.index(k) && d.click(function (a) {
            b.load(a);
            return a.preventDefault()
        });
        a.extend(b, {
            load: function (d) {
                if (b.isOpened()) return b;
                var h = f[c.effect];
                if (!h) throw 'Overlay: cannot find effect : "' + c.effect +
                    '"';
                c.oneInstance && a.each(n, function () {
                    this.close(d)
                });
                d = d || a.Event();
                d.type = "onBeforeLoad";
                e.trigger(d);
                if (d.isDefaultPrevented()) return b;
                r = !0;
                m && a(k).expose(m);
                var v = c.top,
                    l = c.left,
                    s = k.outerWidth(!0),
                    t = k.outerHeight(!0);
                "string" == typeof v && (v = "center" == v ? Math.max((g.height() - t) / 2, 0) : parseInt(v, 10) / 100 * g.height());
                "center" == l && (l = Math.max((g.width() - s) / 2, 0));
                h[0].call(b, {
                    top: v,
                    left: l
                }, function () {
                    r && (d.type = "onLoad", e.trigger(d))
                });
                if (m && c.closeOnClick) a.mask.getMask().one("click", b.close);
                if (c.closeOnClick) a(document).on("click." +
                    q, function (c) {
                        a(c.target).parents(k).length || b.close(c)
                    });
                if (c.closeOnEsc) a(document).on("keydown." + q, function (a) {
                    27 == a.keyCode && b.close(a)
                });
                return b
            },
            close: function (d) {
                if (!b.isOpened()) return b;
                d = a.Event();
                d.type = "onBeforeClose";
                e.trigger(d);
                if (!d.isDefaultPrevented()) return r = !1, f[c.effect][1].call(b, function () {
                    d.type = "onClose";
                    e.trigger(d)
                }), a(document).off("click." + q + " keydown." + q), m && a.mask.close(), b
            },
            getOverlay: function () {
                return k
            },
            getTrigger: function () {
                return d
            },
            getClosers: function () {
                return h
            },
            isOpened: function () {
                return r
            },
            getConf: function () {
                return c
            }
        });
        a.each(["onBeforeLoad", "onStart", "onLoad", "onBeforeClose", "onClose"], function (d, g) {
            if (a.isFunction(c[g])) a(b).on(g, c[g]);
            b[g] = function (c) {
                if (c) a(b).on(g, c);
                return b
            }
        });
        h = k.find(c.close || ".close");
        h.length || c.close || (h = a('<a class="close"></a>'), k.prepend(h));
        h.click(function (a) {
            b.close(a)
        });
        c.load && b.load()
    }
    a.tools = a.tools || {
        version: "1.2.8-dev"
    };
    a.tools.overlay = {
        addEffect: function (a, c, b) {
            f[a] = [c, b]
        },
        conf: {
            close: null,
            closeOnClick: !0,
            closeOnEsc: !0,
            closeSpeed: "fast",
            effect: "default",
            fixed: !a.browser.msie || 6 < a.browser.version,
            left: "center",
            load: !1,
            mask: null,
            oneInstance: !0,
            speed: "normal",
            target: null,
            top: "10%"
        }
    };
    var n = [],
        f = {};
    a.tools.overlay.addEffect("default", function (d, c) {
        var b = this.getConf(),
            e = a(window);
        b.fixed || (d.top += e.scrollTop(), d.left += e.scrollLeft());
        d.position = b.fixed ? "fixed" : "absolute";
        this.getOverlay().css(d).fadeIn(b.speed, c)
    }, function (a) {
        this.getOverlay().fadeOut(this.getConf().closeSpeed, a)
    });
    a.fn.overlay = function (d) {
        var c = this.data("overlay");
        if (c) return c;
        a.isFunction(d) && (d = {
            onBeforeLoad: d
        });
        d = a.extend(!0, {}, a.tools.overlay.conf, d);
        this.each(function () {
            c = new u(a(this), d);
            n.push(c);
            a(this).data("overlay", c)
        });
        return d.api ? c : this
    }
})(jQuery);
(function (a) {
    function u(a) {
        var c = a.offset();
        return {
            top: c.top + a.height() / 2,
            left: c.left + a.width() / 2
        }
    }
    var n = a.tools.overlay,
        f = a(window);
    a.extend(n.conf, {
        start: {
            top: null,
            left: null
        },
        fadeInSpeed: "fast",
        zIndex: 9999
    });
    n.addEffect("apple", function (d, c) {
        var b = this.getOverlay(),
            e = this.getConf(),
            g = this.getTrigger(),
            h = this,
            k = b.outerWidth(!0),
            r = b.data("img"),
            m = e.fixed ? "fixed" : "absolute";
        if (!r) {
            r = b.css("backgroundImage");
            if (!r) throw "background-image CSS property not set for overlay";
            r = r.slice(r.indexOf("(") + 1, r.indexOf(")")).replace(/\"/g,
                "");
            b.css("backgroundImage", "none");
            r = a('<img src="' + r + '"/>');
            r.css({
                border: 0,
                display: "none"
            }).width(k);
            a("body").append(r);
            b.data("img", r)
        }
        var q = e.start.top || Math.round(f.height() / 2),
            s = e.start.left || Math.round(f.width() / 2);
        g && (g = u(g), q = g.top, s = g.left);
        e.fixed ? (q -= f.scrollTop(), s -= f.scrollLeft()) : (d.top += f.scrollTop(), d.left += f.scrollLeft());
        r.css({
            position: "absolute",
            top: q,
            left: s,
            width: 0,
            zIndex: e.zIndex
        }).show();
        d.position = m;
        b.css(d);
        r.animate({
            top: d.top,
            left: d.left,
            width: k
        }, e.speed, function () {
            b.css("zIndex",
                e.zIndex + 1).fadeIn(e.fadeInSpeed, function () {
                h.isOpened() && !a(this).index(b) ? c.call() : b.hide()
            })
        }).css("position", m)
    }, function (d) {
        var c = this.getOverlay().hide(),
            b = this.getConf(),
            e = this.getTrigger(),
            c = c.data("img"),
            g = {
                top: b.start.top,
                left: b.start.left,
                width: 0
            };
        e && a.extend(g, u(e));
        b.fixed && c.css({
            position: "absolute"
        }).animate({
            top: "+=" + f.scrollTop(),
            left: "+=" + f.scrollLeft()
        }, 0);
        c.animate(g, b.closeSpeed, d)
    })
})(jQuery);
(function (a) {
    function u(a, b) {
        var c = Math.pow(10, b);
        return Math.round(a * c) / c
    }

    function n(a, b) {
        var c = parseInt(a.css(b), 10);
        return c ? c : (c = a[0].currentStyle) && c.width && parseInt(c.width, 10)
    }

    function f(a) {
        return (a = a.data("events")) && a.onSlide
    }

    function d(b, c) {
        function d(a, e, f, l) {
            void 0 === f ? f = e / B * Q : l && (f -= c.min);
            w && (f = Math.round(f / w) * w);
            if (void 0 === e || w) e = f * B / Q;
            if (isNaN(f)) return q;
            e = Math.max(0, Math.min(e, B));
            f = e / B * Q;
            if (l || !A) f += c.min;
            A && (l ? e = B - e : f = c.max - f);
            f = u(f, E);
            var k = "click" == a.type;
            if (H && (void 0 !== v && !k) && (a.type = "onSlide", G.trigger(a, [f, e]), a.isDefaultPrevented())) return q;
            l = k ? c.speed : 0;
            k = k ? function () {
                a.type = "change";
                G.trigger(a, [f])
            } : null;
            A ? (t.animate({
                top: e
            }, l, k), c.progress && p.animate({
                height: B - e + t.height() / 2
            }, l)) : (t.animate({
                left: e
            }, l, k), c.progress && p.animate({
                width: e + t.width() / 2
            }, l));
            v = f;
            b.val(f);
            return q
        }

        function e() {
            (A = c.vertical || n(y, "height") > n(y, "width")) ? (B = n(y, "height") - n(t, "height"), l = y.offset().top + B) : (B = n(y, "width") - n(t, "width"), l = y.offset().left)
        }

        function m() {
            e();
            q.setValue(void 0 !==
                c.value ? c.value : c.min)
        }
        var q = this,
            s = c.css,
            y = a("<div><div/><a href='#'/></div>").data("rangeinput", q),
            A, v, l, B;
        b.before(y);
        var t = y.addClass(s.slider).find("a").addClass(s.handle),
            p = y.find("div").addClass(s.progress);
        a.each(["min", "max", "step", "value"], function (a, d) {
            var e = b.attr(d);
            parseFloat(e) && (c[d] = parseFloat(e, 10))
        });
        var Q = c.max - c.min,
            w = "any" == c.step ? 0 : c.step,
            E = c.precision;
        void 0 === E && (E = w.toString().split("."), E = 2 === E.length ? E[1].length : 0);
        if ("range" == b.attr("type")) {
            var x = b.clone().wrap("<div/>").parent().html(),
                x = a(x.replace(/type/i, "type=text data-orig-type"));
            x.val(c.value);
            b.replaceWith(x);
            b = x
        }
        b.addClass(s.input);
        var G = a(q).add(b),
            H = !0;
        a.extend(q, {
            getValue: function () {
                return v
            },
            setValue: function (b, c) {
                e();
                return d(c || a.Event("api"), void 0, b, !0)
            },
            getConf: function () {
                return c
            },
            getProgress: function () {
                return p
            },
            getHandle: function () {
                return t
            },
            getInput: function () {
                return b
            },
            step: function (b, d) {
                d = d || a.Event();
                q.setValue(v + ("any" == c.step ? 1 : c.step) * (b || 1), d)
            },
            stepUp: function (a) {
                return q.step(a || 1)
            },
            stepDown: function (a) {
                return q.step(-a || -1)
            }
        });
        a.each(["onSlide", "change"], function (b, d) {
            if (a.isFunction(c[d])) a(q).on(d, c[d]);
            q[d] = function (b) {
                if (b) a(q).on(d, b);
                return q
            }
        });
        t.drag({
            drag: !1
        }).on("dragStart", function () {
            e();
            H = f(a(q)) || f(b)
        }).on("drag", function (a, c, e) {
            if (b.is(":disabled")) return !1;
            d(a, A ? c : e)
        }).on("dragEnd", function (a) {
            a.isDefaultPrevented() || (a.type = "change", G.trigger(a, [v]))
        }).click(function (a) {
            return a.preventDefault()
        });
        y.click(function (a) {
            if (b.is(":disabled") || a.target == t[0]) return a.preventDefault();
            e();
            var c = A ? t.height() /
                2 : t.width() / 2;
            d(a, A ? B - l - c + a.pageY : a.pageX - l - c)
        });
        c.keyboard && b.keydown(function (c) {
            if (!b.attr("readonly")) {
                var d = c.keyCode,
                    e = -1 != a([75, 76, 38, 33, 39]).index(d),
                    h = -1 != a([74, 72, 40, 34, 37]).index(d);
                if ((e || h) && !(c.shiftKey || c.altKey || c.ctrlKey)) return e ? q.step(33 == d ? 10 : 1, c) : h && q.step(34 == d ? -10 : -1, c), c.preventDefault()
            }
        });
        b.blur(function (b) {
            var c = a(this).val();
            c !== v && q.setValue(c, b)
        });
        a.extend(b[0], {
            stepUp: q.stepUp,
            stepDown: q.stepDown
        });
        m();
        B || a(window).load(m)
    }
    a.tools = a.tools || {
        version: "1.2.8-dev"
    };
    var c;
    c = a.tools.rangeinput = {
        conf: {
            min: 0,
            max: 100,
            step: "any",
            steps: 0,
            value: 0,
            precision: void 0,
            vertical: 0,
            keyboard: !0,
            progress: !1,
            speed: 100,
            css: {
                input: "range",
                slider: "slider",
                progress: "progress",
                handle: "handle"
            }
        }
    };
    var b, e;
    a.fn.drag = function (c) {
        document.ondragstart = function () {
            return !1
        };
        c = a.extend({
            x: !0,
            y: !0,
            drag: !0
        }, c);
        b = b || a(document).on("mousedown mouseup", function (d) {
            var f = a(d.target);
            if ("mousedown" == d.type && f.data("drag")) {
                var r = f.position(),
                    m = d.pageX - r.left,
                    q = d.pageY - r.top,
                    s = !0;
                b.on("mousemove.drag", function (a) {
                    var b =
                        a.pageX - m;
                    a = a.pageY - q;
                    var d = {};
                    c.x && (d.left = b);
                    c.y && (d.top = a);
                    s && (f.trigger("dragStart"), s = !1);
                    c.drag && f.css(d);
                    f.trigger("drag", [a, b]);
                    e = f
                });
                d.preventDefault()
            } else try {
                e && e.trigger("dragEnd")
            } finally {
                b.off("mousemove.drag"), e = null
            }
        });
        return this.data("drag", !0)
    };
    a.expr[":"].range = function (b) {
        var c = b.getAttribute("type");
        return c && "range" == c || !! a(b).filter("input").data("rangeinput")
    };
    a.fn.rangeinput = function (b) {
        if (this.data("rangeinput")) return this;
        b = a.extend(!0, {}, c.conf, b);
        var e;
        this.each(function () {
            var c =
                new d(a(this), a.extend(!0, {}, b)),
                c = c.getInput().data("rangeinput", c);
            e = e ? e.add(c) : c
        });
        return e ? e : this
    }
})(jQuery);
(function (a) {
    function u(d, c) {
        var b = a(c);
        return 2 > b.length ? b : d.parent().find(c)
    }

    function n(d, c) {
        var b = this,
            e = d.add(b),
            g = d.children(),
            h = 0,
            k = c.vertical;
        f || (f = b);
        1 < g.length && (g = a(c.items, d));
        1 < c.size && (c.circular = !1);
        a.extend(b, {
            getConf: function () {
                return c
            },
            getIndex: function () {
                return h
            },
            getSize: function () {
                return b.getItems().size()
            },
            getNaviButtons: function () {
                return q.add(s)
            },
            getRoot: function () {
                return d
            },
            getItemWrap: function () {
                return g
            },
            getItems: function () {
                return g.find(c.item).not("." + c.clonedClass)
            },
            getCircularClones: function () {
                return g.find(c.item).filter("." + c.clonedClass)
            },
            move: function (a, c) {
                return b.seekTo(h + a, c)
            },
            next: function (a) {
                return b.move(c.size, a)
            },
            prev: function (a) {
                return b.move(-c.size, a)
            },
            begin: function (a) {
                return b.seekTo(0, a)
            },
            end: function (a) {
                return b.seekTo(b.getSize() - 1, a)
            },
            focus: function () {
                return f = b
            },
            addItem: function (d) {
                d = a(d);
                c.circular ? (g.children().last().before(d), b.getCircularClones().first().replaceWith(d.clone().addClass(c.clonedClass))) : (g.append(d), s.removeClass("disabled"));
                e.trigger("onAddItem", [d]);
                return b
            },
            removeItem: function (a) {
                e.trigger("onRemoveItem", [a]);
                var d = b.getItems(),
                    g;
                a.jquery ? b.getItems().index(g) : (g = 1 * a, a = b.getItems().eq(g));
                c.circular ? (a.remove(), d = b.getItems(), a = b.getCircularClones(), a.first().replaceWith(d.last().clone().addClass("cloned")), a.last().replaceWith(d.first().clone().addClass("cloned"))) : (a.remove(), b.getItems());
                h >= b.getSize() && (h -= 1, b.move(1));
                return b
            },
            seekTo: function (d, l, q) {
                d.jquery || (d *= 1);
                if (c.circular && 0 === d && -1 == h && 0 !== l || !c.circular &&
                    0 > d || d > b.getSize() || -1 > d) return b;
                var m = d;
                d.jquery ? d = b.getItems().index(d) : m = b.getItems().eq(d);
                var s = a.Event("onBeforeSeek");
                if (!q && (e.trigger(s, [d, l]), s.isDefaultPrevented() || !m.length)) return b;
                m = k ? {
                    top: -m.position().top
                } : {
                    left: -m.position().left
                };
                h = d;
                f = b;
                void 0 === l && (l = c.speed);
                g.animate(m, l, c.easing, q || function () {
                    e.trigger("onSeek", [d])
                });
                return b
            }
        });
        a.each(["onBeforeSeek", "onSeek", "onAddItem", "onRemoveItem"], function (d, e) {
            if (a.isFunction(c[e])) a(b).on(e, c[e]);
            b[e] = function (c) {
                if (c) a(b).on(e,
                    c);
                return b
            }
        });
        if (c.circular) {
            var r = b.getItems().slice(-1).clone().prependTo(g),
                m = b.getItems().eq(1).clone().appendTo(g);
            r.add(m).addClass(c.clonedClass);
            b.onBeforeSeek(function (a, c, d) {
                if (!a.isDefaultPrevented()) {
                    var e = b.getCircularClones();
                    if (-1 == c) return b.seekTo(e.first(), d, function () {
                        b.end(0)
                    }), a.preventDefault();
                    c == b.getSize() && b.seekTo(e.last(), d, function () {
                        b.begin(0)
                    })
                }
            });
            r = d.parents().add(d).filter(function () {
                if ("none" === a(this).css("display")) return !0
            });
            r.length ? (r.show(), b.seekTo(0, 0, function () {}),
                r.hide()) : b.seekTo(0, 0, function () {})
        }
        var q = u(d, c.prev).click(function (a) {
            a.stopPropagation();
            b.prev()
        }),
            s = u(d, c.next).click(function (a) {
                a.stopPropagation();
                b.next()
            });
        c.circular || (b.onBeforeSeek(function (a, d) {
            setTimeout(function () {
                a.isDefaultPrevented() || (q.toggleClass(c.disabledClass, 0 >= d), s.toggleClass(c.disabledClass, d >= b.getSize() - 1))
            }, 1)
        }), c.initialIndex || q.addClass(c.disabledClass));
        2 > b.getSize() && q.add(s).addClass(c.disabledClass);
        c.mousewheel && a.fn.mousewheel && d.mousewheel(function (a, d) {
            if (c.mousewheel) return b.move(0 >
                d ? 1 : -1, c.wheelSpeed || 50), !1
        });
        if (c.touch) {
            var n, A;
            g[0].ontouchstart = function (a) {
                a = a.touches[0];
                n = a.clientX;
                A = a.clientY
            };
            g[0].ontouchmove = function (a) {
                if (1 == a.touches.length && !g.is(":animated")) {
                    var c = a.touches[0],
                        d = n - c.clientX,
                        c = A - c.clientY;
                    b[k && 0 < c || !k && 0 < d ? "next" : "prev"]();
                    a.preventDefault()
                }
            }
        }
        if (c.keyboard) a(document).on("keydown.scrollable", function (d) {
            if (!(!c.keyboard || (d.altKey || d.ctrlKey || d.metaKey || a(d.target).is(":input")) || "static" != c.keyboard && f != b)) {
                var e = d.keyCode;
                if (k && (38 == e || 40 == e)) return b.move(38 ==
                    e ? -1 : 1), d.preventDefault();
                if (!k && (37 == e || 39 == e)) return b.move(37 == e ? -1 : 1), d.preventDefault()
            }
        });
        c.initialIndex && b.seekTo(c.initialIndex, 0, function () {})
    }
    a.tools = a.tools || {
        version: "1.2.8-dev"
    };
    a.tools.scrollable = {
        conf: {
            activeClass: "active",
            circular: !1,
            clonedClass: "cloned",
            disabledClass: "disabled",
            easing: "swing",
            initialIndex: 0,
            item: "> *",
            items: ".items",
            keyboard: !0,
            mousewheel: !1,
            next: ".next",
            prev: ".prev",
            size: 1,
            speed: 400,
            vertical: !1,
            touch: !0,
            wheelSpeed: 0
        }
    };
    var f;
    a.fn.scrollable = function (d) {
        var c = this.data("scrollable");
        if (c) return c;
        d = a.extend({}, a.tools.scrollable.conf, d);
        this.each(function () {
            c = new n(a(this), d);
            a(this).data("scrollable", c)
        });
        return d.api ? c : this
    }
})(jQuery);
(function (a) {
    var u = a.tools.scrollable;
    u.autoscroll = {
        conf: {
            autoplay: !0,
            interval: 3E3,
            autopause: !0
        }
    };
    a.fn.autoscroll = function (n) {
        "number" == typeof n && (n = {
            interval: n
        });
        var f = a.extend({}, u.autoscroll.conf, n),
            d;
        this.each(function () {
            function c() {
                g && clearTimeout(g);
                g = setTimeout(function () {
                    b.next()
                }, f.interval)
            }
            var b = a(this).data("scrollable"),
                e = b.getRoot(),
                g, h = !1;
            b && (d = b);
            b.play = function () {
                g || (h = !1, e.on("onSeek", c), c())
            };
            b.hoverPlay = function () {
                h || b.play()
            };
            b.pause = function () {
                g = clearTimeout(g);
                e.off("onSeek",
                    c)
            };
            b.resume = function () {
                h || b.play()
            };
            b.stop = function () {
                h = !0;
                b.pause()
            };
            f.autopause && e.add(b.getNaviButtons()).hover(b.pause, b.resume);
            f.autoplay && b.play();
            b.onRemoveItem(function (a, c) {
                2 >= b.getSize() && b.stop()
            })
        });
        return f.api ? d : this
    }
})(jQuery);
(function (a) {
    function u(f, d) {
        var c = a(d);
        return 2 > c.length ? c : f.parent().find(d)
    }
    var n = a.tools.scrollable;
    n.navigator = {
        conf: {
            navi: ".navi",
            naviItem: null,
            activeClass: "active",
            indexed: !1,
            idPrefix: null,
            history: !1
        }
    };
    a.fn.navigator = function (f) {
        "string" == typeof f && (f = {
            navi: f
        });
        f = a.extend({}, n.navigator.conf, f);
        var d;
        this.each(function () {
            function c() {
                return g.find(f.naviItem || "> *")
            }

            function b(b) {
                var c = a("<" + (f.naviItem || "a") + "/>").click(function (c) {
                    a(this);
                    e.seekTo(b);
                    c.preventDefault();
                    n && history.pushState({
                            i: b
                        },
                        "")
                });
                0 === b && c.addClass(k);
                f.indexed && c.text(b + 1);
                f.idPrefix && c.attr("id", f.idPrefix + b);
                return c.appendTo(g)
            }
            var e = a(this).data("scrollable"),
                g = f.navi.jquery ? f.navi : u(e.getRoot(), f.navi),
                h = e.getNaviButtons(),
                k = f.activeClass,
                n = f.history && !! history.pushState,
                m = e.getConf().size;
            e && (d = e);
            e.getNaviButtons = function () {
                return h.add(g)
            };
            n && (history.pushState({
                i: 0
            }, ""), a(window).on("popstate", function (a) {
                (a = a.originalEvent.state) && e.seekTo(a.i)
            }));
            c().length ? c().click(function (b) {
                a(this);
                var d = c().index(this);
                e.seekTo(d);
                b.preventDefault();
                n && history.pushState({
                    i: d
                }, "")
            }) : a.each(e.getItems(), function (a) {
                0 == a % m && b(a)
            });
            e.onBeforeSeek(function (a, b) {
                setTimeout(function () {
                    if (!a.isDefaultPrevented()) {
                        var d = b / m;
                        c().eq(d).length && c().removeClass(k).eq(d).addClass(k)
                    }
                }, 1)
            });
            e.onAddItem(function (a, c) {
                var d = e.getItems().index(c);
                0 == d % m && b(d)
            });
            e.onRemoveItem(function (a, b) {
                var d = e.getItems().index(b);
                c().eq(d).remove();
                c().removeClass(k).eq(d < e.getSize() - 1 ? d : 0).addClass(k)
            })
        });
        return f.api ? d : this
    }
})(jQuery);
(function (a) {
    function u(c, b, d) {
        var g = this,
            f = c.add(this),
            k = c.find(d.tabs),
            r = b.jquery ? b : c.children(b),
            m;
        k.length || (k = c.children());
        r.length || (r = c.parent().find(b));
        r.length || (r = a(b));
        a.extend(this, {
            click: function (b, s) {
                var r = k.eq(b),
                    u = !c.data("tabs");
                "string" == typeof b && b.replace("#", "") && (r = k.filter('[href*="' + b.replace("#", "") + '"]'), b = Math.max(k.index(r), 0));
                if (d.rotate) {
                    var v = k.length - 1;
                    if (0 > b) return g.click(v, s);
                    if (b > v) return g.click(0, s)
                }
                if (!r.length) {
                    if (0 <= m) return g;
                    b = d.initialIndex;
                    r = k.eq(b)
                }
                if (b ===
                    m) return g;
                s = s || a.Event();
                s.type = "onBeforeClick";
                f.trigger(s, [b]);
                if (!s.isDefaultPrevented()) return n[u ? d.initialEffect && d.effect || "default" : d.effect].call(g, b, function () {
                    m = b;
                    s.type = "onClick";
                    f.trigger(s, [b])
                }), k.removeClass(d.current), r.addClass(d.current), g
            },
            getConf: function () {
                return d
            },
            getTabs: function () {
                return k
            },
            getPanes: function () {
                return r
            },
            getCurrentPane: function () {
                return r.eq(m)
            },
            getCurrentTab: function () {
                return k.eq(m)
            },
            getIndex: function () {
                return m
            },
            next: function () {
                return g.click(m + 1)
            },
            prev: function () {
                return g.click(m -
                    1)
            },
            destroy: function () {
                k.off(d.event).removeClass(d.current);
                r.find('a[href^="#"]').off("click.T");
                return g
            }
        });
        a.each(["onBeforeClick", "onClick"], function (b, c) {
            if (a.isFunction(d[c])) a(g).on(c, d[c]);
            g[c] = function (b) {
                if (b) a(g).on(c, b);
                return g
            }
        });
        d.history && a.fn.history && (a.tools.history.init(k), d.event = "history");
        k.each(function (b) {
            a(this).on(d.event, function (a) {
                g.click(b, a);
                return a.preventDefault()
            })
        });
        r.find('a[href^="#"]').on("click.T", function (b) {
            g.click(a(this).attr("href"), b)
        });
        location.hash &&
            "a" == d.tabs && c.find('[href="' + location.hash.replace('"', "") + '"]').length ? g.click(location.hash) : (0 === d.initialIndex || 0 < d.initialIndex) && g.click(d.initialIndex)
    }
    a.tools = a.tools || {
        version: "1.2.8-dev"
    };
    a.tools.tabs = {
        conf: {
            tabs: "a",
            current: "current",
            onBeforeClick: null,
            onClick: null,
            effect: "default",
            initialEffect: !1,
            initialIndex: 0,
            event: "click",
            rotate: !1,
            slideUpSpeed: 400,
            slideDownSpeed: 400,
            history: !1
        },
        addEffect: function (a, b) {
            n[a] = b
        }
    };
    var n = {
        "default": function (a, b) {
            this.getPanes().hide().eq(a).show();
            b.call()
        },
        fade: function (a, b) {
            var d = this.getConf(),
                g = d.fadeOutSpeed,
                f = this.getPanes();
            g ? f.fadeOut(g) : f.hide();
            f.eq(a).fadeIn(d.fadeInSpeed, b)
        },
        slide: function (a, b) {
            var d = this.getConf();
            this.getPanes().slideUp(d.slideUpSpeed);
            this.getPanes().eq(a).slideDown(d.slideDownSpeed, b)
        },
        ajax: function (a, b) {
            this.getPanes().eq(0).load(this.getTabs().eq(a).attr("href"), b)
        }
    }, f, d;
    a.tools.tabs.addEffect("horizontal", function (c, b) {
        if (!f) {
            var e = this.getPanes().eq(c),
                g = this.getCurrentPane();
            d || (d = this.getPanes().eq(0).width());
            f = !0;
            e.show();
            g.animate({
                width: 0
            }, {
                step: function (a) {
                    e.css("width", d - a)
                },
                complete: function () {
                    a(this).hide();
                    b.call();
                    f = !1
                }
            });
            g.length || (b.call(), f = !1)
        }
    });
    a.fn.tabs = function (c, b) {
        var d = this.data("tabs");
        d && (d.destroy(), this.removeData("tabs"));
        a.isFunction(b) && (b = {
            onBeforeClick: b
        });
        b = a.extend({}, a.tools.tabs.conf, b);
        this.each(function () {
            d = new u(a(this), c, b);
            a(this).data("tabs", d)
        });
        return b.api ? d : this
    }
})(jQuery);
(function (a) {
    function u() {
        if (a.browser.msie) {
            var b = a(document).height(),
                c = a(window).height();
            return [window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth, 20 > b - c ? c : b]
        }
        return [a(document).width(), a(document).height()]
    }

    function n(b) {
        if (b) return b.call(a.mask)
    }
    a.tools = a.tools || {
        version: "1.2.8-dev"
    };
    var f;
    f = a.tools.expose = {
        conf: {
            maskId: "exposeMask",
            loadSpeed: "slow",
            closeSpeed: "fast",
            closeOnClick: !0,
            closeOnEsc: !0,
            zIndex: 9998,
            opacity: 0.8,
            startOpacity: 0,
            color: "#fff",
            onLoad: null,
            onClose: null
        }
    };
    var d, c, b, e, g;
    a.mask = {
        load: function (h, k) {
            if (b) return this;
            "string" == typeof h && (h = {
                color: h
            });
            h = h || e;
            e = h = a.extend(a.extend({}, f.conf), h);
            d = a("#" + h.maskId);
            d.length || (d = a("<div/>").attr("id", h.maskId), a("body").append(d));
            var r = u();
            d.css({
                position: "absolute",
                top: 0,
                left: 0,
                width: r[0],
                height: r[1],
                display: "none",
                opacity: h.startOpacity,
                zIndex: h.zIndex
            });
            h.color && d.css("backgroundColor", h.color);
            if (!1 === n(h.onBeforeLoad)) return this;
            if (h.closeOnEsc) a(document).on("keydown.mask", function (b) {
                27 ==
                    b.keyCode && a.mask.close(b)
            });
            if (h.closeOnClick) d.on("click.mask", function (b) {
                a.mask.close(b)
            });
            a(window).on("resize.mask", function () {
                a.mask.fit()
            });
            k && k.length && (g = k.eq(0).css("zIndex"), a.each(k, function () {
                var b = a(this);
                /relative|absolute|fixed/i.test(b.css("position")) || b.css("position", "relative")
            }), c = k.css({
                zIndex: Math.max(h.zIndex + 1, "auto" == g ? 0 : g)
            }));
            d.css({
                display: "block"
            }).fadeTo(h.loadSpeed, h.opacity, function () {
                a.mask.fit();
                n(h.onLoad);
                b = "full"
            });
            b = !0;
            return this
        },
        close: function () {
            if (b) {
                if (!1 ===
                    n(e.onBeforeClose)) return this;
                d.fadeOut(e.closeSpeed, function () {
                    c && c.css({
                        zIndex: g
                    });
                    b = !1;
                    n(e.onClose)
                });
                a(document).off("keydown.mask");
                d.off("click.mask");
                a(window).off("resize.mask")
            }
            return this
        },
        fit: function () {
            if (b) {
                var a = d.css("display");
                d.css("display", "none");
                var c = u();
                d.css({
                    display: a,
                    width: c[0],
                    height: c[1]
                })
            }
        },
        getMask: function () {
            return d
        },
        isLoaded: function (a) {
            return a ? "full" == b : b
        },
        getConf: function () {
            return e
        },
        getExposed: function () {
            return c
        }
    };
    a.fn.mask = function (b) {
        a.mask.load(b);
        return this
    };
    a.fn.expose = function (b) {
        a.mask.load(b, this);
        return this
    }
})(jQuery);
(function (a) {
    function u(a) {
        if (a) {
            var c = f.contentWindow.document;
            c.open().close();
            c.location.hash = a
        }
    }
    var n, f, d, c;
    a.tools = a.tools || {
        version: "1.2.8-dev"
    };
    a.tools.history = {
        init: function (b) {
            c || (a.browser.msie && "8" > a.browser.version ? f || (f = a("<iframe/>").attr("src", "javascript:false;").hide().get(0), a("body").append(f), setInterval(function () {
                var b = f.contentWindow.document.location.hash;
                n !== b && a(window).trigger("hash", b)
            }, 100), u(location.hash || "#")) : setInterval(function () {
                var b = location.hash;
                b !== n && a(window).trigger("hash",
                    b)
            }, 100), d = d ? d.add(b) : b, b.click(function (b) {
                var c = a(this).attr("href");
                f && u(c);
                if ("#" != c.slice(0, 1)) return location.href = "#" + c, b.preventDefault()
            }), c = !0)
        }
    };
    a(window).on("hash", function (b, c) {
        c ? d.filter(function () {
            var b = a(this).attr("href");
            return b == c || b == c.replace("#", "")
        }).trigger("history", [c]) : d.eq(0).trigger("history", [c]);
        n = c
    });
    a.fn.history = function (b) {
        a.tools.history.init(this);
        return this.on("history", b)
    }
})(jQuery);
(function (a) {
    function u(f) {
        switch (f.type) {
        case "mousemove":
            return a.extend(f.data, {
                clientX: f.clientX,
                clientY: f.clientY,
                pageX: f.pageX,
                pageY: f.pageY
            });
        case "DOMMouseScroll":
            a.extend(f, f.data);
            f.delta = -f.detail / 3;
            break;
        case "mousewheel":
            f.delta = f.wheelDelta / 120
        }
        f.type = "wheel";
        return a.event.handle.call(this, f, f.delta)
    }
    a.fn.mousewheel = function (a) {
        return this[a ? "on" : "trigger"]("wheel", a)
    };
    a.event.special.wheel = {
        setup: function () {
            a.event.add(this, n, u, {})
        },
        teardown: function () {
            a.event.remove(this, n, u)
        }
    };
    var n =
        a.browser.mozilla ? "DOMMouseScroll" + ("1.9" > a.browser.version ? " mousemove" : "") : "mousewheel"
})(jQuery);
(function (a) {
    function u(d, c, b) {
        var e = b.relative ? d.position().top : d.offset().top,
            g = b.relative ? d.position().left : d.offset().left,
            f = b.position[0],
            e = e - (c.outerHeight() - b.offset[0]),
            g = g + (d.outerWidth() + b.offset[1]);
        /iPad/i.test(navigator.userAgent) && (e -= a(window).scrollTop());
        var k = c.outerHeight() + d.outerHeight();
        "center" == f && (e += k / 2);
        "bottom" == f && (e += k);
        f = b.position[1];
        d = c.outerWidth() + d.outerWidth();
        "center" == f && (g -= d / 2);
        "left" == f && (g -= d);
        return {
            top: e,
            left: g
        }
    }

    function n(d, c) {
        var b = this,
            e = d.add(b),
            g,
            h = 0,
            k = 0,
            n = d.attr("title"),
            m = d.attr("data-tooltip"),
            q = f[c.effect],
            s, y = d.is(":input"),
            A = y && d.is(":checkbox, :radio, select, :button, :submit"),
            v = d.attr("type"),
            l = c.events[v] || c.events[y ? A ? "widget" : "input" : "def"];
        if (!q) throw 'Nonexistent effect "' + c.effect + '"';
        l = l.split(/,\s*/);
        if (2 != l.length) throw "Tooltip: bad events configuration for " + v;
        d.on(l[0], function (a) {
            clearTimeout(h);
            c.predelay ? k = setTimeout(function () {
                b.show(a)
            }, c.predelay) : b.show(a)
        }).on(l[1], function (a) {
            clearTimeout(k);
            c.delay ? h = setTimeout(function () {
                    b.hide(a)
                },
                c.delay) : b.hide(a)
        });
        n && c.cancelDefault && (d.removeAttr("title"), d.data("title", n));
        a.extend(b, {
            show: function (f) {
                if (!g && (m ? g = a(m) : c.tip ? g = a(c.tip).eq(0) : n ? g = a(c.layout).addClass(c.tipClass).appendTo(document.body).hide().append(n) : (g = d.find("." + c.tipClass), g.length || (g = d.next()), g.length || (g = d.parent().next())), !g.length)) throw "Cannot find tooltip for " + d;
                if (b.isShown()) return b;
                g.stop(!0, !0);
                var t = u(d, g, c);
                c.tip && g.html(d.data("title"));
                f = a.Event();
                f.type = "onBeforeShow";
                e.trigger(f, [t]);
                if (f.isDefaultPrevented()) return b;
                t = u(d, g, c);
                g.css({
                    position: "absolute",
                    top: t.top,
                    left: t.left
                });
                s = !0;
                q[0].call(b, function () {
                    f.type = "onShow";
                    s = "full";
                    e.trigger(f)
                });
                t = c.events.tooltip.split(/,\s*/);
                if (!g.data("__set")) {
                    g.off(t[0]).on(t[0], function () {
                        clearTimeout(h);
                        clearTimeout(k)
                    });
                    if (t[1] && !d.is("input:not(:checkbox, :radio), textarea")) g.off(t[1]).on(t[1], function (a) {
                        a.relatedTarget != d[0] && d.trigger(l[1].split(" ")[0])
                    });
                    c.tip || g.data("__set", !0)
                }
                return b
            },
            hide: function (d) {
                if (!g || !b.isShown()) return b;
                d = a.Event();
                d.type = "onBeforeHide";
                e.trigger(d);
                if (!d.isDefaultPrevented()) return s = !1, f[c.effect][1].call(b, function () {
                    d.type = "onHide";
                    e.trigger(d)
                }), b
            },
            isShown: function (a) {
                return a ? "full" == s : s
            },
            getConf: function () {
                return c
            },
            getTip: function () {
                return g
            },
            getTrigger: function () {
                return d
            }
        });
        a.each(["onHide", "onBeforeShow", "onShow", "onBeforeHide"], function (d, e) {
            if (a.isFunction(c[e])) a(b).on(e, c[e]);
            b[e] = function (c) {
                if (c) a(b).on(e, c);
                return b
            }
        })
    }
    a.tools = a.tools || {
        version: "1.2.8-dev"
    };
    a.tools.tooltip = {
        conf: {
            effect: "toggle",
            fadeOutSpeed: "fast",
            predelay: 0,
            delay: 30,
            opacity: 1,
            tip: 0,
            fadeIE: !1,
            position: ["top", "center"],
            offset: [0, 0],
            relative: !1,
            cancelDefault: !0,
            events: {
                def: "mouseenter,mouseleave",
                input: "focus,blur",
                widget: "focus mouseenter,blur mouseleave",
                tooltip: "mouseenter,mouseleave"
            },
            layout: "<div/>",
            tipClass: "tooltip"
        },
        addEffect: function (a, c, b) {
            f[a] = [c, b]
        }
    };
    var f = {
        toggle: [
            function (a) {
                var c = this.getConf(),
                    b = this.getTip(),
                    c = c.opacity;
                1 > c && b.css({
                    opacity: c
                });
                b.show();
                a.call()
            },
            function (a) {
                this.getTip().hide();
                a.call()
            }
        ],
        fade: [
            function (d) {
                var c =
                    this.getConf();
                !a.browser.msie || c.fadeIE ? this.getTip().fadeTo(c.fadeInSpeed, c.opacity, d) : (this.getTip().show(), d())
            },
            function (d) {
                var c = this.getConf();
                !a.browser.msie || c.fadeIE ? this.getTip().fadeOut(c.fadeOutSpeed, d) : (this.getTip().hide(), d())
            }
        ]
    };
    a.fn.tooltip = function (d) {
        d = a.extend(!0, {}, a.tools.tooltip.conf, d);
        "string" == typeof d.position && (d.position = d.position.split(/,?\s/));
        this.each(function () {
            a(this).data("tooltip") || (api = new n(a(this), d), a(this).data("tooltip", api))
        });
        return d.api ? api : this
    }
})(jQuery);
(function (a) {
    var u = a.tools.tooltip;
    a.extend(u.conf, {
        direction: "up",
        bounce: !1,
        slideOffset: 10,
        slideInSpeed: 200,
        slideOutSpeed: 200,
        slideFade: !a.browser.msie
    });
    var n = {
        up: ["-", "top"],
        down: ["+", "top"],
        left: ["-", "left"],
        right: ["+", "left"]
    };
    u.addEffect("slide", function (a) {
        var d = this.getConf(),
            c = this.getTip(),
            b = d.slideFade ? {
                opacity: d.opacity
            } : {}, e = n[d.direction] || n.up;
        b[e[1]] = e[0] + "=" + d.slideOffset;
        d.slideFade && c.css({
            opacity: 0
        });
        c.show().animate(b, d.slideInSpeed, a)
    }, function (f) {
        var d = this.getConf(),
            c = d.slideOffset,
            b = d.slideFade ? {
                opacity: 0
            } : {}, e = n[d.direction] || n.up,
            g = "" + e[0];
        d.bounce && (g = "+" == g ? "-" : "+");
        b[e[1]] = g + "=" + c;
        this.getTip().animate(b, d.slideOutSpeed, function () {
            a(this).hide();
            f.call()
        })
    })
})(jQuery);

/**
 * jQuery Easing v1.3 - http://gsgd.co.uk/sandbox/jquery/easing/
 *
 * Uses the built in easing capabilities added In jQuery 1.1
 * to offer multiple easing options
 *
 * TERMS OF USE - jQuery Easing
 *
 * Open source under the BSD License.
 *
 * Copyright © 2008 George McGinley Smith
 * All rights reserved.
 *
 * TERMS OF USE - EASING EQUATIONS
 *
 * Open source under the BSD License.
 *
 * Copyright © 2001 Robert Penner
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * Redistributions of source code must retain the above copyright notice, this list of
 * conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list
 * of conditions and the following disclaimer in the documentation and/or other materials
 * provided with the distribution.
 *
 * Neither the name of the author nor the names of contributors may be used to endorse
 * or promote products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *  COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 *  EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 *  GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *  NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */
jQuery.easing.jswing = jQuery.easing.swing;
jQuery.extend(jQuery.easing, {
    def: "easeOutQuad",
    swing: function (e, f, a, h, g) {
        return jQuery.easing[jQuery.easing.def](e, f, a, h, g)
    },
    easeInQuad: function (e, f, a, h, g) {
        return h * (f /= g) * f + a
    },
    easeOutQuad: function (e, f, a, h, g) {
        return -h * (f /= g) * (f - 2) + a
    },
    easeInOutQuad: function (e, f, a, h, g) {
        if ((f /= g / 2) < 1) {
            return h / 2 * f * f + a
        }
        return -h / 2 * ((--f) * (f - 2) - 1) + a
    },
    easeInCubic: function (e, f, a, h, g) {
        return h * (f /= g) * f * f + a
    },
    easeOutCubic: function (e, f, a, h, g) {
        return h * ((f = f / g - 1) * f * f + 1) + a
    },
    easeInOutCubic: function (e, f, a, h, g) {
        if ((f /= g / 2) < 1) {
            return h / 2 * f * f * f + a
        }
        return h / 2 * ((f -= 2) * f * f + 2) + a
    },
    easeInQuart: function (e, f, a, h, g) {
        return h * (f /= g) * f * f * f + a
    },
    easeOutQuart: function (e, f, a, h, g) {
        return -h * ((f = f / g - 1) * f * f * f - 1) + a
    },
    easeInOutQuart: function (e, f, a, h, g) {
        if ((f /= g / 2) < 1) {
            return h / 2 * f * f * f * f + a
        }
        return -h / 2 * ((f -= 2) * f * f * f - 2) + a
    },
    easeInQuint: function (e, f, a, h, g) {
        return h * (f /= g) * f * f * f * f + a
    },
    easeOutQuint: function (e, f, a, h, g) {
        return h * ((f = f / g - 1) * f * f * f * f + 1) + a
    },
    easeInOutQuint: function (e, f, a, h, g) {
        if ((f /= g / 2) < 1) {
            return h / 2 * f * f * f * f * f + a
        }
        return h / 2 * ((f -= 2) * f * f * f * f + 2) + a
    },
    easeInSine: function (e, f, a, h, g) {
        return -h * Math.cos(f / g * (Math.PI / 2)) + h + a
    },
    easeOutSine: function (e, f, a, h, g) {
        return h * Math.sin(f / g * (Math.PI / 2)) + a
    },
    easeInOutSine: function (e, f, a, h, g) {
        return -h / 2 * (Math.cos(Math.PI * f / g) - 1) + a
    },
    easeInExpo: function (e, f, a, h, g) {
        return (f == 0) ? a : h * Math.pow(2, 10 * (f / g - 1)) + a
    },
    easeOutExpo: function (e, f, a, h, g) {
        return (f == g) ? a + h : h * (-Math.pow(2, -10 * f / g) + 1) + a
    },
    easeInOutExpo: function (e, f, a, h, g) {
        if (f == 0) {
            return a
        }
        if (f == g) {
            return a + h
        }
        if ((f /= g / 2) < 1) {
            return h / 2 * Math.pow(2, 10 * (f - 1)) + a
        }
        return h / 2 * (-Math.pow(2, -10 * --f) + 2) + a
    },
    easeInCirc: function (e, f, a, h, g) {
        return -h * (Math.sqrt(1 - (f /= g) * f) - 1) + a
    },
    easeOutCirc: function (e, f, a, h, g) {
        return h * Math.sqrt(1 - (f = f / g - 1) * f) + a
    },
    easeInOutCirc: function (e, f, a, h, g) {
        if ((f /= g / 2) < 1) {
            return -h / 2 * (Math.sqrt(1 - f * f) - 1) + a
        }
        return h / 2 * (Math.sqrt(1 - (f -= 2) * f) + 1) + a
    },
    easeInElastic: function (f, h, e, l, k) {
        var i = 1.70158;
        var j = 0;
        var g = l;
        if (h == 0) {
            return e
        }
        if ((h /= k) == 1) {
            return e + l
        }
        if (!j) {
            j = k * 0.3
        }
        if (g < Math.abs(l)) {
            g = l;
            var i = j / 4
        } else {
            var i = j / (2 * Math.PI) * Math.asin(l / g)
        }
        return -(g * Math.pow(2, 10 * (h -= 1)) * Math.sin((h * k - i) * (2 * Math.PI) / j)) + e
    },
    easeOutElastic: function (f, h, e, l, k) {
        var i = 1.70158;
        var j = 0;
        var g = l;
        if (h == 0) {
            return e
        }
        if ((h /= k) == 1) {
            return e + l
        }
        if (!j) {
            j = k * 0.3
        }
        if (g < Math.abs(l)) {
            g = l;
            var i = j / 4
        } else {
            var i = j / (2 * Math.PI) * Math.asin(l / g)
        }
        return g * Math.pow(2, -10 * h) * Math.sin((h * k - i) * (2 * Math.PI) / j) + l + e
    },
    easeInOutElastic: function (f, h, e, l, k) {
        var i = 1.70158;
        var j = 0;
        var g = l;
        if (h == 0) {
            return e
        }
        if ((h /= k / 2) == 2) {
            return e + l
        }
        if (!j) {
            j = k * (0.3 * 1.5)
        }
        if (g < Math.abs(l)) {
            g = l;
            var i = j / 4
        } else {
            var i = j / (2 * Math.PI) * Math.asin(l / g)
        } if (h < 1) {
            return -0.5 * (g * Math.pow(2, 10 * (h -= 1)) * Math.sin((h * k - i) * (2 * Math.PI) / j)) + e
        }
        return g * Math.pow(2, -10 * (h -= 1)) * Math.sin((h * k - i) * (2 * Math.PI) / j) * 0.5 + l + e
    },
    easeInBack: function (e, f, a, i, h, g) {
        if (g == undefined) {
            g = 1.70158
        }
        return i * (f /= h) * f * ((g + 1) * f - g) + a
    },
    easeOutBack: function (e, f, a, i, h, g) {
        if (g == undefined) {
            g = 1.70158
        }
        return i * ((f = f / h - 1) * f * ((g + 1) * f + g) + 1) + a
    },
    easeInOutBack: function (e, f, a, i, h, g) {
        if (g == undefined) {
            g = 1.70158
        }
        if ((f /= h / 2) < 1) {
            return i / 2 * (f * f * (((g *= (1.525)) + 1) * f - g)) + a
        }
        return i / 2 * ((f -= 2) * f * (((g *= (1.525)) + 1) * f + g) + 2) + a
    },
    easeInBounce: function (e, f, a, h, g) {
        return h - jQuery.easing.easeOutBounce(e, g - f, 0, h, g) + a
    },
    easeOutBounce: function (e, f, a, h, g) {
        if ((f /= g) < (1 / 2.75)) {
            return h * (7.5625 * f * f) + a
        } else {
            if (f < (2 / 2.75)) {
                return h * (7.5625 * (f -= (1.5 / 2.75)) * f + 0.75) + a
            } else {
                if (f < (2.5 / 2.75)) {
                    return h * (7.5625 * (f -= (2.25 / 2.75)) * f + 0.9375) + a
                } else {
                    return h * (7.5625 * (f -= (2.625 / 2.75)) * f + 0.984375) + a
                }
            }
        }
    },
    easeInOutBounce: function (e, f, a, h, g) {
        if (f < g / 2) {
            return jQuery.easing.easeInBounce(e, f * 2, 0, h, g) * 0.5 + a
        }
        return jQuery.easing.easeOutBounce(e, f * 2 - g, 0, h, g) * 0.5 + h * 0.5 + a
    }
});

/**
 * hoverIntent r5 // 2007.03.27 // jQuery 1.1.2+
 * <http://cherne.net/brian/resources/jquery.hoverIntent.html>
 *
 * @param  f  onMouseOver function || An object with configuration options
 * @param  g  onMouseOut function  || Nothing (use configuration options object)
 * @author    Brian Cherne <brian@cherne.net>
 */
(function ($) {
    $.fn.hoverIntent = function (f, g) {
        var cfg = {
            sensitivity: 7,
            interval: 100,
            timeout: 0
        };
        cfg = $.extend(cfg, g ? {
            over: f,
            out: g
        } : f);
        var cX, cY, pX, pY;
        var track = function (ev) {
            cX = ev.pageX;
            cY = ev.pageY;
        };
        var compare = function (ev, ob) {
            ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
            if ((Math.abs(pX - cX) + Math.abs(pY - cY)) < cfg.sensitivity) {
                $(ob).unbind("mousemove", track);
                ob.hoverIntent_s = 1;
                return cfg.over.apply(ob, [ev]);
            } else {
                pX = cX;
                pY = cY;
                ob.hoverIntent_t = setTimeout(function () {
                    compare(ev, ob);
                }, cfg.interval);
            }
        };
        var delay = function (ev, ob) {
            ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
            ob.hoverIntent_s = 0;
            return cfg.out.apply(ob, [ev]);
        };
        var handleHover = function (e) {
            var p = (e.type == "mouseover" ? e.fromElement : e.toElement) || e.relatedTarget;
            while (p && p != this) {
                try {
                    p = p.parentNode;
                } catch (e) {
                    p = this;
                }
            }
            if (p == this) {
                return false;
            }
            var ev = jQuery.extend({}, e);
            var ob = this;
            if (ob.hoverIntent_t) {
                ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
            }
            if (e.type == "mouseover") {
                pX = ev.pageX;
                pY = ev.pageY;
                $(ob).bind("mousemove", track);
                if (ob.hoverIntent_s != 1) {
                    ob.hoverIntent_t = setTimeout(function () {
                        compare(ev, ob);
                    }, cfg.interval);
                }
            } else {
                $(ob).unbind("mousemove", track);
                if (ob.hoverIntent_s == 1) {
                    ob.hoverIntent_t = setTimeout(function () {
                        delay(ev, ob);
                    }, cfg.timeout);
                }
            }
        };
        return this.mouseover(handleHover).mouseout(handleHover);
    };
})(jQuery);

/*
 * jQuery Color Animations
 * Copyright 2007 John Resig
 * Released under the MIT and GPL licenses.
 */
(function (d) {
    d.each(["backgroundColor", "borderBottomColor", "borderLeftColor", "borderRightColor", "borderTopColor", "color", "outlineColor"], function (f, e) {
        d.fx.step[e] = function (g) {
            if (g.state == 0) {
                g.start = c(g.elem, e);
                g.end = b(g.end)
            }
            g.elem.style[e] = "rgb(" + [Math.max(Math.min(parseInt((g.pos * (g.end[0] - g.start[0])) + g.start[0]), 255), 0), Math.max(Math.min(parseInt((g.pos * (g.end[1] - g.start[1])) + g.start[1]), 255), 0), Math.max(Math.min(parseInt((g.pos * (g.end[2] - g.start[2])) + g.start[2]), 255), 0)].join(",") + ")"
        }
    });

    function b(f) {
        var e;
        if (f && f.constructor == Array && f.length == 3) {
            return f
        }
        if (e = /rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(f)) {
            return [parseInt(e[1]), parseInt(e[2]), parseInt(e[3])]
        }
        if (e = /rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(f)) {
            return [parseFloat(e[1]) * 2.55, parseFloat(e[2]) * 2.55, parseFloat(e[3]) * 2.55]
        }
        if (e = /#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(f)) {
            return [parseInt(e[1], 16), parseInt(e[2], 16), parseInt(e[3], 16)]
        }
        if (e = /#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(f)) {
            return [parseInt(e[1] + e[1], 16), parseInt(e[2] + e[2], 16), parseInt(e[3] + e[3], 16)]
        }
        return a[d.trim(f).toLowerCase()]
    }

    function c(g, e) {
        var f;
        do {
            f = d.curCSS(g, e);
            if (f != "" && f != "transparent" || d.nodeName(g, "body")) {
                break
            }
            e = "backgroundColor"
        } while (g = g.parentNode);
        return b(f)
    }
    var a = {
        aqua: [0, 255, 255],
        azure: [240, 255, 255],
        beige: [245, 245, 220],
        black: [0, 0, 0],
        blue: [0, 0, 255],
        brown: [165, 42, 42],
        cyan: [0, 255, 255],
        darkblue: [0, 0, 139],
        darkcyan: [0, 139, 139],
        darkgrey: [169, 169, 169],
        darkgreen: [0, 100, 0],
        darkkhaki: [189, 183, 107],
        darkmagenta: [139, 0, 139],
        darkolivegreen: [85, 107, 47],
        darkorange: [255, 140, 0],
        darkorchid: [153, 50, 204],
        darkred: [139, 0, 0],
        darksalmon: [233, 150, 122],
        darkviolet: [148, 0, 211],
        fuchsia: [255, 0, 255],
        gold: [255, 215, 0],
        green: [0, 128, 0],
        indigo: [75, 0, 130],
        khaki: [240, 230, 140],
        lightblue: [173, 216, 230],
        lightcyan: [224, 255, 255],
        lightgreen: [144, 238, 144],
        lightgrey: [211, 211, 211],
        lightpink: [255, 182, 193],
        lightyellow: [255, 255, 224],
        lime: [0, 255, 0],
        magenta: [255, 0, 255],
        maroon: [128, 0, 0],
        navy: [0, 0, 128],
        olive: [128, 128, 0],
        orange: [255, 165, 0],
        pink: [255, 192, 203],
        purple: [128, 0, 128],
        violet: [128, 0, 128],
        red: [255, 0, 0],
        silver: [192, 192, 192],
        white: [255, 255, 255],
        yellow: [255, 255, 0]
    }
})(jQuery);

/**
 * @license Rangy Inputs, a jQuery plug-in for selection and caret manipulation within textareas and text inputs.
 *
 * https://github.com/timdown/rangyinputs
 *
 * For range and selection features for contenteditable, see Rangy.

 * http://code.google.com/p/rangy/
 *
 * Depends on jQuery 1.0 or later.
 *
 * Copyright 2013, Tim Down
 * Licensed under the MIT license.
 * Version: 1.1.2
 * Build date: 6 September 2013
 */
! function (a) {
    function l(a, b) {
        var c = typeof a[b];
        return "function" === c || !("object" != c || !a[b]) || "unknown" == c
    }

    function m(a, c) {
        return typeof a[c] != b
    }

    function n(a, b) {
        return !("object" != typeof a[b] || !a[b])
    }

    function o(a) {
        window.console && window.console.log && window.console.log("RangyInputs not supported in your browser. Reason: " + a)
    }

    function p(a, c, d) {
        return 0 > c && (c += a.value.length), typeof d == b && (d = c), 0 > d && (d += a.value.length), {
            start: c,
            end: d
        }
    }

    function q(a, b, c) {
        return {
            start: b,
            end: c,
            length: c - b,
            text: a.value.slice(b, c)
        }
    }

    function r() {
        return n(document, "body") ? document.body : document.getElementsByTagName("body")[0]
    }
    var c, d, e, f, g, h, i, j, k, b = "undefined";
    a(document).ready(function () {
        function v(a, b) {
            return function () {
                var c = this.jquery ? this[0] : this,
                    d = c.nodeName.toLowerCase();
                if (1 == c.nodeType && ("textarea" == d || "input" == d && "text" == c.type)) {
                    var e = [c].concat(Array.prototype.slice.call(arguments)),
                        f = a.apply(this, e);
                    if (!b) return f
                }
                return b ? this : void 0
            }
        }
        var s = document.createElement("textarea");
        if (r().appendChild(s), m(s, "selectionStart") && m(s, "selectionEnd")) c = function (a) {
            var b = a.selectionStart,
                c = a.selectionEnd;
            return q(a, b, c)
        }, d = function (a, b, c) {
            var d = p(a, b, c);
            a.selectionStart = d.start, a.selectionEnd = d.end
        }, k = function (a, b) {
            b ? a.selectionEnd = a.selectionStart : a.selectionStart = a.selectionEnd
        };
        else {
            if (!(l(s, "createTextRange") && n(document, "selection") && l(document.selection, "createRange"))) return r().removeChild(s), o("No means of finding text input caret position"), void 0;
            c = function (a) {
                var d, e, f, g, b = 0,
                    c = 0,
                    h = document.selection.createRange();
                return h && h.parentElement() == a && (f = a.value.length, d = a.value.replace(/\r\n/g, "\n"), e = a.createTextRange(), e.moveToBookmark(h.getBookmark()), g = a.createTextRange(), g.collapse(!1), e.compareEndPoints("StartToEnd", g) > -1 ? b = c = f : (b = -e.moveStart("character", -f), b += d.slice(0, b).split("\n").length - 1, e.compareEndPoints("EndToEnd", g) > -1 ? c = f : (c = -e.moveEnd("character", -f), c += d.slice(0, c).split("\n").length - 1))), q(a, b, c)
            };
            var t = function (a, b) {
                return b - (a.value.slice(0, b).split("\r\n").length - 1)
            };
            d = function (a, b, c) {
                var d = p(a, b, c),
                    e = a.createTextRange(),
                    f = t(a, d.start);
                e.collapse(!0), d.start == d.end ? e.move("character", f) : (e.moveEnd("character", t(a, d.end)), e.moveStart("character", f)), e.select()
            }, k = function (a, b) {
                var c = document.selection.createRange();
                c.collapse(b), c.select()
            }
        }
        r().removeChild(s), f = function (a, b, c, e) {
            var f;
            b != c && (f = a.value, a.value = f.slice(0, b) + f.slice(c)), e && d(a, b, b)
        }, e = function (a) {
            var b = c(a);
            f(a, b.start, b.end, !0)
        }, j = function (a) {
            var e, b = c(a);
            return b.start != b.end && (e = a.value, a.value = e.slice(0, b.start) + e.slice(b.end)), d(a, b.start, b.start), b.text
        };
        var u = function (a, b, c, e) {
            var f = b + c.length;
            if (e = "string" == typeof e ? e.toLowerCase() : "", ("collapsetoend" == e || "select" == e) && /[\r\n]/.test(c)) {
                var g = c.replace(/\r\n/g, "\n").replace(/\r/g, "\n");
                f = b + g.length;
                var h = b + g.indexOf("\n");
                "\r\n" == a.value.slice(h, h + 2) && (f += g.match(/\n/g).length)
            }
            switch (e) {
            case "collapsetostart":
                d(a, b, b);
                break;
            case "collapsetoend":
                d(a, f, f);
                break;
            case "select":
                d(a, b, f)
            }
        };
        g = function (a, b, c, d) {
            var e = a.value;
            a.value = e.slice(0, c) + b + e.slice(c), "boolean" == typeof d && (d = d ? "collapseToEnd" : ""), u(a, c, b, d)
        }, h = function (a, b, d) {
            var e = c(a),
                f = a.value;
            a.value = f.slice(0, e.start) + b + f.slice(e.end), u(a, e.start, b, d || "collapseToEnd")
        }, i = function (a, d, e, f) {
            typeof e == b && (e = d);
            var g = c(a),
                h = a.value;
            a.value = h.slice(0, g.start) + d + g.text + e + h.slice(g.end);
            var i = g.start + d.length;
            u(a, i, g.text, f || "select")
        }, a.fn.extend({
            getSelection: v(c, !1),
            setSelection: v(d, !0),
            collapseSelection: v(k, !0),
            deleteSelectedText: v(e, !0),
            deleteText: v(f, !0),
            extractSelectedText: v(j, !1),
            insertText: v(g, !0),
            replaceSelectedText: v(h, !0),
            surroundSelectedText: v(i, !0)
        })
    })
}(jQuery);