(()=>{"use strict";var a={n:e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return a.d(t,{a:t}),t},d:(e,t)=>{for(var l in t)a.o(t,l)&&!a.o(e,l)&&Object.defineProperty(e,l,{enumerable:!0,get:t[l]})},o:(a,e)=>Object.prototype.hasOwnProperty.call(a,e)};const e=jQuery;var t,l=a.n(e);l()(".unity-modaal-gallery-item__link").modaal({type:"image",before_open:function(a){t=a},after_open:function(a){var e=l()(t.target).parents(".unity-modaal-gallery");l()(".modaal-gallery-label").each((function(a,t){var n=e.find(".unity-modaal-gallery-item__caption[data-index='".concat(a,"']"));n.length>0&&l()(t).addClass("loaded").html(n.html())}))}})})();