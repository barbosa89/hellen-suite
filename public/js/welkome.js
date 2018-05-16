/*
 * jQuery EasIng v1.1.2 - http://gsgd.co.uk/sandbox/jquery.easIng.php
 *
 * Uses the built In easIng capabilities added In jQuery 1.1
 * to offer multiple easIng options
 *
 * Copyright (c) 2007 George Smith
 * Licensed under the MIT License:
 *   http://www.opensource.org/licenses/mit-license.php
 */

// t: current time, b: begInnIng value, c: change In value, d: duration

jQuery.extend( jQuery.easing,
{
	easeInQuad: function (x, t, b, c, d) {
		return c*(t/=d)*t + b;
	},
	easeOutQuad: function (x, t, b, c, d) {
		return -c *(t/=d)*(t-2) + b;
	},
	easeInOutQuad: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t + b;
		return -c/2 * ((--t)*(t-2) - 1) + b;
	},
	easeInCubic: function (x, t, b, c, d) {
		return c*(t/=d)*t*t + b;
	},
	easeOutCubic: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t + 1) + b;
	},
	easeInOutCubic: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t + b;
		return c/2*((t-=2)*t*t + 2) + b;
	},
	easeInQuart: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t + b;
	},
	easeOutQuart: function (x, t, b, c, d) {
		return -c * ((t=t/d-1)*t*t*t - 1) + b;
	},
	easeInOutQuart: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t + b;
		return -c/2 * ((t-=2)*t*t*t - 2) + b;
	},
	easeInQuint: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t*t + b;
	},
	easeOutQuint: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t*t*t + 1) + b;
	},
	easeInOutQuint: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t*t + b;
		return c/2*((t-=2)*t*t*t*t + 2) + b;
	},
	easeInSine: function (x, t, b, c, d) {
		return -c * Math.cos(t/d * (Math.PI/2)) + c + b;
	},
	easeOutSine: function (x, t, b, c, d) {
		return c * Math.sin(t/d * (Math.PI/2)) + b;
	},
	easeInOutSine: function (x, t, b, c, d) {
		return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;
	},
	easeInExpo: function (x, t, b, c, d) {
		return (t==0) ? b : c * Math.pow(2, 10 * (t/d - 1)) + b;
	},
	easeOutExpo: function (x, t, b, c, d) {
		return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
	},
	easeInOutExpo: function (x, t, b, c, d) {
		if (t==0) return b;
		if (t==d) return b+c;
		if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
		return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
	},
	easeInCirc: function (x, t, b, c, d) {
		return -c * (Math.sqrt(1 - (t/=d)*t) - 1) + b;
	},
	easeOutCirc: function (x, t, b, c, d) {
		return c * Math.sqrt(1 - (t=t/d-1)*t) + b;
	},
	easeInOutCirc: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return -c/2 * (Math.sqrt(1 - t*t) - 1) + b;
		return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b;
	},
	easeInElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return -(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
	},
	easeOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
	},
	easeInOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d/2)==2) return b+c;  if (!p) p=d*(.3*1.5);
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		if (t < 1) return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
		return a*Math.pow(2,-10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )*.5 + c + b;
	},
	easeInBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*(t/=d)*t*((s+1)*t - s) + b;
	},
	easeOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
	},
	easeInOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158; 
		if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
		return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
	},
	easeInBounce: function (x, t, b, c, d) {
		return c - jQuery.easing.easeOutBounce (x, d-t, 0, c, d) + b;
	},
	easeOutBounce: function (x, t, b, c, d) {
		if ((t/=d) < (1/2.75)) {
			return c*(7.5625*t*t) + b;
		} else if (t < (2/2.75)) {
			return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
		} else if (t < (2.5/2.75)) {
			return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
		} else {
			return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
		}
	},
	easeInOutBounce: function (x, t, b, c, d) {
		if (t < d/2) return jQuery.easing.easeInBounce (x, t*2, 0, c, d) * .5 + b;
		return jQuery.easing.easeOutBounce (x, t*2-d, 0, c, d) * .5 + c*.5 + b;
	}
});

