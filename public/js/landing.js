!function(e,t){"object"==typeof exports&&"object"==typeof module?module.exports=t():"function"==typeof define&&define.amd?define([],t):"object"==typeof exports?exports.AOS=t():e.AOS=t()}(this,(function(){return function(e){function t(o){if(n[o])return n[o].exports;var i=n[o]={exports:{},id:o,loaded:!1};return e[o].call(i.exports,i,i.exports,t),i.loaded=!0,i.exports}var n={};return t.m=e,t.c=n,t.p="dist/",t(0)}([function(e,t,n){"use strict";function o(e){return e&&e.__esModule?e:{default:e}}var i=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var o in n)Object.prototype.hasOwnProperty.call(n,o)&&(e[o]=n[o])}return e},a=(o(n(1)),n(6)),r=o(a),u=o(n(7)),s=o(n(8)),c=o(n(9)),f=o(n(10)),d=o(n(11)),l=o(n(14)),p=[],m=!1,v=document.all&&!window.atob,b={offset:120,delay:0,easing:"ease",duration:400,disable:!1,once:!1,startEvent:"DOMContentLoaded",throttleDelay:99,debounceDelay:50,disableMutationObserver:!1},h=function(){if(arguments.length>0&&void 0!==arguments[0]&&arguments[0]&&(m=!0),m)return p=(0,d.default)(p,b),(0,f.default)(p,b.once),p},y=function(){p=(0,l.default)(),h()};e.exports={init:function(e){return b=i(b,e),p=(0,l.default)(),function(e){return!0===e||"mobile"===e&&c.default.mobile()||"phone"===e&&c.default.phone()||"tablet"===e&&c.default.tablet()||"function"==typeof e&&!0===e()}(b.disable)||v?void p.forEach((function(e,t){e.node.removeAttribute("data-aos"),e.node.removeAttribute("data-aos-easing"),e.node.removeAttribute("data-aos-duration"),e.node.removeAttribute("data-aos-delay")})):(document.querySelector("body").setAttribute("data-aos-easing",b.easing),document.querySelector("body").setAttribute("data-aos-duration",b.duration),document.querySelector("body").setAttribute("data-aos-delay",b.delay),"DOMContentLoaded"===b.startEvent&&["complete","interactive"].indexOf(document.readyState)>-1?h(!0):"load"===b.startEvent?window.addEventListener(b.startEvent,(function(){h(!0)})):document.addEventListener(b.startEvent,(function(){h(!0)})),window.addEventListener("resize",(0,u.default)(h,b.debounceDelay,!0)),window.addEventListener("orientationchange",(0,u.default)(h,b.debounceDelay,!0)),window.addEventListener("scroll",(0,r.default)((function(){(0,f.default)(p,b.once)}),b.throttleDelay)),b.disableMutationObserver||(0,s.default)("[data-aos]",y),p)},refresh:h,refreshHard:y}},function(e,t){},,,,,function(e,t){(function(t){"use strict";function n(e,t,n){function i(t){var n=d,o=l;return d=l=void 0,h=t,m=e.apply(o,n)}function r(e){var n=e-b;return void 0===b||n>=t||n<0||k&&e-h>=p}function s(){var e=x();return r(e)?c(e):void(v=setTimeout(s,function(e){var n=t-(e-b);return k?w(n,p-(e-h)):n}(e)))}function c(e){return v=void 0,O&&d?i(e):(d=l=void 0,m)}function f(){var e=x(),n=r(e);if(d=arguments,l=this,b=e,n){if(void 0===v)return function(e){return h=e,v=setTimeout(s,t),y?i(e):m}(b);if(k)return v=setTimeout(s,t),i(b)}return void 0===v&&(v=setTimeout(s,t)),m}var d,l,p,m,v,b,h=0,y=!1,k=!1,O=!0;if("function"!=typeof e)throw new TypeError(u);return t=a(t)||0,o(n)&&(y=!!n.leading,p=(k="maxWait"in n)?g(a(n.maxWait)||0,t):p,O="trailing"in n?!!n.trailing:O),f.cancel=function(){void 0!==v&&clearTimeout(v),h=0,d=b=l=v=void 0},f.flush=function(){return void 0===v?m:c(x())},f}function o(e){var t=void 0===e?"undefined":r(e);return!!e&&("object"==t||"function"==t)}function i(e){return"symbol"==(void 0===e?"undefined":r(e))||function(e){return!!e&&"object"==(void 0===e?"undefined":r(e))}(e)&&y.call(e)==c}function a(e){if("number"==typeof e)return e;if(i(e))return s;if(o(e)){var t="function"==typeof e.valueOf?e.valueOf():e;e=o(t)?t+"":t}if("string"!=typeof e)return 0===e?e:+e;e=e.replace(f,"");var n=l.test(e);return n||p.test(e)?m(e.slice(2),n?2:8):d.test(e)?s:+e}var r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},u="Expected a function",s=NaN,c="[object Symbol]",f=/^\s+|\s+$/g,d=/^[-+]0x[0-9a-f]+$/i,l=/^0b[01]+$/i,p=/^0o[0-7]+$/i,m=parseInt,v="object"==(void 0===t?"undefined":r(t))&&t&&t.Object===Object&&t,b="object"==("undefined"==typeof self?"undefined":r(self))&&self&&self.Object===Object&&self,h=v||b||Function("return this")(),y=Object.prototype.toString,g=Math.max,w=Math.min,x=function(){return h.Date.now()};e.exports=function(e,t,i){var a=!0,r=!0;if("function"!=typeof e)throw new TypeError(u);return o(i)&&(a="leading"in i?!!i.leading:a,r="trailing"in i?!!i.trailing:r),n(e,t,{leading:a,maxWait:t,trailing:r})}}).call(t,function(){return this}())},function(e,t){(function(t){"use strict";function n(e){var t=void 0===e?"undefined":a(e);return!!e&&("object"==t||"function"==t)}function o(e){return"symbol"==(void 0===e?"undefined":a(e))||function(e){return!!e&&"object"==(void 0===e?"undefined":a(e))}(e)&&h.call(e)==s}function i(e){if("number"==typeof e)return e;if(o(e))return u;if(n(e)){var t="function"==typeof e.valueOf?e.valueOf():e;e=n(t)?t+"":t}if("string"!=typeof e)return 0===e?e:+e;e=e.replace(c,"");var i=d.test(e);return i||l.test(e)?p(e.slice(2),i?2:8):f.test(e)?u:+e}var a="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},r="Expected a function",u=NaN,s="[object Symbol]",c=/^\s+|\s+$/g,f=/^[-+]0x[0-9a-f]+$/i,d=/^0b[01]+$/i,l=/^0o[0-7]+$/i,p=parseInt,m="object"==(void 0===t?"undefined":a(t))&&t&&t.Object===Object&&t,v="object"==("undefined"==typeof self?"undefined":a(self))&&self&&self.Object===Object&&self,b=m||v||Function("return this")(),h=Object.prototype.toString,y=Math.max,g=Math.min,w=function(){return b.Date.now()};e.exports=function(e,t,o){function a(t){var n=d,o=l;return d=l=void 0,h=t,m=e.apply(o,n)}function u(e){var n=e-b;return void 0===b||n>=t||n<0||k&&e-h>=p}function s(){var e=w();return u(e)?c(e):void(v=setTimeout(s,function(e){var n=t-(e-b);return k?g(n,p-(e-h)):n}(e)))}function c(e){return v=void 0,O&&d?a(e):(d=l=void 0,m)}function f(){var e=w(),n=u(e);if(d=arguments,l=this,b=e,n){if(void 0===v)return function(e){return h=e,v=setTimeout(s,t),x?a(e):m}(b);if(k)return v=setTimeout(s,t),a(b)}return void 0===v&&(v=setTimeout(s,t)),m}var d,l,p,m,v,b,h=0,x=!1,k=!1,O=!0;if("function"!=typeof e)throw new TypeError(r);return t=i(t)||0,n(o)&&(x=!!o.leading,p=(k="maxWait"in o)?y(i(o.maxWait)||0,t):p,O="trailing"in o?!!o.trailing:O),f.cancel=function(){void 0!==v&&clearTimeout(v),h=0,d=b=l=v=void 0},f.flush=function(){return void 0===v?m:c(w())},f}}).call(t,function(){return this}())},function(e,t){"use strict";function n(e){e&&e.forEach((function(e){var t=Array.prototype.slice.call(e.addedNodes),n=Array.prototype.slice.call(e.removedNodes),o=t.concat(n).filter((function(e){return e.hasAttribute&&e.hasAttribute("data-aos")})).length;o&&a()}))}Object.defineProperty(t,"__esModule",{value:!0});var o=window.document,i=window.MutationObserver||window.WebKitMutationObserver||window.MozMutationObserver,a=function(){};t.default=function(e,t){var r=new i(n);a=t,r.observe(o.documentElement,{childList:!0,subtree:!0,removedNodes:!0})}},function(e,t){"use strict";function n(){return navigator.userAgent||navigator.vendor||window.opera||""}Object.defineProperty(t,"__esModule",{value:!0});var o=function(){function e(e,t){for(var n=0;n<t.length;n++){var o=t[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}return function(t,n,o){return n&&e(t.prototype,n),o&&e(t,o),t}}(),i=/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i,a=/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i,r=/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i,u=/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i,s=function(){function e(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e)}return o(e,[{key:"phone",value:function(){var e=n();return!(!i.test(e)&&!a.test(e.substr(0,4)))}},{key:"mobile",value:function(){var e=n();return!(!r.test(e)&&!u.test(e.substr(0,4)))}},{key:"tablet",value:function(){return this.mobile()&&!this.phone()}}]),e}();t.default=new s},function(e,t){"use strict";Object.defineProperty(t,"__esModule",{value:!0});t.default=function(e,t){var n=window.pageYOffset,o=window.innerHeight;e.forEach((function(e,i){!function(e,t,n){var o=e.node.getAttribute("data-aos-once");t>e.position?e.node.classList.add("aos-animate"):void 0!==o&&("false"===o||!n&&"true"!==o)&&e.node.classList.remove("aos-animate")}(e,o+n,t)}))}},function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var o=function(e){return e&&e.__esModule?e:{default:e}}(n(12));t.default=function(e,t){return e.forEach((function(e,n){e.node.classList.add("aos-init"),e.position=(0,o.default)(e.node,t.offset)})),e}},function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var o=function(e){return e&&e.__esModule?e:{default:e}}(n(13));t.default=function(e,t){var n=0,i=0,a=window.innerHeight,r={offset:e.getAttribute("data-aos-offset"),anchor:e.getAttribute("data-aos-anchor"),anchorPlacement:e.getAttribute("data-aos-anchor-placement")};switch(r.offset&&!isNaN(r.offset)&&(i=parseInt(r.offset)),r.anchor&&document.querySelectorAll(r.anchor)&&(e=document.querySelectorAll(r.anchor)[0]),n=(0,o.default)(e).top,r.anchorPlacement){case"top-bottom":break;case"center-bottom":n+=e.offsetHeight/2;break;case"bottom-bottom":n+=e.offsetHeight;break;case"top-center":n+=a/2;break;case"bottom-center":n+=a/2+e.offsetHeight;break;case"center-center":n+=a/2+e.offsetHeight/2;break;case"top-top":n+=a;break;case"bottom-top":n+=e.offsetHeight+a;break;case"center-top":n+=e.offsetHeight/2+a}return r.anchorPlacement||r.offset||isNaN(t)||(i=t),n+i}},function(e,t){"use strict";Object.defineProperty(t,"__esModule",{value:!0});t.default=function(e){for(var t=0,n=0;e&&!isNaN(e.offsetLeft)&&!isNaN(e.offsetTop);)t+=e.offsetLeft-("BODY"!=e.tagName?e.scrollLeft:0),n+=e.offsetTop-("BODY"!=e.tagName?e.scrollTop:0),e=e.offsetParent;return{top:n,left:t}}},function(e,t){"use strict";Object.defineProperty(t,"__esModule",{value:!0});t.default=function(e){return e=e||document.querySelectorAll("[data-aos]"),Array.prototype.map.call(e,(function(e){return{node:e}}))}}])})),function(e,t,n){e.fn.responsiveSlides=function(o){var i=e.extend({auto:!0,speed:500,timeout:4e3,pager:!1,nav:!1,random:!1,pause:!1,pauseControls:!0,prevText:"Previous",nextText:"Next",maxwidth:"",navContainer:"",manualControls:"",namespace:"rslides",before:e.noop,after:e.noop},o);return this.each((function(){n++;var a,r,u,s,c,f,d=e(this),l=0,p=d.children(),m=p.size(),v=parseFloat(i.speed),b=parseFloat(i.timeout),h=parseFloat(i.maxwidth),y=i.namespace,g=y+"_nav "+(M=y+n)+"_nav",w=y+"_here",x=M+"_on",k=M+"_s",O=e("<ul class='"+y+"_tabs "+M+"_tabs' />"),j={float:"left",position:"relative",opacity:1,zIndex:2},I={float:"none",position:"absolute",opacity:0,zIndex:1},C=function(){var e=(document.body||document.documentElement).style;if("string"==typeof e[n="transition"])return!0;a=["Moz","Webkit","Khtml","O","ms"];var t,n=n.charAt(0).toUpperCase()+n.substr(1);for(t=0;t<a.length;t++)if("string"==typeof e[a[t]+n])return!0;return!1}(),_=function(t){i.before(t),C?(p.removeClass(x).css(I).eq(t).addClass(x).css(j),l=t,setTimeout((function(){i.after(t)}),v)):p.stop().fadeOut(v,(function(){e(this).removeClass(x).css(I).css("opacity",1)})).eq(t).fadeIn(v,(function(){e(this).addClass(x).css(j),i.after(t),l=t}))};if(i.random&&(p.sort((function(){return Math.round(Math.random())-.5})),d.empty().append(p)),p.each((function(e){this.id=k+e})),d.addClass(y+" "+M),o&&o.maxwidth&&d.css("max-width",h),p.hide().css(I).eq(0).addClass(x).css(j).show(),C&&p.show().css({"-webkit-transition":"opacity "+v+"ms ease-in-out","-moz-transition":"opacity "+v+"ms ease-in-out","-o-transition":"opacity "+v+"ms ease-in-out",transition:"opacity "+v+"ms ease-in-out"}),1<p.size()){if(b<v+100)return;if(i.pager&&!i.manualControls){var T=[];p.each((function(e){T+="<li><a href='#' class='"+k+(e+=1)+"'>"+e+"</a></li>"})),O.append(T),o.navContainer?e(i.navContainer).append(O):d.after(O)}if(i.manualControls&&(O=e(i.manualControls)).addClass(y+"_tabs "+M+"_tabs"),(i.pager||i.manualControls)&&O.find("li").each((function(t){e(this).addClass(k+(t+1))})),(i.pager||i.manualControls)&&(f=O.find("a"),r=function(e){f.closest("li").removeClass(w).eq(e).addClass(w)}),i.auto&&(u=function(){c=setInterval((function(){p.stop(!0,!0);var e=l+1<m?l+1:0;(i.pager||i.manualControls)&&r(e),_(e)}),b)})(),s=function(){i.auto&&(clearInterval(c),u())},i.pause&&d.hover((function(){clearInterval(c)}),(function(){s()})),(i.pager||i.manualControls)&&(f.bind("click",(function(t){t.preventDefault(),i.pauseControls||s(),t=f.index(this),l===t||e("."+x).queue("fx").length||(r(t),_(t))})).eq(0).closest("li").addClass(w),i.pauseControls&&f.hover((function(){clearInterval(c)}),(function(){s()}))),i.nav){y="<a href='#' class='"+g+" prev'>"+i.prevText+"</a><a href='#' class='"+g+" next'>"+i.nextText+"</a>",o.navContainer?e(i.navContainer).append(y):d.after(y);var M,z=(M=e("."+M+"_nav")).filter(".prev");M.bind("click",(function(t){if(t.preventDefault(),!(t=e("."+x)).queue("fx").length){var n=p.index(t);t=n-1,n=n+1<m?l+1:0,_(e(this)[0]===z[0]?t:n),(i.pager||i.manualControls)&&r(e(this)[0]===z[0]?t:n),i.pauseControls||s()}})),i.pauseControls&&M.hover((function(){clearInterval(c)}),(function(){s()}))}}if(void 0===document.body.style.maxWidth&&o.maxwidth){var S=function(){d.css("width","100%"),d.width()>h&&d.css("width",h)};S(),e(t).bind("resize",(function(){S()}))}}))}}(jQuery,this,0),function(e){e.fn.UItoTop=function(t){var n=e.extend({text:"To Top",min:200,inDelay:600,outDelay:400,containerID:"toTop",containerHoverID:"toTopHover",scrollSpeed:1e3,easingType:"linear"},t),o="#"+n.containerID,i="#"+n.containerHoverID;e("body").append('<a href="#" id="'+n.containerID+'">'+n.text+"</a>"),e(o).hide().on("click.UItoTop",(function(){return e("html, body").animate({scrollTop:0},n.scrollSpeed,n.easingType),e("#"+n.containerHoverID,this).stop().animate({opacity:0},n.inDelay,n.easingType),!1})).prepend('<span id="'+n.containerHoverID+'"></span>').hover((function(){e(i,this).stop().animate({opacity:1},600,"linear")}),(function(){e(i,this).stop().animate({opacity:0},700,"linear")})),e(window).scroll((function(){var t=e(window).scrollTop();void 0===document.body.style.maxHeight&&e(o).css({position:"absolute",top:t+e(window).height()-50}),t>n.min?e(o).fadeIn(n.inDelay):e(o).fadeOut(n.Outdelay)}))}}(jQuery),function(e){"function"==typeof define&&define.amd?define(["jquery"],(function(t){return e(t)})):"object"==typeof module&&"object"==typeof module.exports?exports=e(require("jquery")):e(jQuery)}((function(e){e.easing.jswing=e.easing.swing;var t=Math.pow,n=Math.sqrt,o=Math.sin,i=Math.cos,a=Math.PI,r=1.70158,u=1.525*r,s=r+1,c=2*a/3,f=2*a/4.5;function d(e){var t=7.5625,n=2.75;return e<1/n?t*e*e:e<2/n?t*(e-=1.5/n)*e+.75:e<2.5/n?t*(e-=2.25/n)*e+.9375:t*(e-=2.625/n)*e+.984375}e.extend(e.easing,{def:"easeOutQuad",swing:function(t){return e.easing[e.easing.def](t)},easeInQuad:function(e){return e*e},easeOutQuad:function(e){return 1-(1-e)*(1-e)},easeInOutQuad:function(e){return e<.5?2*e*e:1-t(-2*e+2,2)/2},easeInCubic:function(e){return e*e*e},easeOutCubic:function(e){return 1-t(1-e,3)},easeInOutCubic:function(e){return e<.5?4*e*e*e:1-t(-2*e+2,3)/2},easeInQuart:function(e){return e*e*e*e},easeOutQuart:function(e){return 1-t(1-e,4)},easeInOutQuart:function(e){return e<.5?8*e*e*e*e:1-t(-2*e+2,4)/2},easeInQuint:function(e){return e*e*e*e*e},easeOutQuint:function(e){return 1-t(1-e,5)},easeInOutQuint:function(e){return e<.5?16*e*e*e*e*e:1-t(-2*e+2,5)/2},easeInSine:function(e){return 1-i(e*a/2)},easeOutSine:function(e){return o(e*a/2)},easeInOutSine:function(e){return-(i(a*e)-1)/2},easeInExpo:function(e){return 0===e?0:t(2,10*e-10)},easeOutExpo:function(e){return 1===e?1:1-t(2,-10*e)},easeInOutExpo:function(e){return 0===e?0:1===e?1:e<.5?t(2,20*e-10)/2:(2-t(2,-20*e+10))/2},easeInCirc:function(e){return 1-n(1-t(e,2))},easeOutCirc:function(e){return n(1-t(e-1,2))},easeInOutCirc:function(e){return e<.5?(1-n(1-t(2*e,2)))/2:(n(1-t(-2*e+2,2))+1)/2},easeInElastic:function(e){return 0===e?0:1===e?1:-t(2,10*e-10)*o((10*e-10.75)*c)},easeOutElastic:function(e){return 0===e?0:1===e?1:t(2,-10*e)*o((10*e-.75)*c)+1},easeInOutElastic:function(e){return 0===e?0:1===e?1:e<.5?-t(2,20*e-10)*o((20*e-11.125)*f)/2:t(2,-20*e+10)*o((20*e-11.125)*f)/2+1},easeInBack:function(e){return s*e*e*e-r*e*e},easeOutBack:function(e){return 1+s*t(e-1,3)+r*t(e-1,2)},easeInOutBack:function(e){return e<.5?t(2*e,2)*(7.189819*e-u)/2:(t(2*e-2,2)*((u+1)*(2*e-2)+u)+2)/2},easeInBounce:function(e){return 1-d(1-e)},easeOutBounce:d,easeInOutBounce:function(e){return e<.5?(1-d(1-2*e))/2:(1+d(2*e-1))/2}})}));
