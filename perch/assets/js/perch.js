if (typeof(Perch) == 'undefined') {
	Perch	= {};
	Perch.UI	= {};
	Perch.Apps	= {};
}

Perch.UI.Global	= function()
{
	var doresize = false;
	var confirmDialogue = false;
	var keepAlivePoll = 5*60*1000; // 5mins
	
	var init	= function() {
		$('body').addClass('js');
		initAppMenu();
		enhanceCSS();
		initPopups();
		hideMessages();
		initEditForm();
		initDeleteButtons();
		initKeepAlive();
	};
	
	var enhanceCSS = function() {
		$('#content #main-panel form div.error').prev().css('border-bottom', '0');
		$('#content #main-panel form div.edititem').prev().find('div:last').css('border-bottom', '0');
		$('#content form div.field').append('<div class="clear"></div>');
	};
	
	var initPopups = function() {
		$('a.assist, a.draft-preview').click(function(e){
			e.preventDefault();
			window.open($(this).attr('href'));
		});
	};
	
	var hideMessages = function() {
		if ($('p.alert-success')) {
			setTimeout("$('p.alert-success').selfHealingRemove()", 5000);
		};
	};
	
	var initEditForm = function() {
		$(window).bind('load', stickButtons);
		$(window).bind('resize', function(){
			doresize = true;
			setTimeout(function(){
				if (doresize) {
					stickButtons;
					doresize = false;
				}
			}, 1000);
		});
		
		$('form#content-edit').submit(function(){
            $('input:file[value=""]').attr('disabled', true);
        });
	};
	
	var stickButtons = function() {
		if ($.browser.msie && parseInt($.browser.version,10)<7) return;

		var btns = $('#content-edit p.submit');
		if (btns.length && !btns.hasClass('nonstick')) {
			var w = $(window);
		
			var t = btns.position().top;
			var bh = (btns.outerHeight(true));
			var wh = w.height();
			
			var msg = $('p.alert-success');
			
			if (msg) t=t-msg.outerHeight(true);
		
			w.unbind('scroll');
			
			var oldie = false;
			if ($.browser.msie && parseInt($.browser.version,10)<9) {
				oldie = true;
			}
		
			if ($('body').height() > wh) {
				w.scroll(function(){
					var position = w.scrollTop() + wh-bh-10;
					
					if (t > position) { 
						btns.addClass('stuck');
						btns.parents('form').addClass('with-stuck');
						var pos_t = position-t;
						if (-pos_t<50) {
							var transparency = (0.8/100)*((-pos_t/50)*100);
						}else{
							var transparency = 0.8;
						}
						if (oldie) {
							btns.css('background-color', 'rgb(191, 191, 191)');
						}else{
							if (btns.hasClass('error')) {
								btns.css('background-color', 'rgba(179, 64, 64, '+transparency+')');
							}else{
								btns.css('background-color', 'rgba(191, 191, 191, '+transparency+')');
							}
						}
					}else{
						btns.removeClass('stuck');
						btns.parents('form').removeClass('with-stuck');
						if (oldie) {
							btns.css('background-color', 'rgb(255, 255, 255)');	
						}else{
							btns.css('background-color', 'rgba(255, 255, 255, 1)');	
						}					
					}
				});
				w.scroll();
			}
		}
	};
	
	
	var initAppMenu = function() {
		var appmenu = $('#nav ul.appmenu');
		var items = appmenu.find('li');
		
		if (items.length>1) {
			appmenu.addClass('menu');
			var cont = $('#nav li.apps');
			cont.addClass('menucont');
			var selectedText = appmenu.find('li.selected a').text();
			if (selectedText) {
				var select = true;
			}else{ 
				selectedText = Perch.Lang.get('Apps');
				var select = false;
			}
			var trigger = $('<a class="trigger" href="#">'+selectedText+'<span></span></a>');
			appmenu.before(trigger).hide();
			if (select) trigger.parent('li').addClass('selected');
			appmenu.prepend($('<li><a class="trigger" href="#">'+selectedText+'<span></span></a></li>'));
			cont.hover(function(){
				appmenu.show();
			}, function(){
				appmenu.hide();
			});
			trigger.click(function(e){
				e.preventDefault();
				appmenu.show();
			});
			appmenu.find('a.trigger').click(function(e){
				e.preventDefault();
				appmenu.hide();
			});
		}
		$('#nav').show();
	};
	
	var initDeleteButtons = function() {
		$('a.inline-delete').click(function(e){
			e.preventDefault();
			var self = $(this);
			var message = Perch.Lang.get('Delete this item?');
			if (self.attr('data-msg')) message = self.attr('data-msg');
			openConfirmDialogue(e, message, function(){
				// ok
				$.post(self.attr('href'), {'_perch_ajax':1, 'formaction':'delete', 'token':Perch.token}, function(r){
					window.location = r;
				});
		
			}, function(){
				// cancel
				closeConfirmDialogue();
			});
		});
	};
	
	var openConfirmDialogue = function(event, message, ok_function, cancel_function) {
		if (confirmDialogue) closeConfirmDialogue();
		var target = $(event.target);

		confirmDialogue = $('<div id="confirm-dialogue"><p>'+message+'</p><a href="#" class="ok">'+Perch.Lang.get('OK')+'</a><a href="#" class="cancel">'+Perch.Lang.get('Cancel')+'</a><span class="speak"></span></div>');
		$('#main-panel').append(confirmDialogue);
		confirmDialogue.css({
			top: target.offset().top-(confirmDialogue.outerHeight()+10),
			left: target.offset().left-(confirmDialogue.outerWidth()/2)-50
		}).fadeIn();

		confirmDialogue.find('a.ok').click(function(e){
			e.preventDefault();
			ok_function();
			closeConfirmDialogue();
		});
		confirmDialogue.find('a.cancel').click(function(e){
			e.preventDefault();
			cancel_function();
			closeConfirmDialogue();
		});
	};
	
	var closeConfirmDialogue = function() {
		if (confirmDialogue) {
			confirmDialogue.fadeOut(function(){
				confirmDialogue = false;
			});
			
		}
	};

	var initKeepAlive = function() {
		if ($('form').length) {
			setInterval(function(){
				$.get(Perch.path+'/inc/keepalive.php');
			}, keepAlivePoll);
		}
	};
	

	
	return {
		init: init,
		enhanceCSS: enhanceCSS
	};
	
}();

