var inline_2Checkout = {};
(function (a) {
    var b = "srcdoc" in a.createElement("iframe") ? function (a, b) {
            a.setAttribute("srcdoc", b)
        } : function (a, b) {
            a.setAttribute("src", "javascript: '" + b.replace(/([\\'])/g, "\\$1") + "'")
        }, c = function () {
            var b = !1,
                c = !1,
                d = a.addEventListener ? function (b, c, d) {
                    a[(d ? "remove" : "add") + "EventListener"](b, c, !1)
                } : function (b, c, d) {
                    a[(d ? "de" : "at") + "tachEvent"]("on" + b, c)
                }, e = function (a, b) {
                    d(a, b)
                }, f = function (a, b) {
                    d(a, b, !0)
                }, g = function () {
                    var b = "complete" === a.readyState || "interactive" === a.readyState;
                    if (!b && !a.addEventListener && a.body) try {
                        a.createElement("div").doScroll("left"), b = !0
                    } catch (c) {}
                    return b
                };
            return function (d) {
                b = b || g();
                var h = function () {
                    b = !0, f("readystatechange", i), f("DOMContentLoaded", h), f("load", h), c && window.clearInterval(c), d()
                }, i = function () {
                        g() && h()
                    };
                b ? d() : (e("readystatechange", i), e("DOMContentLoaded", h), e("load", h), a.addEventListener || (c = window.setInterval(i, 10)))
            }
        }(),
        d = function (b, c) {
            var d = a.createElement(b);
            for (var e in c) c.hasOwnProperty(e) && ("text" === e ? (d.type = "text/css", d.styleSheet ? d.styleSheet.cssText = c[e] : d.appendChild(a.createTextNode(c[e]))) : (d[e] = c[e], d.setAttribute(e, c[e])));
            return d
        }, e = function () {
            var b = d("style", {
                text: "      #tco_lightbox {        display: none;        position: fixed;        top: 0;        bottom: 0;        left: 0;        right: 0;        z-index: 9999      }      #tco_lightbox_iframe {        width:100%;        height:100%;        border:none;        visibility: hidden;      }    "
            }),
                c = a.getElementsByTagName("head")[0];
            c.insertBefore(b, c.firstChild)
        };
    e(), c(function () {
        function j() {
            return /android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4))
        }
        var c = a.getElementsByTagName("body")[0],
            e = d("div", {
                className: "tco_lightbox",
                id: "tco_lightbox"
            }),
            f = function () {
                e.style.display = "none", b(g, "<html><head><style>html { background-color: transparent; }</style></head><body></body></html>")
            }, g = d("iframe", {
                className: "tco_lightbox_iframe",
                name: "tco_lightbox_iframe",
                id: "tco_lightbox_iframe",
                frameborder: "0",
                allowtransparency: "true",
                onload: "this.style.visibility = 'visible';"
            });
        e.appendChild(g), null !== c && c.appendChild(e), f();
        var h = function () {
            var c = a.getElementById("tco_lightbox_iframe");
            return c.style.visibility = "hidden", e.style.display = "block", !0
        }, i = function (a) {
                var b = /^(http|https):\/\/[\w.-]+\.2checkout\.[^\/]+\/?$/;
                return a.match(b) ? !0 : void 0
            };
        window.addEventListener ? window.addEventListener("message", function (a) {
            i(a.origin) && ("close" === a.data ? f() : (f(), window.location.href = a.data))
        }, !1) : window.attachEvent && window.attachEvent("onmessage", function (a) {
            i(a.origin) && ("close" === a.data ? f() : (f(), window.location.href = a.data))
        });
        for (var k = a.getElementsByTagName("form"), l = 0; k.length > l; l++) {
            var m = k[l];
            if (/\/checkout\/(purchase|spurchase)$/.test(m.action)) {
                var n = d("input", {
                    name: "tco_use_inline",
                    type: "hidden",
                    value: "1"
                });
                if (m.appendChild(n), j()) {
                    var o = d("input", {
                        name: "tco_inline_mobile",
                        type: "hidden",
                        value: "1"
                    });
                    m.appendChild(o)
                } else m.target = "tco_lightbox_iframe";
                m.onsubmit = h
            }
        }
    })
})(document);