/* UItoTop jQuery Plugin 1.2 | Matt Varone | http://www.mattvarone.com/web-design/uitotop-jquery-plugin */
(function($){$.fn.UItoTop=function(options){var defaults={text:'To Top',min:200,inDelay:600,outDelay:400,containerID:'toTop',containerHoverID:'toTopHover',scrollSpeed:1000,easingType:'linear'},settings=$.extend(defaults,options),containerIDhash='#'+settings.containerID,containerHoverIDHash='#'+settings.containerHoverID;$('body').append('<a href="#" id="'+settings.containerID+'">'+settings.text+'</a>');$(containerIDhash).hide().on('click.UItoTop',function(){$('html, body').animate({scrollTop:0},settings.scrollSpeed,settings.easingType);$('#'+settings.containerHoverID,this).stop().animate({'opacity':0},settings.inDelay,settings.easingType);return false;}).prepend('<span id="'+settings.containerHoverID+'"></span>').hover(function(){$(containerHoverIDHash,this).stop().animate({'opacity':1},600,'linear');},function(){$(containerHoverIDHash,this).stop().animate({'opacity':0},700,'linear');});$(window).scroll(function(){var sd=$(window).scrollTop();if(typeof document.body.style.maxHeight==="undefined"){$(containerIDhash).css({'position':'absolute','top':sd+$(window).height()-50});}
if(sd>settings.min)
$(containerIDhash).fadeIn(settings.inDelay);else
$(containerIDhash).fadeOut(settings.Outdelay);});};})(jQuery);
/*!
* jquery.countup.js 1.0.3
*
* Copyright 2016, Adrián Guerra Marrero http://agmstudio.io @AGMStudio_io
* Released under the MIT License
*
* Date: Oct 27, 2016
*/
(function( $ ){
  "use strict";

  $.fn.countUp = function( options ) {

    // Defaults
    var settings = $.extend({
        'time': 2000,
        'delay': 10
    }, options);

    return this.each(function(){

        // Store the object
        var $this = $(this);
        var $settings = settings;

        var counterUpper = function() {
            if(!$this.data('counterupTo')) {
                $this.data('counterupTo',$this.text());
            }
            var time = parseInt($this.data("counter-time")) > 0 ? parseInt($this.data("counter-time")) : $settings.time;
            var delay = parseInt($this.data("counter-delay")) > 0 ? parseInt($this.data("counter-delay")) : $settings.delay;
            var divisions = time / delay;
            var num = $this.data('counterupTo');
            var nums = [num];
            var isComma = /[0-9]+,[0-9]+/.test(num);
            num = num.replace(/,/g, '');
            var isInt = /^[0-9]+$/.test(num);
            var isFloat = /^[0-9]+\.[0-9]+$/.test(num);
            var decimalPlaces = isFloat ? (num.split('.')[1] || []).length : 0;

            // Generate list of incremental numbers to display
            for (var i = divisions; i >= 1; i--) {

                // Preserve as int if input was int
                var newNum = parseInt(Math.round(num / divisions * i));

                // Preserve float if input was float
                if (isFloat) {
                    newNum = parseFloat(num / divisions * i).toFixed(decimalPlaces);
                }

                // Preserve commas if input had commas
                if (isComma) {
                    while (/(\d+)(\d{3})/.test(newNum.toString())) {
                        newNum = newNum.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
                    }
                }

                nums.unshift(newNum);
            }

            $this.data('counterup-nums', nums);
            $this.text('0');

            // Updates the number until we're done
            var f = function() {
                $this.text($this.data('counterup-nums').shift());
                if ($this.data('counterup-nums').length) {
                    setTimeout($this.data('counterup-func'),delay);
                } else {
                    delete $this.data('counterup-nums');
                    $this.data('counterup-nums', null);
                    $this.data('counterup-func', null);
                }
            };
            $this.data('counterup-func', f);

            // Start the count up
            setTimeout($this.data('counterup-func'),delay);
        };

        // Perform counts when the element gets into view
        $this.waypoint(counterUpper, { offset: '100%', triggerOnce: true });
    });

  };

})( jQuery );