Perch.Apps.Content = function() {
	
	var contentOpenRows = [];
	var contentListCookie = 'cmscl';
	
	var init = function() {
		initContentList();
		initItemReordering();
	};
	
	var initContentList = function() { 

		var contentList = $('#content-list');
		if (contentList.length) {

			if (!Perch.Apps.Content.settings.collapseList) return;
			
			var regioncellwidth = contentList.find('thead th.region').width();
			
			if ($.cookie(contentListCookie)==null) $.cookie(contentListCookie, '');
			
			// fix column widths
			
			var max = 0;
			var j = 0;
			contentList.find('td.type').each(function(i, o){
				if (j>50) return false;
				var width = $(o).textWidth();
				if (width>max) max=width;
				j++;
			});
			contentList.find('thead th.type').css('width', max+10);
			
			var regionth = contentList.find('thead th.region');
			regionth.css('width', regionth.outerWidth());
			
						
			var toplevels = contentList.find('tr:has(.level0)');
			
			// toggle based on cookie
			var stropenrows = $.cookie(contentListCookie);
			var cookie_openrows = [];
			if (stropenrows) cookie_openrows = stropenrows.split(',');
			
			toplevels.each(function(i, o){
				var self = $(o);
				
				if (self.next(':not(tr:has(.level0))').length) {
					self.addClass('closed');
					if (self.find('td.region a').length) {
						self.find('td.region a').wrap('<div class="regions"></div>');
					}else{
						self.find('td.region').append('<div class="regions"></div>');
					}
					
					self.find('td.region a:first').clone().addClass('orig').prependTo(self.find('td.region'));
					self.nextUntil('tr:has(.level0)').hide().find('td.region a').clone().addClass('clone').appendTo(self.find('td.region .regions'));
					var r = self.find('td.region .regions');
					
					if (r.outerWidth()>regioncellwidth) {
						/*
						$(self.find('td.region a:not(:first)').get().reverse()).each(function(i, o){
							o.style.display='none';
							if (r.outerWidth()+17 < regioncellwidth) return false;	
						});
						*/
						self.find('td.region a:not(:first)').hide().each(function(i, o){
							o = $(o);
							o.show();
							if (r.outerWidth()+17 > regioncellwidth) {
								o.hide();
								return false;
							} 
						});
					}
									
					var list = self.find('td.region a:visible:not(:last)');
					if (list.length) list.append(',').after(' ');
					self.find('td.region a:visible:last').after(' &hellip;');
				
					var typecell = self.find('td.type');
					if (!typecell.find('span.new').length) {
						var typetext = typecell.text();
						typecell.empty().append('<span class="orig">'+typetext+'</span>');	
					
						if (self.nextUntil('tr:has(.level0)').find('td.type:has(.new)').length) {
							typecell.append('<span class="clone new">'+Perch.Lang.get('New')+'</span>');
						}else{
							typecell.append('<span class="clone">'+Perch.Lang.get('Mixed')+'</span>');
						}
					}
							
					var tog = $('<a href="#" class="toggle"></a>');
					tog.click(function(e){
						e.preventDefault();
						var self = $(this);
						var parent = self.parents('tr');
					
						if (parent.hasClass('closed')) {
							contentListOpenFromRow(parent);
						}else{
							contentListCloseFromRow(parent);
						}
					});
					tog.append('<span>+</span>');
					self.find('td:first').append(tog);
				
				
					if ($.inArray(self.attr('data-contentid'), cookie_openrows)!=-1) {
						contentListOpenFromRow(self);
					}
				}
				
				// delete buttons
				self.find('a.inline-delete').click(function(e){
					if (self.hasClass('closed')) {
						e.preventDefault();
						contentListOpenFromRow(self);
					}
				});
			});
		}
	};
	
	var contentListOpenFromRow = function(row) {
		row.nextUntil('tr:has(.level0)').show();
		row.toggleClass('closed');
		
		var id = row.attr('data-contentid');
		contentOpenRows.push(id);
		$.cookie(contentListCookie, contentOpenRows);
	};
	
	var contentListCloseFromRow = function(row) {
		row.nextUntil('tr:has(.level0)').hide();
		row.toggleClass('closed');
		
		var i, l;
		var newContentOpenRows = [];
		var id = row.attr('data-contentid');
		for(i=0, l=contentOpenRows.length; i<l; i++) {
			if (contentOpenRows[i]!=id) newContentOpenRows.push(contentOpenRows[i]);
		}
		contentOpenRows = newContentOpenRows;
		$.cookie(contentListCookie, contentOpenRows);
	};
	
	var initItemReordering = function() {
		var itemList = $('#content-reorder');
		if (itemList.length) {
						
			var overlay = $('<div id="edit-overlay"></div>');
			overlay.fadeTo(1, 0).hide();
			$('body').append(overlay);
			
			var buttons = itemList.find('h4 .buttons');
			buttons.hide();
			
			var save = $('<a href="#" class="save compact-button">'+Perch.Lang.get('Save')+'</a>');
			save.click(function(e){
				e.preventDefault();
				var order = [];
				itemList.find('li').each(function(i,o){
					var self = $(o);
					order.push(self.attr('data-idx'));
					self.attr('data-idx', i);
					self.find('a').attr('href', '#item'+(i+1));
				});
				
				var url = Perch.path+'/apps/content/edit/?id='+itemList.attr('data-id');
				$.post(url, {'_perch_ajax':1, 'formaction':'reorder', 'token':Perch.token, 'new_order':order.join(',')}, function(r){
					var html = $(r);
					var main = html.find('#main-panel');
					var old_main = $('#main-panel');
					old_main.fadeOut('slow', function(){
						old_main.replaceWith(main);
						Perch.UI.Global.enhanceCSS();
						main.fadeIn('slow');
						Perch.token = main.attr('data-token');
						overlay.fadeTo('slow', 0, function(){
							overlay.hide();
						});
						buttons.fadeOut();
						$(window).trigger('Perch_Init_Editors');
					});
				});
				
			}).appendTo(buttons);
			
			var undo = $('<a href="#" class="undo compact-button">'+Perch.Lang.get('Undo')+'</a>');
			undo.click(function(e){
				e.preventDefault();
				var items = itemList.find('li').get();
				items.sort(function(a, b){
					var compA = parseInt(a.getAttribute('data-idx'),10);
					var compB = parseInt(b.getAttribute('data-idx'),10);
					return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
				});
				var i, l;
				var ul = itemList.find('ul:first');
				for(i=0, l=items.length; i<l; i++) {
					$(items[i]).appendTo(ul);
				}
				overlay.fadeTo('slow', 0, function(){
					overlay.hide();
				});
				buttons.fadeOut();
			}).appendTo(buttons);

			itemList.find('ul').sortable({
					start: function() {
						overlay.show().fadeTo('slow', 0.7);
					},
					stop: function() {
						buttons.fadeIn();
					}
			}); 
		}

	};
	
	return {
		init: init
	};
}();


