!function(t){function e(o){if(n[o])return n[o].exports;var i=n[o]={i:o,l:!1,exports:{}};return t[o].call(i.exports,i,i.exports,e),i.l=!0,i.exports}var n={};e.m=t,e.c=n,e.i=function(t){return t},e.d=function(t,n,o){e.o(t,n)||Object.defineProperty(t,n,{configurable:!1,enumerable:!0,get:o})},e.n=function(t){var n=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(n,"a",n),n},e.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},e.p="",e(e.s=12)}({0:function(t,e){t.exports=jQuery},1:function(t,e,n){"use strict";var o,i,r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},s=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var o in n)Object.prototype.hasOwnProperty.call(n,o)&&(t[o]=n[o])}return t},l="function"==typeof Symbol&&"symbol"==r(Symbol.iterator)?function(t){return void 0===t?"undefined":r(t)}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":void 0===t?"undefined":r(t)};!function(r,s){"object"===l(e)&&void 0!==t?t.exports=s():(o=s,void 0!==(i="function"==typeof o?o.call(e,n,e,t):o)&&(t.exports=i))}(0,function(){var t=!("onscroll"in window)||/glebot/.test(navigator.userAgent),e=function(t,e){t&&t(e)},n=function(t){return t.getBoundingClientRect().top+window.pageYOffset-t.ownerDocument.documentElement.clientTop},o=function(t,e,o){return(e===window?window.innerHeight+window.pageYOffset:n(e)+e.offsetHeight)<=n(t)-o},i=function(t){return t.getBoundingClientRect().left+window.pageXOffset-t.ownerDocument.documentElement.clientLeft},r=function(t,e,n){var o=window.innerWidth;return(e===window?o+window.pageXOffset:i(e)+o)<=i(t)-n},l=function(t,e,o){return(e===window?window.pageYOffset:n(e))>=n(t)+o+t.offsetHeight},a=function(t,e,n){return(e===window?window.pageXOffset:i(e))>=i(t)+n+t.offsetWidth},c=function(t,e){var n,o="LazyLoad::Initialized",i=new t(e);try{n=new CustomEvent(o,{detail:{instance:i}})}catch(t){(n=document.createEvent("CustomEvent")).initCustomEvent(o,!1,!1,{instance:i})}window.dispatchEvent(n)},u="data-",d="was-processed",f="true",h=function(t,e){return t.getAttribute(u+e)},p=function(t){return e=d,n=f,t.setAttribute(u+e,n);var e,n},_=function(t){return h(t,d)===f},m=function(t,e,n){for(var o,i=0;o=t.children[i];i+=1)if("SOURCE"===o.tagName){var r=h(o,n);r&&o.setAttribute(e,r)}},g=function(t,e,n){n&&t.setAttribute(e,n)},v="undefined"!=typeof window,w=v&&"classList"in document.createElement("p"),b=function(t,e){w?t.classList.add(e):t.className+=(t.className?" ":"")+e},y=function(t,e){w?t.classList.remove(e):t.className=t.className.replace(new RegExp("(^|\\s+)"+e+"(\\s+|$)")," ").replace(/^\s+/,"").replace(/\s+$/,"")},E=function(t){this._settings=s({},{elements_selector:"img",container:window,threshold:300,throttle:150,data_src:"src",data_srcset:"srcset",data_sizes:"sizes",class_loading:"loading",class_loaded:"loaded",class_error:"error",class_initial:"initial",skip_invisible:!0,callback_load:null,callback_error:null,callback_set:null,callback_processed:null,callback_enter:null},t),this._queryOriginNode=this._settings.container===window?document:this._settings.container,this._previousLoopTime=0,this._loopTimeout=null,this._boundHandleScroll=this.handleScroll.bind(this),this._isFirstLoop=!0,window.addEventListener("resize",this._boundHandleScroll),this.update()};E.prototype={_reveal:function(t,n){if(n||!_(t)){var o=this._settings,i=function n(){o&&(t.removeEventListener("load",r),t.removeEventListener("error",n),y(t,o.class_loading),b(t,o.class_error),e(o.callback_error,t))},r=function n(){o&&(y(t,o.class_loading),b(t,o.class_loaded),t.removeEventListener("load",n),t.removeEventListener("error",i),e(o.callback_load,t))};e(o.callback_enter,t),-1<["IMG","IFRAME","VIDEO"].indexOf(t.tagName)&&(t.addEventListener("load",r),t.addEventListener("error",i),b(t,o.class_loading)),function(t,e){var n=e.data_sizes,o=e.data_srcset,i=e.data_src,r=h(t,i),s=t.tagName;if("IMG"===s){var l=t.parentNode;l&&"PICTURE"===l.tagName&&m(l,"srcset",o);var a=h(t,n);g(t,"sizes",a);var c=h(t,o);return g(t,"srcset",c),g(t,"src",r)}if("IFRAME"!==s)return"VIDEO"===s?(m(t,"src",i),g(t,"src",r)):r&&(t.style.backgroundImage='url("'+r+'")');g(t,"src",r)}(t,o),e(o.callback_set,t)}},_loopThroughElements:function(n){var i,s,c,u=this._settings,d=this._elements,f=d?d.length:0,h=void 0,_=[],m=this._isFirstLoop;for(h=0;h<f;h++){var g=d[h];u.skip_invisible&&null===g.offsetParent||!t&&!n&&(i=g,s=u.container,c=u.threshold,o(i,s,c)||l(i,s,c)||r(i,s,c)||a(i,s,c))||(m&&b(g,u.class_initial),this.load(g),_.push(h),p(g))}for(;_.length;)d.splice(_.pop(),1),e(u.callback_processed,d.length);0===f&&this._stopScrollHandler(),m&&(this._isFirstLoop=!1)},_purgeElements:function(){var t=this._elements,e=t.length,n=void 0,o=[];for(n=0;n<e;n++){var i=t[n];_(i)&&o.push(n)}for(;0<o.length;)t.splice(o.pop(),1)},_startScrollHandler:function(){this._isHandlingScroll||(this._isHandlingScroll=!0,this._settings.container.addEventListener("scroll",this._boundHandleScroll))},_stopScrollHandler:function(){this._isHandlingScroll&&(this._isHandlingScroll=!1,this._settings.container.removeEventListener("scroll",this._boundHandleScroll))},handleScroll:function(){var t=this._settings.throttle;if(0!==t){var e=Date.now(),n=t-(e-this._previousLoopTime);n<=0||t<n?(this._loopTimeout&&(clearTimeout(this._loopTimeout),this._loopTimeout=null),this._previousLoopTime=e,this._loopThroughElements()):this._loopTimeout||(this._loopTimeout=setTimeout(function(){this._previousLoopTime=Date.now(),this._loopTimeout=null,this._loopThroughElements()}.bind(this),n))}else this._loopThroughElements()},loadAll:function(){this._loopThroughElements(!0)},update:function(){this._elements=Array.prototype.slice.call(this._queryOriginNode.querySelectorAll(this._settings.elements_selector)),this._purgeElements(),this._loopThroughElements(),this._startScrollHandler()},destroy:function(){window.removeEventListener("resize",this._boundHandleScroll),this._loopTimeout&&(clearTimeout(this._loopTimeout),this._loopTimeout=null),this._stopScrollHandler(),this._elements=null,this._queryOriginNode=null,this._settings=null},load:function(t,e){this._reveal(t,e)}};var T=window.lazyLoadOptions;return v&&T&&function(t,e){var n=e.length;if(n)for(var o=0;o<n;o++)c(t,e[o]);else c(t,e)}(E,T),E})},12:function(t,e,n){"use strict";(function(t){function e(t){return t&&t.__esModule?t:{default:t}}var o=n(1),i=e(o),r=640;new i.default;!function(t,e){t.get(visita.weather,function(e){t(".site-logo .weather").attr("title",visita.weather_text).text(Math.round(e.current["temp_"+visita.weather_unit])+"°"+visita.weather_unit.toUpperCase())});var n=!1,o={type:"text/css",rel:"stylesheet"};t(function(){t("<link/>",Object.assign(o,{href:visita.styles})).appendTo("head"),t("<link/>",Object.assign(o,{href:visita.fonts})).appendTo("head")}),t(window).on("load resize orientationchange",function(){!n&&t(e).width()>=r&&(t("<link/>",Object.assign(o,{href:visita.tablet})).appendTo("head"),n=!0)}),t(".entry-header.float, .visita-widget .entry-header").on("click",function(e){if("post-edit-link"!==e.target.className){var n=t(this).parent().find("a.url");(e.ctrlKey||e.metaKey)&&n.attr({target:"_blank"}),t(this).parent().find("a.url")[0].click()}}),t('a[rel="external"]').each(function(e){var n=t(this).attr("href");"#"!==n&&""!==n?t(this).attr({target:"_blank"}):t(this).attr({rel:"bookmark"})}),window.top!==window.self&&delete window.top.onbeforeunload}(t,document),function(t,e){var n=t("#nav");if(n[0]){if(n.find(".menu-toggle")[0]){t(".menu-toggle").on("click",function(t){t.preventDefault(),n.toggleClass("show-menu")});var o=0,i=void 0;t(".menu-main .menu-item-has-children > a").on("click touchend",function(n){"click"==n.type&&t(e).width()>r||(n.preventDefault(),o++,o>1?(clearTimeout(i),n.target.href&&(document.location.href=n.target.href)):(i=setTimeout(function(){return o=0},300),t(n.target).parent().toggleClass("show").siblings().removeClass("show")))})}}}(t,document)}).call(e,n(0))}});