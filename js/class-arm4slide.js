/**
 * @package arm4slide
 * File: class-arm4slide.js
 * Description: js mooTools class. The slider core.
 * Requires: mooTools-core, mooTools-more,
**/
var arm4slide = new Class({
	Implements: [Options, Events],

	options:
	{
		wrapper_id: null,
		wrapper_width: null,
		wrapper_height: null,
		wrapper_texture: null,
		wrapper_background: null,
		wrapper_shadow_x: null,
		wrapper_shadow_y: null,
		wrapper_shadow_blur: null,
		wrapper_shadow_color: null,
		wrapper_border_radius: null,
		wrapper_padding: null,
		slides_class: null,
		slides_shadow_x: null,
		slides_shadow_y: null,
		slides_shadow_blur: null,
		slides_shadow_color: null,
		slides_border_radius: null,
		captions_class: null,
		captions_title_pos: null,
		captions_title_fontcolor: null,
		captions_title_size: null,
		captions_title_font_shadow_x: null,
		captions_title_font_shadow_y: null,
		captions_title_font_shadow_blur: null,
		captions_title_font_shadow_color: null,
		captions_title_texture: null,
		captions_title_background: null,
		captions_title_shadow_x: null,
		captions_title_shadow_y: null,
		captions_title_shadow_blur: null,
		captions_title_shadow_color: null,
		captions_title_padding: null,
		captions_content_pos: null,
		captions_content_width: null,
		captions_content_fontcolor: null,
		captions_content_size: null,
		captions_content_font_shadow_x: null,
		captions_content_font_shadow_y: null,
		captions_content_font_shadow_blur: null,
		captions_content_font_shadow_color: null,
		captions_content_texture: null,
		captions_content_background: null,
		captions_content_shadow_x: null,
		captions_content_shadow_y: null,
		captions_content_shadow_blur: null,
		captions_content_shadow_color: null,
		captions_content_padding: null,
		control_class: null,
		control_type: null,
		control_pos: null,
		/*control_width: null,
		control_height: null,*/
		control_texture: null,
		control_background: null,
		control_color: null,
		control_shadow_x: null,
		control_shadow_y: null,
		control_shadow_blur: null,
		control_shadow_color: null,
		control_border_radius: null,
		//control_margin: null,
		control_padding: null,
		anim_first: null,
		anim_movetime: null,
		anim_movetype_out: null,
		anim_movetype_in: null,
		anim_title_move: null,
		anim_title_type: null,
		anim_content_move: null,
		anim_content_type: null,
		anim_pause_in: null,
		anim_pause_out: null,
		readmore: null,
		link: null,
		auto: null,
		//transition: '3d',
		//transitionFallback: 'slide'
		narrow: false,
	},
	
	currentSlide: null,
	nextSlide: null,
	prevSlide: null,
	numSlides: null,
	actualSlideWidth: function() {},
	slideWidth: null,
	actualSlideHeight: function() {},
	slideHeight: null,
	setSlides: function() {},
	firstTime: true,
	setControl: function() {},
	direction: null,
	actualCaption: null,
	actualTitle: null,
	actualContent: null,
	actualCaptionDimensions: null,
	actualTitleDimensions: null,
	actualContentDimensions: null,
	distance: null,
	readmore_array: null,
	from_auto: null,
	
	initialize: function(element,options)
	{
			// _this stores now the self object
			_this = this;
			// options setting
			this.setOptions(options);
				// string to number conversions
				this.toNumbers();
				// movement distance based on transition options
				this.setDistance();				
			// get the element as itself
			this.element = element;
			// stores the slides
			this.slides = $(this.options.wrapper_id + '-inner').getChildren();
			// slider styles
			this.sliderStyles();
			// slider slides setter
			this.setNewSlides();
			// slider controls setter
			this.setControl();
			// extra options
				// defines a 'Read more' link on the opposite side of title
				if( this.options.readmore == '1' ) {
					readmore_array = $$('.readmore');
					this.setReadmore();
				}
				// defines all the caption like a link object
				if( this.options.link == '1' ) {
					this.wholeLink();
				}
				// defines the automovement
				if ( this.options.auto !== '' ) {
					this.autoMove();
				}
	},
	setDistance: function()
	{
		switch( this.options.transition ) {
			default:
				return this.distance = this.options.wrapper_width + 50;
		}
	},
	sliderStyles: function()
	{
		control_height = $('slides-control-wrapper').getSize();
		control_height = control_height.y/2;
		total_height = Number.from(this.options.wrapper_height) + control_height + Number.from(this.options.wrapper_padding) + Number.from(this.options.wrapper_padding);

		if( this.options.wrapper_width == 'auto' ) {
			$(this.element).setStyles({
				'width': 'auto'
			});
		} else {
			$(this.element).setStyles({
				'width': this.options.wrapper_width + 'px'
			});
		}
		if( this.options.wrapper_height == 'auto' ) {
			$(this.element).setStyles({
				'height': 'auto'
			});
		} else {
			$(this.element).setStyles({
				'height': total_height + 'px'
			});
		}
		
		$(this.element).setStyles({
			'position':'relative',
			'overflow': 'hidden',
			'margin': '0 auto',
			'padding': this.options.wrapper_padding + 'px',
			'background-image': 'url("' + this.options.wrapper_texture + '")',
			'background-color': this.options.wrapper_background,
			'box-shadow': this.options.wrapper_shadow_x + 'px ' + this.options.wrapper_shadow_y + 'px ' + this.options.wrapper_shadow_blur + 'px ' + this.options.wrapper_shadow_color,
			'border-radius': this.options.wrapper_border_radius + 'px'
		});

		if( this.options.wrapper_height == 'auto' ) {
			$(this.options.wrapper_id + '-inner').setStyles({
				'height': '100%'
			});
		} else {
			$(this.options.wrapper_id + '-inner').setStyles({
				'height': this.options.wrapper_height  + 'px'
			});
		}
		
		$(this.options.wrapper_id + '-inner').setStyles({
			'position': 'relative',
			'width': '100%',
			'float': 'left'
		});
	},
	slidesStyles: function()
	{
		// Defines slides styles
		this.slides.each(function(item,index) {
			$$(item).setStyles({
				'position': 'absolute',
				'top': '0',
				'height': '100%',
				'width': '100%',
				'overflow': 'hidden',
				'box-shadow': _this.options.slides_shadow_x + 'px ' + _this.options.slides_shadow_y + 'px ' + _this.options.slides_shadow_blur + 'px ' + _this.options.slides_shadow_color,
				'border-radius': _this.options.slides_border_radius + 'px'
			});
		});

		// Defines slides attachment image styles
		$$('.' + this.options.slides_class + '_attach').setStyles({
			'overflow': 'hidden',
			'position': 'absolute',
			'top': '0',
			'z-index': '1',
			'width': '100%',
			'height': '100%'
		});
		// caption image
			$$('.' + this.options.slides_class + ' img').setStyles({
				'width': '100%',
				'height': '100%'
			});

		// Defines slides captions styles
			// caption container
			$$('.' + this.options.captions_class).setStyles({
				'position': 'absolute',
				'width': '100%',
				'height': '100%',
				'z-index': '2',
				'overflow': 'hidden',
			});
			
			// caption title
			$$('.' + this.options.captions_class + '-title').setStyles({
				'position': 'relative',
				'background-image': 'url(' + this.options.captions_title_texture + ')',
				'background-color': this.options.captions_title_background,
				'box-shadow': this.options.captions_title_shadow_x + 'px ' + this.options.captions_title_shadow_y + 'px ' + this.options.captions_title_shadow_blur + 'px ' + this.options.captions_title_shadow_color,
				'padding': this.options.captions_title_padding + 'px',
				'font-size': _this.options.captions_title_size + 'px!important',
			});			
				// caption title link
					$$('.' + this.options.captions_class + '-title a').setStyles({
						'text-shadow': 'none'
					});
				$$('.' + this.options.captions_class + '-title a').setStyles({
					'font-size': _this.options.captions_title_size + 'px!important',
					'color': _this.options.captions_title_fontcolor,
					'text-shadow': _this.options.captions_title_font_shadow_x + 'px ' + _this.options.captions_title_font_shadow_y + 'px ' + _this.options.captions_title_font_shadow_blur + 'px ' + _this.options.captions_title_font_shadow_color,
				});

			// caption content
			$$('.' + this.options.captions_class + '-content').setStyles({
				'clear': 'both',
				'font-size': this.options.captions_content_size + 'px',
				'color': this.options.captions_content_fontcolor,
				'position': 'relative',
				'background-image': 'url(' + this.options.captions_content_texture + ')',
				'background-color': this.options.captions_content_background,
				'box-shadow': this.options.captions_content_shadow_x + 'px ' + this.options.captions_content_shadow_y + 'px ' + this.options.captions_content_shadow_blur + 'px ' + this.options.captions_content_shadow_color,
				'width': this.options.captions_content_width + '%',
				'bottom': '0',
				'line-height': this.options.captions_content_size + 'px',
				'padding': this.options.captions_content_padding + 'px',
				'text-shadow': this.options.captions_content_font_shadow_x + 'px ' + this.options.captions_content_font_shadow_y + 'px ' + this.options.captions_content_font_shadow_blur + 'px ' + this.options.captions_content_font_shadow_color,
			});
			
			this.setCaptionReStyle();
	},
	setCaptionReStyle: function()
	{
		switch( this.options.captions_title_pos ) {
				case 'left':
					$$('.' + this.options.captions_class + '-title').setStyles({
						'left': '-' + this.options.wrapper_width + 'px',
						'float': 'left'
					});
				break;
				case 'right':
					$$('.' + this.options.captions_class + '-title').setStyles({
						'left': this.options.wrapper_width + 'px',
						'float': 'right'
					});
				break;
		}
		switch( this.options.captions_content_pos ) {
			case 'left':
				$$('.' + this.options.captions_class + '-content').setStyles({
					'left': '-' + _this.options.wrapper_width + 'px',
					'float': 'left'
				});
			break;
			case 'right':
				$$('.' + this.options.captions_class + '-content').setStyles({
					'left': _this.options.wrapper_width + 'px',
					'float': 'right'
				});
			break;
		}
	},
	setNewSlides: function()
	{
		this.slidesStyles();
			
		// Stores the slides in slides[] and provides an id for each one
		this.slides.each(function(item,index) {
			item.set('id', this.options.slides_class + '-' + index);
		}, this);
			
		// Get the lenght of slides[]
		this.numSlides = this.slides.length;
		
		// Set the first slide as current slide 
		this.currentSlide = this.slides[0];
		
		// Get slide width/height for support resizing
		this.actualSlideWidth = function() {
			slideWidth = this.currentSlide.getStyle('width');
			return slideWidth;
		};
		this.actualSlideHeight = function() {
			slideHeight = this.currentSlide.getStyle('height');
			return slideHeight;
		};
		
		// Define the slides position depending on each slide order
		this.setSlides();
		
		// End of the initial setup
		this.firstTime = false;

		return this;
	},
	setSlides: function()
	{
		this.setCaptionReStyle();
		
		this.actualSlideWidth();
		this.actualSlideHeight();
		
		this.nextSlide = this.currentSlide.getNext();
		this.prevSlide = this.currentSlide.getPrevious();

		if( this.firstTime ) {
			this.slides.filter(':not(#'+this.options.slides_class+'-0)').each(function(el) {
				el.setStyle('left',_this.distance);
			});
			// Show first slide caption	
			showFirstCaption = function() {
				_this.caption('in','left','0');
			};
			window.setTimeout('showFirstCaption()',this.options.anim_first);
		} else {
			this.currentSlide.getAllNext().each(function(el) {
				el.setStyle('left',_this.distance);
			});
			this.currentSlide.getAllPrevious().each(function(el) {
				el.setStyle('left','-' + _this.distance);
			});
		}

		return this;
	},
	setControlStyles: function()
	{
		// Controls wrapper
		$(this.options.control_class + '-wrapper').setStyles({
			//'width': this.options.control_width + 'px',
			'width': 'auto',
			//'height': this.options.control_height + 'px',
			'height': 'auto',
			'background-image': 'url(' + this.options.control_texture + ')',
			'background-color': this.options.control_background,
			'box-shadow': this.options.control_shadow_x + 'px ' + this.options.control_shadow_y + 'px ' + this.options.control_shadow_blur + 'px ' + this.options.control_shadow_color,
			'border-radius': this.options.control_border_radius + 'px',
			'padding': this.options.control_padding + 'px',
		});
			// margin definer
			switch( this.options.control_pos ) {
				case 'topleft':
				case 'bottomleft':
					$(this.options.control_class + '-wrapper').setStyles({
						'float': 'left',
					});
					break;
				case 'topright':
				case 'bottomright':
					$(this.options.control_class + '-wrapper').setStyles({
						'float': 'right',
					});
					break;
			}

		// Control links
		switch( this.options.control_type ) {
			case 'titles':
				$$('.' + this.options.control_class).setStyles({
					'float': 'left',
					'margin': '5px',
					
				});
				$$('.' + this.options.control_class + ' a').setStyle(
					'border-bottom', '0px solid #000'
				);
				break;
			case 'thumbs':
				$$('.' + this.options.control_class).setStyles({
					'float': 'left',
					'margin': '5px',
					'box-shadow': '0px 0px 0px #000',
				});
			break;
			case 'squares':
				$$('.' + this.options.control_class).setStyles({
					'width': '22px',
					'height': '22px',
					'float': 'left',
					'margin': '5px'
				});
				$$('.' + this.options.control_class + '-' + this.options.control_type).setStyles({
					'background': 'url(' + vars.pluginURL + '/images/control_squares.png) no-repeat',
					'background-position': '0px 0px',
					'display': 'block',
					'width': '21px',
					'height': '21px'
				});
			break;
			case 'circles':
				$$('.' + this.options.control_class).setStyles({
					'width': '22px',
					'height': '22px',
					'float': 'left',
					'margin': '5px'
				});
				$$('.' + this.options.control_class + '-' + this.options.control_type).setStyles({
					'background': 'url(' + vars.pluginURL + '/images/control_circles.png) no-repeat',
					'background-position': '0px 0px',
					'display': 'block',
					'width': '21px',
					'height': '21px'
				});
			break;
		}
		
	},
	setControl: function()
	{
		// Sets controls styles
		this.setControlStyles();

		// stores control links
		this.controls = $$('.' + this.options.control_class);
		$controls = this.controls;
		// define to null the pointer
		$controls.to = null;

			// animation set
		this.controls.set('tween', {
				transition: 'bounce:out'
			// id set
			}).each(function(item,index) {
				item.set('id',index);
			// events set
			}).addEvents({
				// on hover:in
				mouseenter: function() {
					if( this.hasClass('active') ) { return; }
					else {					
						// sets animation time
						this.set( 'tween', { duration:40 } );
						// sets the proper animation based on control type
						switch( _this.options.control_type ) {
							case 'titles':
								this.set( 'tween', { duration:1 } );
								this.getChildren().tween( 'border-bottom','2px solid ' + _this.options.control_color );
							case 'thumbs':
								this.tween( 'box-shadow','0px 0px 5px ' + _this.options.control_color );
							break;
							case 'squares':
							case 'circles':
								this.getElement('a').setStyle( 'background-position','-20px 0px');
							break;
						}
					}
				},
				// on hover:out
				mouseleave: function() {
					if( this.hasClass('active') ) { return; }
					else {
						// sets animation time
						this.set( 'tween', { duration: 150 } );					
						// sets the proper animation based on control type
						switch( _this.options.control_type ) {
							case 'titles':
								this.getChildren().tween( 'border-bottom','0px solid ' + _this.options.control_color );
							case 'thumbs':
								this.tween( 'box-shadow','0px 0px 0px #000' );
							break;
							case 'squares':
							case 'circles':
								this.getElement('a').setStyle( 'background-position','0px 0px');
							break;
						}
					}
				},
				click: function(event) {
					
					console.debug(event);
					//if( !from_auto ) {
						// prevents default linking behavior
						if (event && event.stop) event.stop(); 
					//}

					_this.setInactive();

					_this.setActive(this);
					
					// defines the 'where-to-go' variable
					$controls.to = this.getProperty('id');

					// calls transition
					_this.transition.slide('#'+ _this.options.slides_class + '-' + $controls.to);
				}
			});
			
		// defines first item style on slider load
		switch( this.options.control_type ) {
			case 'titles':
				$('0').setStyle( 'border-bottom','2px solid ' + this.options.control_color ).addClass('active');
			case 'thumbs':
				$('0').setStyle( 'box-shadow','0px 0px 5px ' + this.options.control_color ).addClass('active');
			break;
			case 'squares':
			case 'circles':
				$('0').getElement('a').setStyle('background-position','-19px 0px');
				$('0').addClass('active');
			break;
		}

		return this;
	},
	setActive: function(clicked_element)
	{
		switch( _this.options.control_type ) {
			case 'titles':
				clicked_element.getChildren().setStyle( 'border-bottom','2px solid ' + _this.options.control_color );
				clicked_element.addClass('active');
			case 'thumbs':
				clicked_element.setStyle( 'box-shadow','0px 0px 5px ' + _this.options.control_color ).addClass('active');
			break;
			case 'squares':
			case 'circles':
				clicked_element.getElement('a').setStyle('background-position','-20px 0px');
				clicked_element.addClass('active');
			break;
		}
	},
	setInactive: function()
	{
		$controls.each(function(el) {
			el.removeClass('active');
			
			switch( _this.options.control_type ) {
				case 'titles':
					el.getChildren().setStyle( 'border-bottom','0px solid ' + _this.options.control_color );
				case 'thumbs':
					el.setStyle( 'box-shadow','0px 0px 0px ' + _this.options.control_color );
				break;
				case 'squares':
				case 'circles':
					el.getElement('a').setStyle('background-position','0px 0px');
				break;
			}
			
		});
	},
	transition:
	{
		// Horizontal slide transition
		slide: function(sectionTo)
		{			
			// Search for the objective slide
			_this.slides.each(function(item,index) {
				if ( item.match([id=sectionTo]) ) {
					indexTo = index;
				}
			});
			// Get the actual slide order
			indexFrom = _this.slides.indexOf(_this.currentSlide);
			
			// Animation
			// -> direction = right
			if( indexFrom < indexTo ) {
				_this.direction = 'right';
				_this.nextSlide = _this.slides[indexTo];
					
			_this.move(_this.currentSlide,'left','-'+_this.distance,
						_this.nextSlide,'left','0');
			// <- direction = left
			} else if( indexFrom > indexTo ) {
				_this.direction = 'left';
				_this.prevSlide = _this.slides[indexTo];

			_this.move(_this.currentSlide,'left',_this.distance,
						_this.prevSlide,'left','0');
			}
		}
	},
	setCaption: function()
	{
		//if( !this.options.narrow ) {
			this.actualCaption = this.currentSlide.getElement('.' + this.options.captions_class);
			this.actualTitle = this.actualCaption.getElement('.' + this.options.captions_class + '-title');
			this.actualContent = this.actualCaption.getElement('.' + this.options.captions_class + '-content');

			if( this.actualContent.getStyle('display') === 'none' ) {
				this.actualContent.show();
			}

			// Dimensions getter
			this.actualTitleDimensions = this.actualTitle.getDimensions();
			this.actualContentDimensions = this.actualContent.getDimensions();
			this.actualCaptionDimensions = this.actualCaption.getDimensions();
			// Top margin setter
			this.actualContentTopMargin = this.actualCaptionDimensions.height - this.actualTitleDimensions.height - this.actualContentDimensions.height + 1;
			this.actualContent.setStyle('top',this.actualContentTopMargin);

			// Checks for narrow screen
			envwidth = $(window).getSize();
			envwidth = envwidth.x;
			if( envwidth <= 480 ) {
				this.options.narrow = true;
				this.setCaption();
			}
	/*	} else {			
			this.actualCaption = this.currentSlide.getElement('.page_content');
			this.actualTitle = this.actualCaption.getElement('.page_content_title');
			// Clones the caption and get it out		
			wrapper = $(this.options.wrapper_id).getParent('#wrapper');
			slide_id = this.actualCaption.getParent().get('id');
			
			if( $('clone' + '-' + slide_id) ) {
				if( $('clone' + '-' + slide_id).getStyle('display') === 'none' ) {
					$('clone' + '-' + slide_id).show();
				} else {
					$('clone' + '-' + slide_id).hide();
				}
			} else { 				
				actualContent_clone = this.actualCaption.getElement('.page_content_excerpt').clone(true,true).set('id','clone' + '-' + slide_id);
				this.actualCaption.getElement('.page_content_excerpt').hide();
				// Put the clone after the slider area
				wrapper.adopt(actualContent_clone);
				$('clone' + '-' + slide_id).setStyles({
					'top': '0',
					'width': '95%',
					'margin': '0',
					'padding': '10px',
					'left': '0'
				});
			}

			envwidth = $(window).getSize();
			envwidth = envwidth.x;
			if( envwidth > 480 ) {
				this.options.narrow = false;
				this.setCaption();
			}
		}*/
	},
	setCaptionFx: function()
	{
		this.setCaption();
		// Fx
		titleFx = new Fx.Tween( this.actualTitle, {
			duration: _this.options.anim_title_move,
			transition: _this.options_anim_title_type,
			link: 'chain',
			onStart: function() {
				
			}
		});
		contentFx = new Fx.Tween( this.actualContent, {
			duration: _this.options.anim_content_move,
			transition: _this.options_anim_content_type,
			link: 'chain',
			onStart: function() {
			
			}
		});
		return this;	
	},
	caption: function(dir,prop,val)
	{
		this.setCaptionFx();
		switch(dir) {
			case 'in':
				titleFx.start(prop,val)
					.wait(_this.options.anim_pause_in)
					.chain(function(){
						contentFx.start(prop,val);
						if( _this.options.readmore == '1' ) {
							readmore_fx.start(1);
						}
					});
				break;
			case 'out':
				contentFx.start(prop,val)
					.wait(_this.options.anim_pause_out)
					.chain(function(){
						if( _this.options.readmore == '1' ) {
							readmore_fx.start(0);
						}
						titleFx.start(prop,val);
					});
		}
		//return this;
	},
	move: function(el,prop,val,nextEl,nextProp,nextVal)
	{
		half_movetime = _this.options.anim_movetime / 2;
		
		moveNextEl = new Fx.Tween( $(nextEl), {
			duration: half_movetime,
			transition: _this.options.anim_movetype_in,
			link: 'chain',
			property: nextProp,
			onComplete: function() {
				switch(_this.direction) {
					case 'right':
						_this.currentSlide = _this.nextSlide;
						break;
					case 'left':
						_this.currentSlide = _this.prevSlide;
						break;
				}
				_this.setSlides();
				_this.caption('in','left','0');
				_this.setReadmore();
			}
		});
		moveEl = new Fx.Tween( $(el), {
			duration: _this.options.anim_movetime,
			transition: _this.options.anim_movetype_out,
			link: 'chain',
			property: prop,
			onStart: function() {
				// Hides the actual slide captions
				_this.caption('out','left',_this.actualContentDimensions.width);
			},
			onComplete: function() {
				_this.setCaptionReStyle();
				moveNextEl.start(nextVal);
			}
		});
		moveEl.start(val);
	},
	resizer: function()
	{
		this.setSlides();
		this.setCaption();
	},
	toNumbers: function()
	{
		this.options.wrapper_width = Number.from(this.options.wrapper_width);
		this.options.wrapper_height = Number.from(this.options.wrapper_height);
		this.options.control_height = Number.from(this.options.control_height);
		this.options.control_margin = Number.from(this.options.control_margin);
		this.options.control_padding = Number.from(this.options.control_padding);
		this.options.anim_movetime = Number.from(this.options.anim_movetime);
		this.options.anim_title_move = Number.from(this.options.anim_title_move);
		this.options.anim_content_move = Number.from(this.options.anim_content_move);
		this.options.anim_pause_in = Number.from(this.options.anim_pause_in);
		this.options.anim_pause_out = Number.from(this.options.anim_pause_out);
	},
	setReadmore: function()
	{		
		$$('.readmore').setStyles({
			'background-image': 'url(' + this.options.captions_title_texture + ')',
			'background-color': this.options.captions_title_background,
			'box-shadow': this.options.captions_title_shadow_x + 'px ' + this.options.captions_title_shadow_y + 'px ' + this.options.captions_title_shadow_blur + 'px ' + this.options.captions_title_shadow_color,
			'padding': this.options.captions_content_padding + 'px',
			'font-size': this.options.captions_content_size + 'px',
			'text-shadow': 'none',
			'color': this.options.captions_content_fontcolor,
			'opacity': 0
		});

		id = this.getCurrentSlideID();
		
		this.readMoreFX(id);
	},
	readMoreFX: function( index )
	{
		readmore_fx = new Fx.Tween( readmore_array[index], {
			duration: '1000',
			transition: 'back:in',
			link: 'chain',
			property: 'opacity'
		});
	},
	wholeLink: function()
	{		
		this.slides.each(function(el,index) {

			base_link = el.getElement('.' + _this.options.captions_class + '-title a');
			base = el.getElement('.' + _this.options.captions_class);
			
			link_href = base_link.getProperty('href');
			link_id = 'whole-link-' + index;
			
			wholeLink = new Element('a', {
				id: link_id,
				href: link_href
			});
			wholeLink.setStyles({
				'position': 'absolute',
				'width': '100%',
				'height': '100%',
				'z-index': '999'
			});
			wholeLink.wraps( base );
			
		});		
	},
	autoMove: function()
	{		
		setInterval( function() {
			//from_auto = true;
			id = _this.getCurrentSlideID();
			next = Number.from(id) + 1;
			if( next >= $controls.length ) {
				next = 0;
			}
			$controls[next].fireEvent('click',[{stop: function(){}}]);
		},
		this.options.auto );
	},
	getCurrentSlideID: function()
	{
		id = this.currentSlide.getProperty('id');
		id = id.slice(-1);

		return id;
	}
});