/*
 Inspired by the lightbox plugin adapted to jquery by Leandro Vieira Pinho (http://leandrovieira.com)
 
 @author  : Nicolas Turlais : nicolas-at-insipi.de
 @version : V0.3.1 - June 2012
 @license : Licensed under CCAttribution-ShareAlike
 @website : http://chocolat.insipi.de
 
*/
(function($) {
	images = [];
	var calls = 0;
	$.fn.Chocolat = function(settings) {
		settings = $.extend({
			container             : $('body'),
			displayAsALink        : false,
			linkImages            : true,
			linksContainer        : 'Choco_links_container',				
			overlayOpacity        : 0.9,
			overlayColor          : '#fff',
			fadeInOverlayduration : 500,
			fadeInImageduration   : 500,
			fadeOutImageduration  : 500,
			vache                 : true,					
			separator1            : ' | ',						
			separator2            : '/',						
			leftImg               : 'images/left.gif',	
			rightImg              : 'images/right.gif',	
			closeImg              : 'images/close.gif',		
			loadingImg            : 'images/loading.gif',		
			currentImage          : 0,						
			setIndex              : 0,
			setTitle              : '',
			lastImage             : 0
		},settings);
		
		calls++;
		settings.setIndex = calls;
		images[settings.setIndex] = [];
		
		//images:
		this.each(function(index){
			if(index == 0 && settings.linkImages && settings.setTitle == ''){
				settings.setTitle = isSet($(this).attr('rel'), ' ');
			}
			$(this).each(function() {
				images[settings.setIndex]['displayAsALink'] = settings.displayAsALink;
				images[settings.setIndex][index]            = [];
				images[settings.setIndex][index]['adress']  = isSet($(this).attr('href'), ' ');
				images[settings.setIndex][index]['caption'] = isSet($(this).attr('title'), ' ');
				if(!settings.displayAsALink){
					$(this).unbind('click').bind('click', {
						id  : settings.setIndex,
						nom : settings.setTitle,
						i   : index
					}, _initialise);
				}
			})
		});
		
		//setIndex:
		for(var i = 0; i < images[settings.setIndex].length; i++)
		{
			if(images[settings.setIndex]['displayAsALink']){
				if($('#'+settings.linksContainer).size() == 0){
					this.filter(":first").before('<ul id="'+settings.linksContainer+'"></ul>');
				}
				$('#'+settings.linksContainer).append('<li><a href="#" id="Choco_numsetIndex_'+settings.setIndex+'" class="Choco_link">'+settings.setTitle+'</a></li>');
				e = this.parent();
				$(this).remove();
				if($.trim(e.html()) == ""){//If parent empty : remove it
					e.remove();
				}
				return $('#Choco_numsetIndex_'+settings.setIndex).unbind('click').bind('click', {id: settings.setIndex, nom : settings.setTitle, i : settings.currentImage}, _initialise);
			}
		}
		
		function _initialise(event) {
			
			settings.currentImage = event.data.i;
			settings.setIndex     = event.data.id;
			settings.setTitle     = event.data.nom;
			settings.lastImage    = images[settings.setIndex].length - 1;
			showChocolat();
			return false;
		}
		function _interface(){
			//html
			clear();
			settings.container.append('<div id="Choco_overlay"></div><div id="Choco_content"><div id="Choco_close"></div><div id="Choco_loading"></div><div id="Choco_container_photo"><img id="Choco_bigImage" src="" /></div><div id="Choco_container_description"><span id="Choco_container_title"></span><span id="Choco_container_via"></span></div><div id="Choco_left_arrow" class="Choco_arrows"></div><div id="Choco_right_arrow" class="Choco_arrows"></div></div>');	
			$('#Choco_left_arrow').css('background-image', 'url('+settings.leftImg+')');  
			$('#Choco_right_arrow').css('background-image', 'url('+settings.rightImg+')');  
			$('#Choco_close').css('background-image', 'url('+settings.closeImg+')'); 
			$('#Choco_loading').css('background-image', 'url('+settings.loadingImg+')'); 
			if(settings.container.get(0).nodeName.toLowerCase() !== 'body'){
				settings.container.css({'position':'relative','overflow':'hidden','line-height':'normal'});//yes, yes
				$('#Choco_content').css('position','relative');
				$('#Choco_overlay').css('position', 'absolute');
			}
			//events
			$(document).unbind('keydown').bind('keydown', function(e){
				switch(e.keyCode){
					case 37:
						changePageChocolat(-1);
						break;
					case 39:
						changePageChocolat(1);
						break;
					case 27:
						close();
						break;
				};
			});
			if(settings.vache){
				$('#Choco_overlay').click(function(){
					close();
					return false;
				});
			}
			$('#Choco_left_arrow').unbind().bind('click', function(){
				changePageChocolat(-1);
				return false;
			});
			$('#Choco_right_arrow').unbind().bind('click', function(){
				changePageChocolat(1);
				return false;
			});
			$('#Choco_close').unbind().bind('click', function(){
				close();
				return false;
			});
			$(window).resize(function() {
				load(settings.currentImage,true);
			});
	
		}
		function showChocolat(){	
			_interface();
			load(settings.currentImage, false);
			$('#Choco_overlay')
				.css({
					'background-color' : settings.overlayColor,
					'opacity'          : settings.overlayOpacity
				})
				.fadeIn(settings.fadeInOverlayduration);
			$('#Choco_content').fadeIn(settings.fadeInImageduration,function(){});
			
		}
		function load(image,resize){
			settings.currentImage = image;
			$('#Choco_loading').fadeIn(settings.fadeInImageduration);
			var imgPreloader = new Image();
			imgPreloader.onload = function(){
				$('#Choco_bigImage').attr('src',images[settings.setIndex][settings.currentImage]['adress']);
				var ajustees = iWantThePerfectImageSize(imgPreloader.height,imgPreloader.width);
				ChoColat(ajustees['hauteur'],ajustees['largeur'],resize);
				$('#Choco_loading').stop().fadeOut(settings.fadeOutImageduration);
			};
			imgPreloader.src = images[settings.setIndex][settings.currentImage]['adress'];
			preload();
			upadteDescription();
		}
		function changePageChocolat(signe){
			if(!settings.linkImages || (settings.currentImage == 0 && signe == -1) || (settings.currentImage == settings.lastImage && signe == 1))
			{
				return false;
			}
			else{
				//$('#Choco_container_description').fadeTo(settings.fadeOutImageduration,0); making a weird bug with firefox 17
				$('#Choco_container_description').css('visibility','hidden');
				$('#Choco_bigImage').fadeTo(settings.fadeOutImageduration, 0, function(){
					load(settings.currentImage + parseInt(signe), false);
				});
			}
		}
		function ChoColat(hauteur_image,largeur_image,resize){

			if(resize){
				$('#Choco_container_photo, #Choco_content, #Choco_bigImage').stop(true,false).css({'overflow':'visible'});
				$('#Choco_bigImage').animate({
					'height' : hauteur_image+'px',
					'width'  : largeur_image+'px'
				},settings.fadeInImageduration);
			}
			$('#Choco_container_photo').animate({
					'height' : hauteur_image,
					'width'  : largeur_image
			},settings.fadeInImageduration);
			$('#Choco_content').animate({
				'height'     : hauteur_image,
				'width'      : largeur_image,
				'marginLeft' : -largeur_image/2,
				'marginTop'  : -(hauteur_image)/2
			},settings.fadeInImageduration, 'swing', function(){
				$('#Choco_bigImage').fadeTo(settings.fadeInImageduration, 1).height(hauteur_image).width(largeur_image);
				if(!resize)
				{
					arrowsManaging();
					//$('#Choco_container_description').fadeTo(settings.fadeInImageduration,1); making a weird bug with firefox 17
					$('#Choco_container_description').css('visibility','visible');
					$('#Choco_close').fadeIn(settings.fadeInImageduration);
				}
			}).
			css('overflow', 'visible');
		}
		function arrowsManaging(){
			if(settings.linkImages){
				var what = ['Choco_right_arrow','Choco_left_arrow'];
				for(var i=0; i < what.length; i++){
					hide = false;
					if(what[i] == 'Choco_right_arrow' && settings.currentImage == settings.lastImage){
						hide = true;
						$('#'+what[i]).fadeOut(300);
					}
					else if(what[i] == 'Choco_left_arrow' && settings.currentImage == 0){
						hide = true;
						$('#'+what[i]).fadeOut(300);
					}
					if(!hide){
						$('#'+what[i]).fadeIn(settings.fadeOutImageduration);
					}
				}
			}
		}
		function preload(){
			if(settings.currentImage !== settings.lastImage){
				i = new Image;
				z = settings.currentImage + 1;
				i.src = images[settings.setIndex][z]['adress'];
			}
		}
		function upadteDescription(){
			var current = settings.currentImage + 1;
			var last = settings.lastImage + 1;
			$('#Choco_container_title').html(images[settings.setIndex][settings.currentImage]['caption']);
			$('#Choco_container_via').html(settings.setTitle+settings.separator1+current +settings.separator2+last);
		}
		function isSet(variable,defaultValue){
			// return variable === undefined ? defaultValue : variable; ?
			if (variable === undefined) {
				return defaultValue;
			}
			else{
				return variable;
			}
		}
		function iWantThePerfectImageSize(himg,limg){
			//28% = 14% + 14% margin
			var lblock             = limg + (limg*28/100);
			var heightDescAndClose = $('#Choco_container_description').height()+$('#Choco_close').height();
			var hblock             = himg + heightDescAndClose;
			var k                  = limg/himg;
			var kk                 = himg/limg;
			if(settings.container.get(0).nodeName.toLowerCase() == 'body'){
				windowHeight = $(window).height();
				windowWidth  = $(window).width();
			}
			else{
				windowHeight = settings.container.height();
				windowWidth  = settings.container.width();
			}
			notFitting = true;
				while (notFitting){
				var lblock = limg + (limg*28/100);
				var hblock = himg + heightDescAndClose;
					if(lblock > windowWidth){
						limg = windowWidth*100/128;
						
						himg = kk * limg;
					}else if(hblock > windowHeight){
						himg = (windowHeight - heightDescAndClose);
						limg = k * himg;
					}else{
						notFitting = false;
					};
				};
			return {
				largeur:limg,
				hauteur:himg
			};

		}
		function clear(){
			$('#Choco_overlay').remove();
			$('#Choco_content').remove();
		}
		function close(){
			$('#Choco_overlay').fadeOut(900, function(){$('#Choco_overlay').remove()});
			$('#Choco_content').fadeOut(500, function(){$('#Choco_content').remove()});
			settings.currentImage = 0;
		}
	
};
})(jQuery);

