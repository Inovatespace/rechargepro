(function($) {
	$.fn.extend({
		//plugin name - seuntech first plugin
		//close: function(param) {$('#lightBox, #lightBoxcontainer').remove();},
		//jQuery.fn.close(2000);
		//jQuery.fn.loadlink("../shoping/loadwant.php?id="+id+"","boxpost"+id);
		//jQuery.fn.refresh("../index.php?p=9&m=5&cat=com&country=Nigeria");
		tunnel: function(options) {
			//Settings list and the default values
			var defaults = {
				seuntechadding: 60,
				defaultPadding: 10,
				evenColor: '#ccc',
				oddColor: '#eee'
			};
			var options = $.extend(defaults, options);
			return this.each(function() {
				return $(this).on('click', function(e){
					e.preventDefault();
					if ($("#lightBox").length > 0) {
						$('#lightBox').remove();
					}
					var url = $(this).attr('name');
					//if not url or image allert error
					//if empty herif allert error
					//if empty use default width
					$('body').append('<div id="lightBoxcontainer"></div>');
					$('#lightBoxcontainer').fadeTo("slow", 0.5);
					$('body').append('<div id="lightBox"></div>');
					$('#lightBox').append('<div id="lightboxloading"><img src="java/display/images/loading.gif" /></div>');
					if (url.match(/width=([0-9]+)/)[1] == "") {
						alert("with and height must be specified");
						return false;
					}
					var pagewidth = url.match(/width=([0-9]+)/)[1];
					//var pageheight = url.match(/height=([0-9]+)/)[1];
					$('#lightBox').append('<div id="sitecontent" style="position:relative; overflow:hidden;"><div style="overflow:hidden;"><div class="close" style="float:right; padding-right:5px;"><img src="java/display/images/xlose.png" /></div></div><div id="innercontent"><img src="java/display/images/loading.gif" /><div></div>');
					$("#innercontent").load(url);
					$('#lightboxloading').delay(10).remove();
					//Get the screen height and width
					var hideHeight = $(document).height();
					var hideWidth = $(window).width();
					var siteWidth = $(document).width();
					var siteHeight = hideHeight - $('#lightBox').height();
					//Set heigth and width to hide to fill up the whole screen
					$('#lightBoxcontainer').css({
						'width': siteWidth,
						'height': siteHeight
					});
					jQuery.fn.loadlink = function(urllink, divid) {
						return $("#" + divid).load(urllink);
					}
					jQuery.fn.close = function(number) {
						if (number == "") {
							number = 0
						};
						return setTimeout(function() {
							$('#lightBox, #lightBoxcontainer').remove();
						}, number);
					}
					jQuery.fn.refresh = function(url) {
						return window.parent.location.href = url;
					}
					jQuery.fn.center = function() {
						this.css('position', 'fixed');
						//var modalTop = ($(window).height() - this.height()) / 7;
                        var modalTop = ($(window).height() - 150) / 7;
						var modalLeft = ($(window).width() - this.width()) / 2 + $(window).scrollLeft();
                        //if( $.browser.msie && parseInt($.browser.version) <= 6 ) top = top + $(window).scrollTop();
						this.animate({
							'left': modalLeft + 'px',
							'top': modalTop + 'px'
						});
						return this;
					}
					pagewidth = parseInt(pagewidth);
					$("#lightBox").css("width", pagewidth + "px");
					$("#lightBox").center();
					$('.close, #lightBoxcontainer').bind('click', $.proxy(function(ev) {
						$('#lightBox, #lightBoxcontainer').remove();
						ev.preventDefault();
					}, this));

					function resizewindows() {
						if ($('#lightBoxcontainer').length) {
							$('#lightBoxcontainer').css({
								'width': $("#wrapper").width()
							});
							$('#lightBox').css('left', ($(window).width() - $('#lightBox').width()) / 2 + $(window).scrollLeft() + 'px');
						}
					}

					function resizescroll() {
						if ($('#lightBoxcontainer').length) {
							$('#lightBoxcontainer').css({
								'width': $("#wrapper").width()
							});
						}
					}
					$(window).bind('resize', $.proxy(function() {
						if ($('#lightBoxcontainer').length) {
							resizewindows();
						}
					}, this));
					$(window).bind('scroll', $.proxy(function() {
						if ($('#lightBoxcontainer').length) {
							resizescroll();
						}
					}, this));
					$(document).bind('keydown', function(e) {
						if ($('#lightBoxcontainer').length) {
							if (e.keyCode == 27) { // esc
								$('#lightBox, #lightBoxcontainer').remove();
							}
						}
					});
				});
			});
		},
		callme: function(divcall, width) {
			if ($("#lightBox").length > 0) {
				$('#lightBox').remove();
			}
			var url = $(divcall).html();
			if (width == "") {
				alert("with must be specified");
				return false;
			}
			//if not url or image allert error
			//if empty herif allert error
			//if empty use default width
			$('body').append('<div id="lightBoxcontainer"></div>');
			$('#lightBoxcontainer').fadeTo("slow", 0.5);
			$('body').append('<div id="lightBox"></div>');
			$('#lightBox').append('<div id="lightboxloading"><img src="java/display/images/loading.gif" /></div>');
			var pagewidth = width;
			//var pageheight = url.match(/height=([0-9]+)/)[1];
			$('#lightBox').append('<div id="sitecontent"><div class="close" style="float:right; padding-right:5px;"><img src="java/display/images/xlose.png" /></div><div style="clear:both;"></div><div id="innercontent"><div></div>');
			$("#innercontent").html(url)
			$('#lightboxloading').delay(10).remove();
			//Get the screen height and width
			var hideHeight = $(document).height();
			var hideWidth = $(window).width();
			var siteWidth = $(document).width();
			var siteHeight = hideHeight - $('#lightBox').height();
			//Set heigth and width to hide to fill up the whole screen
			$('#lightBoxcontainer').css({
				'width': siteWidth,
				'height': siteHeight
			});
		
				jQuery.fn.close = function(number) {
						if (number == "") {
							number = 0
						};
						return setTimeout(function() {
							$('#lightBox, #lightBoxcontainer').remove();
						}, number);
					}
			
			jQuery.fn.center = function() {
				this.css('position', 'fixed');
				var modalTop = ($(window).height() - this.height()) / 5;
				var modalLeft = ($(window).width() - this.width()) / 2 + $(window).scrollLeft();
				this.animate({
					'left': modalLeft + 'px',
					'top': modalTop + 'px'
				});
				return this;
			}
			pagewidth = parseInt(pagewidth);
			$("#lightBox").css("width", pagewidth + "px");
			$("#lightBox").center();
			$('.close, #lightBoxcontainer').bind('click', $.proxy(function(ev) {
				$('#lightBox, #lightBoxcontainer').remove();
				ev.preventDefault();
			}, this));

			function resizewindows() {
				if ($('#lightBoxcontainer').length) {
					$('#lightBoxcontainer').css({
						'width': $("#wrapper").width()
					});
					$('#lightBox').css('left', ($(window).width() - $('#lightBox').width()) / 2 + $(window).scrollLeft() + 'px');
				}
			}

			function resizescroll() {
				if ($('#lightBoxcontainer').length) {
					$('#lightBoxcontainer').css({
						'width': $("#wrapper").width()
					});
				}
			}
			$(window).bind('resize', $.proxy(function() {
				if ($('#lightBoxcontainer').length) {
					resizewindows();
				}
			}, this));
			$(window).bind('scroll', $.proxy(function() {
				if ($('#lightBoxcontainer').length) {
					resizescroll();
				}
			}, this));
			//alert($(optionsf).html());
		},
		calllink: function(divlink) {
			if ($("#lightBox").length > 0) {
				$('#lightBox').remove();
			}
          
        
            
            
            	if (divlink.match(/width=([0-9]+)/)[1] == "") {
						alert("with and height must be specified");
						return false;
					}
            var width = divlink.match(/width=([0-9]+)/)[1];
			//if not url or image allert error
			//if empty herif allert error
			//if empty use default width
			$('body').append('<div id="lightBoxcontainer"></div>');
			$('#lightBoxcontainer').fadeTo("slow", 0.5);
			$('body').append('<div id="lightBox"></div>');
			$('#lightBox').append('<div id="lightboxloading"><img src="java/display/images/loading.gif" /></div>');
			var pagewidth = width;
			//var pageheight = url.match(/height=([0-9]+)/)[1];
			$('#lightBox').append('<div id="sitecontent"><div class="close" style="float:right; padding-right:5px;"><img src="java/display/images/xlose.png" /></div><div style="clear:both;"></div><div id="innercontent"><div></div>');
			$("#innercontent").load(divlink);
			$('#lightboxloading').delay(10).remove();
			//Get the screen height and width
			var hideHeight = $(document).height();
			var hideWidth = $(window).width();
			var siteWidth = $(document).width();
			var siteHeight = hideHeight - $('#lightBox').height();
			//Set heigth and width to hide to fill up the whole screen
			$('#lightBoxcontainer').css({
				'width': siteWidth,
				'height': siteHeight
			});
		
				jQuery.fn.close = function(number) {
						if (number == "") {
							number = 0
						};
						return setTimeout(function() {
							$('#lightBox, #lightBoxcontainer').remove();
						}, number);
					}
			
			jQuery.fn.center = function() {
				this.css('position', 'fixed');
				var modalTop = ($(window).height() - this.height()) / 5;
				var modalLeft = ($(window).width() - this.width()) / 2 + $(window).scrollLeft();
				this.animate({
					'left': modalLeft + 'px',
					'top': modalTop + 'px'
				});
				return this;
			}
			pagewidth = parseInt(pagewidth);
			$("#lightBox").css("width", pagewidth + "px");
			$("#lightBox").center();
			$('.close, #lightBoxcontainer').bind('click', $.proxy(function(ev) {
				$('#lightBox, #lightBoxcontainer').remove();
				ev.preventDefault();
			}, this));

			function resizewindows() {
				if ($('#lightBoxcontainer').length) {
					$('#lightBoxcontainer').css({
						'width': $("#wrapper").width()
					});
					$('#lightBox').css('left', ($(window).width() - $('#lightBox').width()) / 2 + $(window).scrollLeft() + 'px');
				}
			}

			function resizescroll() {
				if ($('#lightBoxcontainer').length) {
					$('#lightBoxcontainer').css({
						'width': $("#wrapper").width()
					});
				}
			}
			$(window).bind('resize', $.proxy(function() {
				if ($('#lightBoxcontainer').length) {
					resizewindows();
				}
			}, this));
			$(window).bind('scroll', $.proxy(function() {
				if ($('#lightBoxcontainer').length) {
					resizescroll();
				}
			}, this));
			//alert($(optionsf).html());
		}
	});
})(jQuery);