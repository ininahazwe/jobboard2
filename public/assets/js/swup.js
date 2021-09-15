(function e(t, n) {
    if (typeof exports === "object" && typeof module === "object") module.exports = n(); else if (typeof define === "function" && define.amd) define([], n); else if (typeof exports === "object") exports["Swup"] = n(); else t["Swup"] = n()
})(window, function () {
    return function (e) {
        var t = {};

        function n(r) {
            if (t[r]) {
                return t[r].exports
            }
            var i = t[r] = {i: r, l: false, exports: {}};
            e[r].call(i.exports, i, i.exports, n);
            i.l = true;
            return i.exports
        }

        n.m = e;
        n.c = t;
        n.d = function (e, t, r) {
            if (!n.o(e, t)) {
                Object.defineProperty(e, t, {enumerable: true, get: r})
            }
        };
        n.r = function (e) {
            if (typeof Symbol !== "undefined" && Symbol.toStringTag) {
                Object.defineProperty(e, Symbol.toStringTag, {value: "Module"})
            }
            Object.defineProperty(e, "__esModule", {value: true})
        };
        n.t = function (e, t) {
            if (t & 1) e = n(e);
            if (t & 8) return e;
            if (t & 4 && typeof e === "object" && e && e.__esModule) return e;
            var r = Object.create(null);
            n.r(r);
            Object.defineProperty(r, "default", {enumerable: true, value: e});
            if (t & 2 && typeof e != "string") for (var i in e) n.d(r, i, function (t) {
                return e[t]
            }.bind(null, i));
            return r
        };
        n.n = function (e) {
            var t = e && e.__esModule ? function t() {
                return e["default"]
            } : function t() {
                return e
            };
            n.d(t, "a", t);
            return t
        };
        n.o = function (e, t) {
            return Object.prototype.hasOwnProperty.call(e, t)
        };
        n.p = "";
        return n(n.s = 2)
    }([function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        t.Link = t.markSwupElements = t.getCurrentUrl = t.transitionEnd = t.fetch = t.getDataFromHtml = t.createHistoryRecord = t.classify = undefined;
        var r = n(8);
        var i = w(r);
        var a = n(9);
        var o = w(a);
        var s = n(10);
        var u = w(s);
        var l = n(11);
        var c = w(l);
        var f = n(12);
        var d = w(f);
        var h = n(13);
        var p = w(h);
        var v = n(14);
        var g = w(v);
        var m = n(15);
        var y = w(m);

        function w(e) {
            return e && e.__esModule ? e : {default: e}
        }

        var b = t.classify = i.default;
        var E = t.createHistoryRecord = o.default;
        var P = t.getDataFromHtml = u.default;
        var _ = t.fetch = c.default;
        var k = t.transitionEnd = d.default;
        var S = t.getCurrentUrl = p.default;
        var O = t.markSwupElements = g.default;
        var j = t.Link = y.default
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = t.query = function e(t) {
            var n = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : document;
            if (typeof t !== "string") {
                return t
            }
            return n.querySelector(t)
        };
        var i = t.queryAll = function e(t) {
            var n = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : document;
            if (typeof t !== "string") {
                return t
            }
            return Array.prototype.slice.call(n.querySelectorAll(t))
        }
    }, function (e, t, n) {
        "use strict";
        var r = n(3);
        var i = a(r);

        function a(e) {
            return e && e.__esModule ? e : {default: e}
        }

        e.exports = i.default
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = Object.assign || function (e) {
            for (var t = 1; t < arguments.length; t++) {
                var n = arguments[t];
                for (var r in n) {
                    if (Object.prototype.hasOwnProperty.call(n, r)) {
                        e[r] = n[r]
                    }
                }
            }
            return e
        };
        var i = function () {
            function e(e, t) {
                for (var n = 0; n < t.length; n++) {
                    var r = t[n];
                    r.enumerable = r.enumerable || false;
                    r.configurable = true;
                    if ("value" in r) r.writable = true;
                    Object.defineProperty(e, r.key, r)
                }
            }

            return function (t, n, r) {
                if (n) e(t.prototype, n);
                if (r) e(t, r);
                return t
            }
        }();
        var a = n(4);
        var o = M(a);
        var s = n(6);
        var u = M(s);
        var l = n(7);
        var c = M(l);
        var f = n(16);
        var d = M(f);
        var h = n(17);
        var p = M(h);
        var v = n(18);
        var g = M(v);
        var m = n(19);
        var y = M(m);
        var w = n(20);
        var b = M(w);
        var E = n(21);
        var P = M(E);
        var _ = n(22);
        var k = M(_);
        var S = n(23);
        var O = n(1);
        var j = n(0);

        function M(e) {
            return e && e.__esModule ? e : {default: e}
        }

        function H(e, t) {
            if (!(e instanceof t)) {
                throw new TypeError("Cannot call a class as a function")
            }
        }

        var L = function () {
            function e(t) {
                H(this, e);
                var n = {
                    animateHistoryBrowsing: false,
                    animationSelector: '[class*="transition-"]',
                    linkSelector: 'a[href^="' + window.location.origin + '"]:not([data-no-swup]), a[href^="/"]:not([data-no-swup]), a[href^="#"]:not([data-no-swup])',
                    cache: true,
                    containers: ["#swup"],
                    requestHeaders: {"X-Requested-With": "swup", Accept: "text/html, application/xhtml+xml"},
                    plugins: [],
                    skipPopStateHandling: function e(t) {
                        return !(t.state && t.state.source === "swup")
                    }
                };
                var i = r({}, n, t);
                this._handlers = {
                    animationInDone: [],
                    animationInStart: [],
                    animationOutDone: [],
                    animationOutStart: [],
                    animationSkipped: [],
                    clickLink: [],
                    contentReplaced: [],
                    disabled: [],
                    enabled: [],
                    openPageInNewTab: [],
                    pageLoaded: [],
                    pageRetrievedFromCache: [],
                    pageView: [],
                    popState: [],
                    samePage: [],
                    samePageWithHash: [],
                    serverError: [],
                    transitionStart: [],
                    transitionEnd: [],
                    willReplaceContent: []
                };
                this.scrollToElement = null;
                this.preloadPromise = null;
                this.options = i;
                this.plugins = [];
                this.transition = {};
                this.delegatedListeners = {};
                this.boundPopStateHandler = this.popStateHandler.bind(this);
                this.cache = new u.default;
                this.cache.swup = this;
                this.loadPage = c.default;
                this.renderPage = d.default;
                this.triggerEvent = p.default;
                this.on = g.default;
                this.off = y.default;
                this.updateTransition = b.default;
                this.getAnimationPromises = P.default;
                this.getPageData = k.default;
                this.log = function () {
                };
                this.use = S.use;
                this.unuse = S.unuse;
                this.findPlugin = S.findPlugin;
                this.enable()
            }

            i(e, [{
                key: "enable", value: function e() {
                    var t = this;
                    if (typeof Promise === "undefined") {
                        console.warn("Promise is not supported");
                        return
                    }
                    this.delegatedListeners.click = (0, o.default)(document, this.options.linkSelector, "click", this.linkClickHandler.bind(this));
                    window.addEventListener("popstate", this.boundPopStateHandler);
                    var n = (0, j.getDataFromHtml)(document.documentElement.outerHTML, this.options.containers);
                    n.url = n.responseURL = (0, j.getCurrentUrl)();
                    if (this.options.cache) {
                        this.cache.cacheUrl(n)
                    }
                    (0, j.markSwupElements)(document.documentElement, this.options.containers);
                    this.options.plugins.forEach(function (e) {
                        t.use(e)
                    });
                    window.history.replaceState(Object.assign({}, window.history.state, {
                        url: window.location.href,
                        random: Math.random(),
                        source: "swup"
                    }), document.title, window.location.href);
                    this.triggerEvent("enabled");
                    document.documentElement.classList.add("swup-enabled");
                    this.triggerEvent("pageView")
                }
            }, {
                key: "destroy", value: function e() {
                    var t = this;
                    this.delegatedListeners.click.destroy();
                    window.removeEventListener("popstate", this.boundPopStateHandler);
                    this.cache.empty();
                    this.options.plugins.forEach(function (e) {
                        t.unuse(e)
                    });
                    (0, O.queryAll)("[data-swup]").forEach(function (e) {
                        e.removeAttribute("data-swup")
                    });
                    this.off();
                    this.triggerEvent("disabled");
                    document.documentElement.classList.remove("swup-enabled")
                }
            }, {
                key: "linkClickHandler", value: function e(t) {
                    if (!t.metaKey && !t.ctrlKey && !t.shiftKey && !t.altKey) {
                        if (t.button === 0) {
                            this.triggerEvent("clickLink", t);
                            t.preventDefault();
                            var n = new j.Link(t.delegateTarget);
                            if (n.getAddress() == (0, j.getCurrentUrl)() || n.getAddress() == "") {
                                if (n.getHash() != "") {
                                    this.triggerEvent("samePageWithHash", t);
                                    var r = document.querySelector(n.getHash());
                                    if (r != null) {
                                        history.replaceState({
                                            url: n.getAddress() + n.getHash(),
                                            random: Math.random(),
                                            source: "swup"
                                        }, document.title, n.getAddress() + n.getHash())
                                    } else {
                                        console.warn("Element for offset not found (" + n.getHash() + ")")
                                    }
                                } else {
                                    this.triggerEvent("samePage", t)
                                }
                            } else {
                                if (n.getHash() != "") {
                                    this.scrollToElement = n.getHash()
                                }
                                var i = t.delegateTarget.getAttribute("data-swup-transition");
                                this.loadPage({url: n.getAddress(), customTransition: i}, false)
                            }
                        }
                    } else {
                        this.triggerEvent("openPageInNewTab", t)
                    }
                }
            }, {
                key: "popStateHandler", value: function e(t) {
                    if (this.options.skipPopStateHandling(t)) return;
                    var n = new j.Link(t.state ? t.state.url : window.location.pathname);
                    if (n.getHash() !== "") {
                        this.scrollToElement = n.getHash()
                    } else {
                        t.preventDefault()
                    }
                    this.triggerEvent("popState", t);
                    this.loadPage({url: n.getAddress()}, t)
                }
            }]);
            return e
        }();
        t.default = L
    }, function (e, t, n) {
        var r = n(5);

        function i(e, t, n, r, i) {
            var o = a.apply(this, arguments);
            e.addEventListener(n, o, i);
            return {
                destroy: function () {
                    e.removeEventListener(n, o, i)
                }
            }
        }

        function a(e, t, n, i) {
            return function (n) {
                n.delegateTarget = r(n.target, t);
                if (n.delegateTarget) {
                    i.call(e, n)
                }
            }
        }

        e.exports = i
    }, function (e, t) {
        var n = 9;
        if (typeof Element !== "undefined" && !Element.prototype.matches) {
            var r = Element.prototype;
            r.matches = r.matchesSelector || r.mozMatchesSelector || r.msMatchesSelector || r.oMatchesSelector || r.webkitMatchesSelector
        }

        function i(e, t) {
            while (e && e.nodeType !== n) {
                if (typeof e.matches === "function" && e.matches(t)) {
                    return e
                }
                e = e.parentNode
            }
        }

        e.exports = i
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = function () {
            function e(e, t) {
                for (var n = 0; n < t.length; n++) {
                    var r = t[n];
                    r.enumerable = r.enumerable || false;
                    r.configurable = true;
                    if ("value" in r) r.writable = true;
                    Object.defineProperty(e, r.key, r)
                }
            }

            return function (t, n, r) {
                if (n) e(t.prototype, n);
                if (r) e(t, r);
                return t
            }
        }();

        function i(e, t) {
            if (!(e instanceof t)) {
                throw new TypeError("Cannot call a class as a function")
            }
        }

        var a = t.Cache = function () {
            function e() {
                i(this, e);
                this.pages = {};
                this.last = null
            }

            r(e, [{
                key: "cacheUrl", value: function e(t) {
                    if (t.url in this.pages === false) {
                        this.pages[t.url] = t
                    }
                    this.last = this.pages[t.url];
                    this.swup.log("Cache (" + Object.keys(this.pages).length + ")", this.pages)
                }
            }, {
                key: "getPage", value: function e(t) {
                    return this.pages[t]
                }
            }, {
                key: "getCurrentPage", value: function e() {
                    return this.getPage(window.location.pathname + window.location.search)
                }
            }, {
                key: "exists", value: function e(t) {
                    return t in this.pages
                }
            }, {
                key: "empty", value: function e() {
                    this.pages = {};
                    this.last = null;
                    this.swup.log("Cache cleared")
                }
            }, {
                key: "remove", value: function e(t) {
                    delete this.pages[t]
                }
            }]);
            return e
        }();
        t.default = a
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = Object.assign || function (e) {
            for (var t = 1; t < arguments.length; t++) {
                var n = arguments[t];
                for (var r in n) {
                    if (Object.prototype.hasOwnProperty.call(n, r)) {
                        e[r] = n[r]
                    }
                }
            }
            return e
        };
        var i = n(0);
        var a = function e(t, n) {
            var a = this;
            var o = [], s = void 0;
            var u = function e() {
                a.triggerEvent("animationOutStart");
                document.documentElement.classList.add("is-changing");
                document.documentElement.classList.add("is-leaving");
                document.documentElement.classList.add("is-animating");
                if (n) {
                    document.documentElement.classList.add("is-popstate")
                }
                document.documentElement.classList.add("to-" + (0, i.classify)(t.url));
                o = a.getAnimationPromises("out");
                Promise.all(o).then(function () {
                    a.triggerEvent("animationOutDone")
                });
                if (!n) {
                    var r = void 0;
                    if (a.scrollToElement != null) {
                        r = t.url + a.scrollToElement
                    } else {
                        r = t.url
                    }
                    (0, i.createHistoryRecord)(r)
                }
            };
            this.triggerEvent("transitionStart", n);
            if (t.customTransition != null) {
                this.updateTransition(window.location.pathname, t.url, t.customTransition);
                document.documentElement.classList.add("to-" + (0, i.classify)(t.customTransition))
            } else {
                this.updateTransition(window.location.pathname, t.url)
            }
            if (!n || this.options.animateHistoryBrowsing) {
                u()
            } else {
                this.triggerEvent("animationSkipped")
            }
            if (this.cache.exists(t.url)) {
                s = new Promise(function (e) {
                    e()
                });
                this.triggerEvent("pageRetrievedFromCache")
            } else {
                if (!this.preloadPromise || this.preloadPromise.route != t.url) {
                    s = new Promise(function (e, n) {
                        (0, i.fetch)(r({}, t, {headers: a.options.requestHeaders}), function (r) {
                            if (r.status === 500) {
                                a.triggerEvent("serverError");
                                n(t.url);
                                return
                            } else {
                                var i = a.getPageData(r);
                                if (i != null) {
                                    i.url = t.url
                                } else {
                                    n(t.url);
                                    return
                                }
                                a.cache.cacheUrl(i);
                                a.triggerEvent("pageLoaded")
                            }
                            e()
                        })
                    })
                } else {
                    s = this.preloadPromise
                }
            }
            Promise.all(o.concat([s])).then(function () {
                a.renderPage(a.cache.getPage(t.url), n);
                a.preloadPromise = null
            }).catch(function (e) {
                a.options.skipPopStateHandling = function () {
                    window.location = e;
                    return true
                };
                window.history.go(-1)
            })
        };
        t.default = a
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = function e(t) {
            var n = t.toString().toLowerCase().replace(/\s+/g, "-").replace(/\//g, "-").replace(/[^\w\-]+/g, "").replace(/\-\-+/g, "-").replace(/^-+/, "").replace(/-+$/, "");
            if (n[0] === "/") n = n.splice(1);
            if (n === "") n = "homepage";
            return n
        };
        t.default = r
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = function e(t) {
            window.history.pushState({
                url: t || window.location.href.split(window.location.hostname)[1],
                random: Math.random(),
                source: "swup"
            }, document.getElementsByTagName("title")[0].innerText, t || window.location.href.split(window.location.hostname)[1])
        };
        t.default = r
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (e) {
            return typeof e
        } : function (e) {
            return e && typeof Symbol === "function" && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
        };
        var i = n(1);
        var a = function e(t, n) {
            var a = document.createElement("html");
            a.innerHTML = t;
            var o = [];
            var s = function e(t) {
                if (a.querySelector(n[t]) == null) {
                    return {v: null}
                } else {
                    (0, i.queryAll)(n[t]).forEach(function (e, r) {
                        (0, i.queryAll)(n[t], a)[r].setAttribute("data-swup", o.length);
                        o.push((0, i.queryAll)(n[t], a)[r].outerHTML)
                    })
                }
            };
            for (var u = 0; u < n.length; u++) {
                var l = s(u);
                if ((typeof l === "undefined" ? "undefined" : r(l)) === "object") return l.v
            }
            var c = {
                title: a.querySelector("title").innerText,
                pageClass: a.querySelector("body").className,
                originalContent: t,
                blocks: o
            };
            a.innerHTML = "";
            a = null;
            return c
        };
        t.default = a
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = Object.assign || function (e) {
            for (var t = 1; t < arguments.length; t++) {
                var n = arguments[t];
                for (var r in n) {
                    if (Object.prototype.hasOwnProperty.call(n, r)) {
                        e[r] = n[r]
                    }
                }
            }
            return e
        };
        var i = function e(t) {
            var n = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
            var i = {url: window.location.pathname + window.location.search, method: "GET", data: null, headers: {}};
            var a = r({}, i, t);
            var o = new XMLHttpRequest;
            o.onreadystatechange = function () {
                if (o.readyState === 4) {
                    if (o.status !== 500) {
                        n(o)
                    } else {
                        n(o)
                    }
                }
            };
            o.open(a.method, a.url, true);
            Object.keys(a.headers).forEach(function (e) {
                o.setRequestHeader(e, a.headers[e])
            });
            o.send(a.data);
            return o
        };
        t.default = i
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = function e() {
            var t = document.createElement("div");
            var n = {
                WebkitTransition: "webkitTransitionEnd",
                MozTransition: "transitionend",
                OTransition: "oTransitionEnd otransitionend",
                transition: "transitionend"
            };
            for (var r in n) {
                if (t.style[r] !== undefined) {
                    return n[r]
                }
            }
            return false
        };
        t.default = r
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = function e() {
            return window.location.pathname + window.location.search
        };
        t.default = r
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = n(1);
        var i = function e(t, n) {
            var i = 0;
            var a = function e(a) {
                if (t.querySelector(n[a]) == null) {
                    console.warn("Element " + n[a] + " is not in current page.")
                } else {
                    (0, r.queryAll)(n[a]).forEach(function (e, o) {
                        (0, r.queryAll)(n[a], t)[o].setAttribute("data-swup", i);
                        i++
                    })
                }
            };
            for (var o = 0; o < n.length; o++) {
                a(o)
            }
        };
        t.default = i
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = function () {
            function e(e, t) {
                for (var n = 0; n < t.length; n++) {
                    var r = t[n];
                    r.enumerable = r.enumerable || false;
                    r.configurable = true;
                    if ("value" in r) r.writable = true;
                    Object.defineProperty(e, r.key, r)
                }
            }

            return function (t, n, r) {
                if (n) e(t.prototype, n);
                if (r) e(t, r);
                return t
            }
        }();

        function i(e, t) {
            if (!(e instanceof t)) {
                throw new TypeError("Cannot call a class as a function")
            }
        }

        var a = function () {
            function e(t) {
                i(this, e);
                if (t instanceof Element || t instanceof SVGElement) {
                    this.link = t
                } else {
                    this.link = document.createElement("a");
                    this.link.href = t
                }
            }

            r(e, [{
                key: "getPath", value: function e() {
                    var t = this.link.pathname;
                    if (t[0] !== "/") {
                        t = "/" + t
                    }
                    return t
                }
            }, {
                key: "getAddress", value: function e() {
                    var t = this.link.pathname + this.link.search;
                    if (this.link.getAttribute("xlink:href")) {
                        t = this.link.getAttribute("xlink:href")
                    }
                    if (t[0] !== "/") {
                        t = "/" + t
                    }
                    return t
                }
            }, {
                key: "getHash", value: function e() {
                    return this.link.hash
                }
            }]);
            return e
        }();
        t.default = a
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = Object.assign || function (e) {
            for (var t = 1; t < arguments.length; t++) {
                var n = arguments[t];
                for (var r in n) {
                    if (Object.prototype.hasOwnProperty.call(n, r)) {
                        e[r] = n[r]
                    }
                }
            }
            return e
        };
        var i = n(1);
        var a = n(0);
        var o = function e(t, n) {
            var i = this;
            document.documentElement.classList.remove("is-leaving");
            var o = new a.Link(t.responseURL);
            if (window.location.pathname !== o.getPath()) {
                window.history.replaceState({
                    url: o.getPath(),
                    random: Math.random(),
                    source: "swup"
                }, document.title, o.getPath());
                this.cache.cacheUrl(r({}, t, {url: o.getPath()}))
            }
            if (!n || this.options.animateHistoryBrowsing) {
                document.documentElement.classList.add("is-rendering")
            }
            this.triggerEvent("willReplaceContent", n);
            for (var s = 0; s < t.blocks.length; s++) {
                document.body.querySelector('[data-swup="' + s + '"]').outerHTML = t.blocks[s]
            }
            document.title = t.title;
            this.triggerEvent("contentReplaced", n);
            this.triggerEvent("pageView", n);
            if (!this.options.cache) {
                this.cache.empty()
            }
            setTimeout(function () {
                if (!n || i.options.animateHistoryBrowsing) {
                    i.triggerEvent("animationInStart");
                    document.documentElement.classList.remove("is-animating")
                }
            }, 10);
            if (!n || this.options.animateHistoryBrowsing) {
                var u = this.getAnimationPromises("in");
                Promise.all(u).then(function () {
                    i.triggerEvent("animationInDone");
                    i.triggerEvent("transitionEnd", n);
                    document.documentElement.className.split(" ").forEach(function (e) {
                        if (new RegExp("^to-").test(e) || e === "is-changing" || e === "is-rendering" || e === "is-popstate") {
                            document.documentElement.classList.remove(e)
                        }
                    })
                })
            } else {
                this.triggerEvent("transitionEnd", n)
            }
            this.scrollToElement = null
        };
        t.default = o
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = function e(t, n) {
            this._handlers[t].forEach(function (e) {
                try {
                    e(n)
                } catch (e) {
                    console.error(e)
                }
            });
            var r = new CustomEvent("swup:" + t, {detail: t});
            document.dispatchEvent(r)
        };
        t.default = r
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = function e(t, n) {
            if (this._handlers[t]) {
                this._handlers[t].push(n)
            } else {
                console.warn("Unsupported event " + t + ".")
            }
        };
        t.default = r
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = function e(t, n) {
            var r = this;
            if (t != null) {
                if (n != null) {
                    if (this._handlers[t] && this._handlers[t].filter(function (e) {
                        return e === n
                    }).length) {
                        var i = this._handlers[t].filter(function (e) {
                            return e === n
                        })[0];
                        var a = this._handlers[t].indexOf(i);
                        if (a > -1) {
                            this._handlers[t].splice(a, 1)
                        }
                    } else {
                        console.warn("Handler for event '" + t + "' no found.")
                    }
                } else {
                    this._handlers[t] = []
                }
            } else {
                Object.keys(this._handlers).forEach(function (e) {
                    r._handlers[e] = []
                })
            }
        };
        t.default = r
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = function e(t, n, r) {
            this.transition = {from: t, to: n, custom: r}
        };
        t.default = r
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = n(1);
        var i = n(0);
        var a = function e() {
            var t = [];
            var n = (0, r.queryAll)(this.options.animationSelector);
            n.forEach(function (e) {
                var n = new Promise(function (t) {
                    e.addEventListener((0, i.transitionEnd)(), function (n) {
                        if (e == n.target) {
                            t()
                        }
                    })
                });
                t.push(n)
            });
            return t
        };
        t.default = a
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = n(0);
        var i = function e(t) {
            var n = t.responseText;
            var i = (0, r.getDataFromHtml)(n, this.options.containers);
            if (i) {
                i.responseURL = t.responseURL ? t.responseURL : window.location.href
            } else {
                console.warn("Received page is invalid.");
                return null
            }
            return i
        };
        t.default = i
    }, function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: true});
        var r = t.use = function e(t) {
            if (!t.isSwupPlugin) {
                console.warn("Not swup plugin instance " + t + ".");
                return
            }
            this.plugins.push(t);
            t.swup = this;
            if (typeof t._beforeMount === "function") {
                t._beforeMount()
            }
            t.mount();
            return this.plugins
        };
        var i = t.unuse = function e(t) {
            var n = void 0;
            if (typeof t === "string") {
                n = this.plugins.find(function (e) {
                    return t === e.name
                })
            } else {
                n = t
            }
            if (!n) {
                console.warn("No such plugin.");
                return
            }
            n.unmount();
            if (typeof n._afterUnmount === "function") {
                n._afterUnmount()
            }
            var r = this.plugins.indexOf(n);
            this.plugins.splice(r, 1);
            return this.plugins
        };
        var a = t.findPlugin = function e(t) {
            return this.plugins.find(function (e) {
                return t === e.name
            })
        }
    }])
});