Perch.Lang	= function()
{
	var translations = {};
	
	var init = function(t) {
		translations = t;
	};
	
	var get = function(str) {
		if (translations[str]) {
			return translations[str];
		}
		
		return str;
	};
	
	return {
		init: init,
		get: get
	};
}();


jQuery.fn.selfHealingRemove = function(settings, fn) {
	if (jQuery.isFunction(settings)){
		fn = settings;
		settings = {};
	}else{
		settings = jQuery.extend({
			speed: 'slow'
		}, settings);
	};
	
	return this.each(function(){
		var self = jQuery(this); 
		self.animate({
			opacity: 0
		}, settings.speed, function(){
			self.slideUp(settings.speed, function(){
				self.remove();
				if (jQuery.isFunction(fn)) fn();
			});
		});
	});
};

jQuery.fn.textWidth = function(){
	var self = $(this);
	var html_org = self.html();
	var html_calc = '<span>' + html_org + '</span>';
	self.html(html_calc);
	var width = $(this).find('span:first').width();
	self.html(html_org);
	return width;
};

/**
 * jQuery Cookie plugin
 * Copyright (c) 2010 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */
jQuery.cookie=function(D,E,B){if(arguments.length>1&&String(E)!=="[object Object]"){B=jQuery.extend({},B);if(E===null||E===undefined){B.expires=-1;}if(typeof B.expires==="number"){var G=B.expires,C=B.expires=new Date();C.setDate(C.getDate()+G);}E=String(E);return(document.cookie=[encodeURIComponent(D),"=",B.raw?E:encodeURIComponent(E),B.expires?"; expires="+B.expires.toUTCString():"",B.path?"; path="+B.path:"",B.domain?"; domain="+B.domain:"",B.secure?"; secure":""].join(""));}B=E||{};var A,F=B.raw?function(H){return H;}:decodeURIComponent;return(A=new RegExp("(?:^|; )"+encodeURIComponent(D)+"=([^;]*)").exec(document.cookie))?F(A[1]):null;};


jQuery(function($) { 
	Perch.UI.Global.init(); 
	for (var app in Perch.Apps) Perch.Apps[app].init();
});