!function(){function e(){z.keyboardSupport&&f("keydown",a)}function t(){if(!A&&document.body){A=!0;var t=document.body,o=document.documentElement,n=window.innerHeight,r=t.scrollHeight;if(B=document.compatMode.indexOf("CSS")>=0?o:t,D=t,e(),top!=self)X=!0;else if(r>n&&(t.offsetHeight<=n||o.offsetHeight<=n)){var a=document.createElement("div");a.style.cssText="position:absolute; z-index:-10000; top:0; left:0; right:0; height:"+B.scrollHeight+"px",document.body.appendChild(a);var i;T=function(){i||(i=setTimeout(function(){L||(a.style.height="0",a.style.height=B.scrollHeight+"px",i=null)},500))},setTimeout(T,10),f("resize",T);var l={attributes:!0,childList:!0,characterData:!1};if(M=new V(T),M.observe(t,l),B.offsetHeight<=n){var c=document.createElement("div");c.style.clear="both",t.appendChild(c)}}z.fixedBackground||L||(t.style.backgroundAttachment="scroll",o.style.backgroundAttachment="scroll")}}function o(){M&&M.disconnect(),h(I,r),h("mousedown",i),h("keydown",a),h("resize",T),h("load",t)}function n(e,t,o){if(p(t,o),1!=z.accelerationMax){var n=Date.now(),r=n-R;if(r<z.accelerationDelta){var a=(1+50/r)/2;a>1&&(a=Math.min(a,z.accelerationMax),t*=a,o*=a)}R=Date.now()}if(q.push({x:t,y:o,lastX:0>t?.99:-.99,lastY:0>o?.99:-.99,start:Date.now()}),!P){var i=e===document.body,l=function(){for(var n=Date.now(),r=0,a=0,c=0;c<q.length;c++){var u=q[c],d=n-u.start,s=d>=z.animationTime,m=s?1:d/z.animationTime;z.pulseAlgorithm&&(m=x(m));var f=u.x*m-u.lastX>>0,h=u.y*m-u.lastY>>0;r+=f,a+=h,u.lastX+=f,u.lastY+=h,s&&(q.splice(c,1),c--)}i?window.scrollBy(r,a):(r&&(e.scrollLeft+=r),a&&(e.scrollTop+=a)),t||o||(q=[]),q.length?_(l,e,1e3/z.frameRate+1):P=!1};_(l,e,0),P=!0}}function r(e){A||t();var o=e.target,r=u(o);if(!r||e.defaultPrevented||e.ctrlKey)return!0;if(w(D,"embed")||w(o,"embed")&&/\.pdf/i.test(o.src)||w(D,"object"))return!0;var a=-e.wheelDeltaX||e.deltaX||0,i=-e.wheelDeltaY||e.deltaY||0;return K&&(e.wheelDeltaX&&b(e.wheelDeltaX,120)&&(a=-120*(e.wheelDeltaX/Math.abs(e.wheelDeltaX))),e.wheelDeltaY&&b(e.wheelDeltaY,120)&&(i=-120*(e.wheelDeltaY/Math.abs(e.wheelDeltaY)))),a||i||(i=-e.wheelDelta||0),1===e.deltaMode&&(a*=40,i*=40),!z.touchpadSupport&&v(i)?!0:(Math.abs(a)>1.2&&(a*=z.stepSize/120),Math.abs(i)>1.2&&(i*=z.stepSize/120),n(r,a,i),e.preventDefault(),void l())}function a(e){var t=e.target,o=e.ctrlKey||e.altKey||e.metaKey||e.shiftKey&&e.keyCode!==N.spacebar;document.contains(D)||(D=document.activeElement);var r=/^(textarea|select|embed|object)$/i,a=/^(button|submit|radio|checkbox|file|color|image)$/i;if(r.test(t.nodeName)||w(t,"input")&&!a.test(t.type)||w(D,"video")||y(e)||t.isContentEditable||e.defaultPrevented||o)return!0;if((w(t,"button")||w(t,"input")&&a.test(t.type))&&e.keyCode===N.spacebar)return!0;var i,c=0,d=0,s=u(D),m=s.clientHeight;switch(s==document.body&&(m=window.innerHeight),e.keyCode){case N.up:d=-z.arrowScroll;break;case N.down:d=z.arrowScroll;break;case N.spacebar:i=e.shiftKey?1:-1,d=-i*m*.9;break;case N.pageup:d=.9*-m;break;case N.pagedown:d=.9*m;break;case N.home:d=-s.scrollTop;break;case N.end:var f=s.scrollHeight-s.scrollTop-m;d=f>0?f+10:0;break;case N.left:c=-z.arrowScroll;break;case N.right:c=z.arrowScroll;break;default:return!0}n(s,c,d),e.preventDefault(),l()}function i(e){D=e.target}function l(){clearTimeout(E),E=setInterval(function(){F={}},1e3)}function c(e,t){for(var o=e.length;o--;)F[j(e[o])]=t;return t}function u(e){var t=[],o=document.body,n=B.scrollHeight;do{var r=F[j(e)];if(r)return c(t,r);if(t.push(e),n===e.scrollHeight){var a=s(B)&&s(o),i=a||m(B);if(X&&d(B)||!X&&i)return c(t,$())}else if(d(e)&&m(e))return c(t,e)}while(e=e.parentElement)}function d(e){return e.clientHeight+10<e.scrollHeight}function s(e){var t=getComputedStyle(e,"").getPropertyValue("overflow-y");return"hidden"!==t}function m(e){var t=getComputedStyle(e,"").getPropertyValue("overflow-y");return"scroll"===t||"auto"===t}function f(e,t){window.addEventListener(e,t,!1)}function h(e,t){window.removeEventListener(e,t,!1)}function w(e,t){return(e.nodeName||"").toLowerCase()===t.toLowerCase()}function p(e,t){e=e>0?1:-1,t=t>0?1:-1,(Y.x!==e||Y.y!==t)&&(Y.x=e,Y.y=t,q=[],R=0)}function v(e){return e?(O.length||(O=[e,e,e]),e=Math.abs(e),O.push(e),O.shift(),clearTimeout(H),H=setTimeout(function(){window.localStorage&&(localStorage.SS_deltaBuffer=O.join(","))},1e3),!g(120)&&!g(100)):void 0}function b(e,t){return Math.floor(e/t)==e/t}function g(e){return b(O[0],e)&&b(O[1],e)&&b(O[2],e)}function y(e){var t=e.target,o=!1;if(-1!=document.URL.indexOf("www.youtube.com/watch"))do if(o=t.classList&&t.classList.contains("html5-video-controls"))break;while(t=t.parentNode);return o}function S(e){var t,o,n;return e*=z.pulseScale,1>e?t=e-(1-Math.exp(-e)):(o=Math.exp(-1),e-=1,n=1-Math.exp(-e),t=o+n*(1-o)),t*z.pulseNormalize}function x(e){return e>=1?1:0>=e?0:(1==z.pulseNormalize&&(z.pulseNormalize/=S(1)),S(e))}function k(e){for(var t in e)C.hasOwnProperty(t)&&(z[t]=e[t])}var D,M,T,E,H,C={frameRate:150,animationTime:400,stepSize:100,pulseAlgorithm:!0,pulseScale:4,pulseNormalize:1,accelerationDelta:50,accelerationMax:3,keyboardSupport:!0,arrowScroll:50,touchpadSupport:!1,fixedBackground:!0,excluded:""},z=C,L=!1,X=!1,Y={x:0,y:0},A=!1,B=document.documentElement,O=[],K=/^Mac/.test(navigator.platform),N={left:37,up:38,right:39,down:40,spacebar:32,pageup:33,pagedown:34,end:35,home:36},q=[],P=!1,R=Date.now(),j=function(){var e=0;return function(t){return t.uniqueID||(t.uniqueID=e++)}}(),F={};window.localStorage&&localStorage.SS_deltaBuffer&&(O=localStorage.SS_deltaBuffer.split(","));var I,_=function(){return window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||function(e,t,o){window.setTimeout(e,o||1e3/60)}}(),V=window.MutationObserver||window.WebKitMutationObserver||window.MozMutationObserver,$=function(){var e;return function(){if(!e){var t=document.createElement("div");t.style.cssText="height:10000px;width:1px;",document.body.appendChild(t);{var o=document.body.scrollTop;document.documentElement.scrollTop}window.scrollBy(0,1),e=document.body.scrollTop!=o?document.body:document.documentElement,window.scrollBy(0,-1),document.body.removeChild(t)}return e}}(),U=window.navigator.userAgent,W=/Edge/.test(U),G=/chrome/i.test(U)&&!W,J=/safari/i.test(U)&&!W,Q=/mobile/i.test(U),Z=(G||J)&&!Q;"onwheel"in document.createElement("div")?I="wheel":"onmousewheel"in document.createElement("div")&&(I="mousewheel"),I&&Z&&(f(I,r),f("mousedown",i),f("load",t)),k.destroy=o,window.SmoothScrollOptions&&k(window.SmoothScrollOptions),"object"==typeof exports?module.exports=k:window.SmoothScroll=k}();
/*!
Waypoints - 4.0.0
Copyright © 2011-2015 Caleb Troughton
Licensed under the MIT license.
https://github.com/imakewebthings/waypoints/blob/master/licenses.txt
*/
!function(){"use strict";function t(o){if(!o)throw new Error("No options passed to Waypoint constructor");if(!o.element)throw new Error("No element option passed to Waypoint constructor");if(!o.handler)throw new Error("No handler option passed to Waypoint constructor");this.key="waypoint-"+e,this.options=t.Adapter.extend({},t.defaults,o),this.element=this.options.element,this.adapter=new t.Adapter(this.element),this.callback=o.handler,this.axis=this.options.horizontal?"horizontal":"vertical",this.enabled=this.options.enabled,this.triggerPoint=null,this.group=t.Group.findOrCreate({name:this.options.group,axis:this.axis}),this.context=t.Context.findOrCreateByElement(this.options.context),t.offsetAliases[this.options.offset]&&(this.options.offset=t.offsetAliases[this.options.offset]),this.group.add(this),this.context.add(this),i[this.key]=this,e+=1}var e=0,i={};t.prototype.queueTrigger=function(t){this.group.queueTrigger(this,t)},t.prototype.trigger=function(t){this.enabled&&this.callback&&this.callback.apply(this,t)},t.prototype.destroy=function(){this.context.remove(this),this.group.remove(this),delete i[this.key]},t.prototype.disable=function(){return this.enabled=!1,this},t.prototype.enable=function(){return this.context.refresh(),this.enabled=!0,this},t.prototype.next=function(){return this.group.next(this)},t.prototype.previous=function(){return this.group.previous(this)},t.invokeAll=function(t){var e=[];for(var o in i)e.push(i[o]);for(var n=0,r=e.length;r>n;n++)e[n][t]()},t.destroyAll=function(){t.invokeAll("destroy")},t.disableAll=function(){t.invokeAll("disable")},t.enableAll=function(){t.invokeAll("enable")},t.refreshAll=function(){t.Context.refreshAll()},t.viewportHeight=function(){return window.innerHeight||document.documentElement.clientHeight},t.viewportWidth=function(){return document.documentElement.clientWidth},t.adapters=[],t.defaults={context:window,continuous:!0,enabled:!0,group:"default",horizontal:!1,offset:0},t.offsetAliases={"bottom-in-view":function(){return this.context.innerHeight()-this.adapter.outerHeight()},"right-in-view":function(){return this.context.innerWidth()-this.adapter.outerWidth()}},window.Waypoint=t}(),function(){"use strict";function t(t){window.setTimeout(t,1e3/60)}function e(t){this.element=t,this.Adapter=n.Adapter,this.adapter=new this.Adapter(t),this.key="waypoint-context-"+i,this.didScroll=!1,this.didResize=!1,this.oldScroll={x:this.adapter.scrollLeft(),y:this.adapter.scrollTop()},this.waypoints={vertical:{},horizontal:{}},t.waypointContextKey=this.key,o[t.waypointContextKey]=this,i+=1,this.createThrottledScrollHandler(),this.createThrottledResizeHandler()}var i=0,o={},n=window.Waypoint,r=window.onload;e.prototype.add=function(t){var e=t.options.horizontal?"horizontal":"vertical";this.waypoints[e][t.key]=t,this.refresh()},e.prototype.checkEmpty=function(){var t=this.Adapter.isEmptyObject(this.waypoints.horizontal),e=this.Adapter.isEmptyObject(this.waypoints.vertical);t&&e&&(this.adapter.off(".waypoints"),delete o[this.key])},e.prototype.createThrottledResizeHandler=function(){function t(){e.handleResize(),e.didResize=!1}var e=this;this.adapter.on("resize.waypoints",function(){e.didResize||(e.didResize=!0,n.requestAnimationFrame(t))})},e.prototype.createThrottledScrollHandler=function(){function t(){e.handleScroll(),e.didScroll=!1}var e=this;this.adapter.on("scroll.waypoints",function(){(!e.didScroll||n.isTouch)&&(e.didScroll=!0,n.requestAnimationFrame(t))})},e.prototype.handleResize=function(){n.Context.refreshAll()},e.prototype.handleScroll=function(){var t={},e={horizontal:{newScroll:this.adapter.scrollLeft(),oldScroll:this.oldScroll.x,forward:"right",backward:"left"},vertical:{newScroll:this.adapter.scrollTop(),oldScroll:this.oldScroll.y,forward:"down",backward:"up"}};for(var i in e){var o=e[i],n=o.newScroll>o.oldScroll,r=n?o.forward:o.backward;for(var s in this.waypoints[i]){var a=this.waypoints[i][s],l=o.oldScroll<a.triggerPoint,h=o.newScroll>=a.triggerPoint,p=l&&h,u=!l&&!h;(p||u)&&(a.queueTrigger(r),t[a.group.id]=a.group)}}for(var c in t)t[c].flushTriggers();this.oldScroll={x:e.horizontal.newScroll,y:e.vertical.newScroll}},e.prototype.innerHeight=function(){return this.element==this.element.window?n.viewportHeight():this.adapter.innerHeight()},e.prototype.remove=function(t){delete this.waypoints[t.axis][t.key],this.checkEmpty()},e.prototype.innerWidth=function(){return this.element==this.element.window?n.viewportWidth():this.adapter.innerWidth()},e.prototype.destroy=function(){var t=[];for(var e in this.waypoints)for(var i in this.waypoints[e])t.push(this.waypoints[e][i]);for(var o=0,n=t.length;n>o;o++)t[o].destroy()},e.prototype.refresh=function(){var t,e=this.element==this.element.window,i=e?void 0:this.adapter.offset(),o={};this.handleScroll(),t={horizontal:{contextOffset:e?0:i.left,contextScroll:e?0:this.oldScroll.x,contextDimension:this.innerWidth(),oldScroll:this.oldScroll.x,forward:"right",backward:"left",offsetProp:"left"},vertical:{contextOffset:e?0:i.top,contextScroll:e?0:this.oldScroll.y,contextDimension:this.innerHeight(),oldScroll:this.oldScroll.y,forward:"down",backward:"up",offsetProp:"top"}};for(var r in t){var s=t[r];for(var a in this.waypoints[r]){var l,h,p,u,c,d=this.waypoints[r][a],f=d.options.offset,w=d.triggerPoint,y=0,g=null==w;d.element!==d.element.window&&(y=d.adapter.offset()[s.offsetProp]),"function"==typeof f?f=f.apply(d):"string"==typeof f&&(f=parseFloat(f),d.options.offset.indexOf("%")>-1&&(f=Math.ceil(s.contextDimension*f/100))),l=s.contextScroll-s.contextOffset,d.triggerPoint=y+l-f,h=w<s.oldScroll,p=d.triggerPoint>=s.oldScroll,u=h&&p,c=!h&&!p,!g&&u?(d.queueTrigger(s.backward),o[d.group.id]=d.group):!g&&c?(d.queueTrigger(s.forward),o[d.group.id]=d.group):g&&s.oldScroll>=d.triggerPoint&&(d.queueTrigger(s.forward),o[d.group.id]=d.group)}}return n.requestAnimationFrame(function(){for(var t in o)o[t].flushTriggers()}),this},e.findOrCreateByElement=function(t){return e.findByElement(t)||new e(t)},e.refreshAll=function(){for(var t in o)o[t].refresh()},e.findByElement=function(t){return o[t.waypointContextKey]},window.onload=function(){r&&r(),e.refreshAll()},n.requestAnimationFrame=function(e){var i=window.requestAnimationFrame||window.mozRequestAnimationFrame||window.webkitRequestAnimationFrame||t;i.call(window,e)},n.Context=e}(),function(){"use strict";function t(t,e){return t.triggerPoint-e.triggerPoint}function e(t,e){return e.triggerPoint-t.triggerPoint}function i(t){this.name=t.name,this.axis=t.axis,this.id=this.name+"-"+this.axis,this.waypoints=[],this.clearTriggerQueues(),o[this.axis][this.name]=this}var o={vertical:{},horizontal:{}},n=window.Waypoint;i.prototype.add=function(t){this.waypoints.push(t)},i.prototype.clearTriggerQueues=function(){this.triggerQueues={up:[],down:[],left:[],right:[]}},i.prototype.flushTriggers=function(){for(var i in this.triggerQueues){var o=this.triggerQueues[i],n="up"===i||"left"===i;o.sort(n?e:t);for(var r=0,s=o.length;s>r;r+=1){var a=o[r];(a.options.continuous||r===o.length-1)&&a.trigger([i])}}this.clearTriggerQueues()},i.prototype.next=function(e){this.waypoints.sort(t);var i=n.Adapter.inArray(e,this.waypoints),o=i===this.waypoints.length-1;return o?null:this.waypoints[i+1]},i.prototype.previous=function(e){this.waypoints.sort(t);var i=n.Adapter.inArray(e,this.waypoints);return i?this.waypoints[i-1]:null},i.prototype.queueTrigger=function(t,e){this.triggerQueues[e].push(t)},i.prototype.remove=function(t){var e=n.Adapter.inArray(t,this.waypoints);e>-1&&this.waypoints.splice(e,1)},i.prototype.first=function(){return this.waypoints[0]},i.prototype.last=function(){return this.waypoints[this.waypoints.length-1]},i.findOrCreate=function(t){return o[t.axis][t.name]||new i(t)},n.Group=i}(),function(){"use strict";function t(t){this.$element=e(t)}var e=window.jQuery,i=window.Waypoint;e.each(["innerHeight","innerWidth","off","offset","on","outerHeight","outerWidth","scrollLeft","scrollTop"],function(e,i){t.prototype[i]=function(){var t=Array.prototype.slice.call(arguments);return this.$element[i].apply(this.$element,t)}}),e.each(["extend","inArray","isEmptyObject"],function(i,o){t[o]=e[o]}),i.adapters.push({name:"jquery",Adapter:t}),i.Adapter=t}(),function(){"use strict";function t(t){return function(){var i=[],o=arguments[0];return t.isFunction(arguments[0])&&(o=t.extend({},arguments[1]),o.handler=arguments[0]),this.each(function(){var n=t.extend({},o,{element:this});"string"==typeof n.context&&(n.context=t(this).closest(n.context)[0]),i.push(new e(n))}),i}}var e=window.Waypoint;window.jQuery&&(window.jQuery.fn.waypoint=t(window.jQuery)),window.Zepto&&(window.Zepto.fn.waypoint=t(window.Zepto))}();