!function(t){function e(r){if(n[r])return n[r].exports;var o=n[r]={i:r,l:!1,exports:{}};return t[r].call(o.exports,o,o.exports,e),o.l=!0,o.exports}var n={};e.m=t,e.c=n,e.i=function(t){return t},e.d=function(t,n,r){e.o(t,n)||Object.defineProperty(t,n,{configurable:!1,enumerable:!0,get:r})},e.n=function(t){var n=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(n,"a",n),n},e.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},e.p="",e(e.s=3)}([function(t,e){t.exports=jQuery},function(t,e,n){var r,o,a=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(t[r]=n[r])}return t},s="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t};!function(a,i){"object"===s(e)&&void 0!==t?t.exports=i():(r=i,void 0!==(o="function"==typeof r?r.call(e,n,e,t):r)&&(t.exports=o))}(0,function(){"use strict";var t={elements_selector:"img",container:document,threshold:300,data_src:"src",data_srcset:"srcset",class_loading:"loading",class_loaded:"loaded",class_error:"error",callback_load:null,callback_error:null,callback_set:null},e=function(t){return t.filter(function(t){return!t.dataset.wasProcessed})},n=function(t,e){var n=new t(e),r=new CustomEvent("LazyLoad::Initialized",{detail:{instance:n}});window.dispatchEvent(r)},r=function(t,e){var n=e.data_srcset,r=t.parentElement;if("PICTURE"===r.tagName)for(var o,a=0;o=r.children[a];a+=1)if("SOURCE"===o.tagName){var s=o.dataset[n];s&&o.setAttribute("srcset",s)}},o=function(t,e){var n=e.data_src,o=e.data_srcset,a=t.tagName,s=t.dataset[n];if("IMG"===a){r(t,e);var i=t.dataset[o];return i&&t.setAttribute("srcset",i),void(s&&t.setAttribute("src",s))}"IFRAME"!==a?s&&(t.style.backgroundImage='url("'+s+'")'):s&&t.setAttribute("src",s)},s=function(t,e){t&&t(e)},i=function(t,e,n){t.removeEventListener("load",e),t.removeEventListener("error",n)},c=function(t,e){var n=function n(o){l(o,!0,e),i(t,n,r)},r=function r(o){l(o,!1,e),i(t,n,r)};t.addEventListener("load",n),t.addEventListener("error",r)},l=function(t,e,n){var r=t.target;r.classList.remove(n.class_loading),r.classList.add(e?n.class_loaded:n.class_error),s(e?n.callback_load:n.callback_error,r)},u=function(t,e){["IMG","IFRAME"].indexOf(t.tagName)>-1&&(c(t,e),t.classList.add(e.class_loading)),o(t,e),t.dataset.wasProcessed=!0,s(e.callback_set,t)},f=function(e){this._settings=a({},t,e),this._setObserver(),this.update()};f.prototype={_setObserver:function(){var t=this;if("IntersectionObserver"in window){var n=this._settings;this._observer=new IntersectionObserver(function(r){r.forEach(function(e){if(e.isIntersecting){var r=e.target;u(r,n),t._observer.unobserve(r)}}),t._elements=e(t._elements)},{root:n.container===document?null:n.container,rootMargin:n.threshold+"px"})}},update:function(){var t=this,n=this._settings,r=n.container.querySelectorAll(n.elements_selector);this._elements=e(Array.prototype.slice.call(r)),this._observer?this._elements.forEach(function(e){t._observer.observe(e)}):(this._elements.forEach(function(t){u(t,n)}),this._elements=e(this._elements))},destroy:function(){var t=this;this._observer&&(e(this._elements).forEach(function(e){t._observer.unobserve(e)}),this._observer=null),this._elements=null,this._settings=null}};var d=window.lazyLoadOptions;return d&&function(t,e){if(e.length)for(var r,o=0;r=e[o];o+=1)n(t,r);else n(t,e)}(f,d),f})},,function(t,e,n){"use strict";(function(t){function e(t){return t&&t.__esModule?t:{default:t}}var r=n(1),o=e(r),a=640;new o.default;!function(t,e){t.get(visita.weather,function(e){t(".site-logo .weather").attr("title",visita.weather_text).text(e.current["temp_"+visita.weather_unit]+"°"+visita.weather_unit.toUpperCase())});var n=!1,r={type:"text/css",rel:"stylesheet"};t(function(){t("<link/>",Object.assign(r,{href:visita.styles})).appendTo("head"),t("<link/>",Object.assign(r,{href:visita.fonts})).appendTo("head")}),t(window).on("load resize orientationchange",function(){!n&&t(e).width()>=a&&(t("<link/>",Object.assign(r,{href:visita.tablet})).appendTo("head"),n=!0)}),t(".entry-header.float, .visita-widget .entry-header").on("click",function(e){if("post-edit-link"!==e.target.className){var n=t(this).parent().find("a.url");(e.ctrlKey||e.metaKey)&&n.attr({target:"_blank"}),t(this).parent().find("a.url")[0].click()}}),t('a[rel="external"]').each(function(e){var n=t(this).attr("href");"#"!==n&&""!==n?t(this).attr({target:"_blank"}):t(this).attr({rel:"bookmark"})}),window.top!==window.self&&delete window.top.onbeforeunload}(t,document),function(t,e){var n=t("#nav");if(n[0]){if(n.find(".menu-toggle")[0]){t(".menu-toggle").on("click",function(t){t.preventDefault(),n.toggleClass("show-menu")});var r=0,o=void 0;t(".menu-main .menu-item-has-children > a").on("click touchend",function(n){"click"==n.type&&t(e).width()>a||(n.preventDefault(),r++,r>1?(clearTimeout(o),n.target.href&&(document.location.href=n.target.href)):(o=setTimeout(function(){return r=0},300),t(n.target).parent().toggleClass("show").siblings().removeClass("show")))})}}}(t,document)}).call(e,n(0))}]);