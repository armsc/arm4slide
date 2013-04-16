/**
 * @package arm4slide
 * File: admin_js.js
 * Description: js code for the admin side of arm4slide plugin.
 * Requires: mooTools-core, mooTools-more, class-arm4slide
**/
jQuery.noConflict();

// jQuery scripts
jQuery(document).ready(function($) {

if( pagenow === 'appearance_page_arm4slide' ) {

// -- Texture uploads -- //
// -- --------------- -- //
	$('#wrapper-texture,#title-texture,#content-texture,#control-texture').hide();
	texture_buttons = $('#wrapper-texture-button,#title-texture-button,#content-texture-button,#control-texture-button');
	$(texture_buttons).after('<div class="texture"></div>');
	
	$('.texture').each(function(i,el) {
		texture = $(el).parent().find('input[type="text"]').val();
		$(el).css('background','url('+texture+')');
	});
	
	$(texture_buttons).click(function() {
		_this = $(this);
        tb_show('Sube tu textura', 'media-upload.php?referer=arsSlider&type=image&TB_iframe=true&post_id=0', false);  
		window.send_to_editor = function(html) {
			var texture_url = $('img',html).attr('src');
			_this.parent().find('input[type="text"]').val(texture_url);
			_this.parent().find('.texture').css('background','url(' + texture_url + ') repeat');
			tb_remove();
		};
        return false;
    }); 

// -- Color pickers -- //
// -- ------------- -- //
	color_buttons = $('#wrapper-background,#title-fontcolor,#title-background,#content-fontcolor,#content-background,#control-background,#control-color');
	$(color_buttons).each(function(i,el) {
		$(el).wpColorPicker();
	});

// -- Slides which -- //
// -- ------------ -- //
	slides_which = function(el) {
		election = $(el).val();
		if( election === 'cats' ) {
			$('.which_input,.which_msg').show();
		} else {
			$('.which_input,.which_msg').hide();
		}
	};
	$('#slides-which').change(function() {
		slides_which($(this));
	});

	slides_which($('#slides-which'));

// -- Slide count -- //
// -- ----------- -- //
	$('#slides-count').attr('size','2').css('text-align','right').after('<div class="left" style="margin:3px 0px 0px 3px;">' + vars.slidesLocalizatedName + '</div>');

// -- Size inputs -- //
// -- ----------- -- //
	size_inputs = $('#wrapper-width,#wrapper-height');
	$(size_inputs).attr('size','2').css('text-align','right').after('<div class="measure left"> px</div>');

	$('#content-width').attr('size','2').css('text-align','right').after('<div class="measure left"> %</div>');
	/*
	$(size_inputs_auto).after('<input class="auto_check" type="checkbox"/> Auto');
	$('.auto_check').before('<div class="measure left"> px</div>')
		.change( function() {
			if( $(this).is(':checkbox:checked') ) {
				$(this).parent().find('input[type="text"]')
					.val('auto').css('background','#EEE')
					.keydown(function(event) {
						event.preventDefault();
					});
			} else {
				$(this).parent().find('input[type="text"]')
					.val('').css('background','none')
					.off('keydown');
			}
		});*/

// -- Shadow inputs -- // // -- Border radius -- //
// -- ------------- -- // // -- ------------- -- //
	radius_inputs = $('#wrapper-border-radius,#slides-border-radius,#control-border-radius');
	$(radius_inputs)
		.attr('size','2')
		.css('text-align','right')
		.after('<div class="measure left"> px</div>')
		.keyup(function() {
			radius_name = $(this).attr('id');
			rad_indexof = radius_name.indexOf('-');
			radius_name = radius_name.slice(0,rad_indexof);
			radius_name = '#' + radius_name + '-ex';
			
			rad = $(this).val();
			radius = rad + 'px';
			
			$(radius_name).css('border-radius',radius);
		});

	shadow_inputs = $('#wrapper-shadow-x,#wrapper-shadow-y,#wrapper-shadow-blur,#slides-shadow-x,#slides-shadow-y,#slides-shadow-blur,#title-shadow-x,#title-shadow-y,#title-shadow-blur,#content-shadow-x,#content-shadow-y,#content-shadow-blur,#control-shadow-x,#control-shadow-y,#control-shadow-blur,#title-font-shadow-x,#title-font-shadow-y,#title-font-shadow-blur,#content-font-shadow-x,#content-font-shadow-y,#content-font-shadow-blur');
	shadow_colors = $('#wrapper-shadow-color,#slides-shadow-color,#title-shadow-color,#content-shadow-color,#control-shadow-color,#title-font-shadow-color,#content-font-shadow-color');

	$(shadow_colors).each(function(i,el) {
		$(el).wpColorPicker({
			change: function(event,ui) {
						create_shadow($(this));
						$('#' + section_name + '-ex').css('box-shadow',shadow);
					},
			clear: function() {
						create_shadow($(this));
						$('#' + section_name + '-ex').css('box-shadow',shadow);
					}
		});
	});
	
	$(shadow_inputs)
		.attr('size','2')
		.css('text-align','right')
		.after('<div class="measure left"> px</div>');
			
	$('#wrapper-shadow-x,#slides-shadow-x,#title-shadow-x,#content-shadow-x,#control-shadow-x')
		.each(function(i,el) {
			name = $(el).attr('id');
			indexof = name.indexOf('-');
			name = name.slice(0,indexof);

			$(el).after('<div id="'+name+'-ex" class="section_ex"></div>');
		});

	create_shadow = function(element) {
		section_name = $(element).attr('id');
		section_indexof = section_name.indexOf('-');
		section_name = section_name.substring(0,indexof);

		switch( section_name ) {
			case 'slides-':
				section_name = 'slides';
			break;
			case 'title-s':
				section_name = 'title';
			break;
			
		}

		values = {
			x: $('#'+section_name+'-shadow-x').val(),
			y: $('#'+section_name+'-shadow-y').val(),
			blur: $('#'+section_name+'-shadow-blur').val(),
			color: $('#'+section_name+'-shadow-color').val()
		};

		shadow = values.x + 'px ' + values.y + 'px ' + values.blur + 'px ' + values.color;
	};
	
	$(shadow_inputs).keyup(function() {
		create_shadow($(this));
		$('#' + section_name + '-ex').css('box-shadow',shadow);
	});

	$(shadow_inputs).each(function(i,el) {
		create_shadow($(el));
		border = $('#' + section_name + '-border-radius').val() + 'px';
		
		$('#' + section_name + '-ex').css({'box-shadow':shadow,'border-radius':border});
	});

// -- Controls inputs -- //
// -- --------------- -- //
	$('#control-type').after('<div class="control_image"></div>')
		.change( function() {
			control_type($('#control-type'));
		});
	control_type = function(el) {
		image = $(el).val();
		switch( image ) {
			case 'circles':
			case 'squares':
				$('.control_image').html('')
					.css({'background':'url(' + vars.pluginDir + '/images/admin/control_' + image + '.png) no-repeat','width':'40px'});
			break;
			case 'titles':
				$('.control_image').html(vars.controlTypeTitles)
				.css({'background':'none', 'width':'auto'});
			break;
			case 'thumbs':
				$('.control_image').html(vars.controlTypeThumbs)
				.css({'background':'none', 'width':'auto'});
			break;
		}
	};
	control_type($('#control-type'));

// -- Animation inputs -- //
// -- ---------------- -- //
	anim_inputs = $('#firsttime,#movetime,#titlemove,#contentmove,#pausein,#pauseout');
	$(anim_inputs).attr('size','2').css('text-align','right').after('<div class="left measure">ms</div>');

// -- Animation options -- //
// -- ----------------- -- //
	anim_options = $('#movetype-out,#movetype-in,#titletype,#contenttype');
	anim_options.after('<div class="anim_ex"></div>');

// -- Other inputs -- //
// -- ------------ -- //
	padding_inputs = $('#wrapper-padding,#title-padding,#content-padding,#control-margin,#control-padding');

	$(padding_inputs)
		.attr('size','2')
		.css('text-align','right')
		.after('<div class="measure left"> px</div>');

	auto_input = $('#auto').attr('size','2').addClass('left').after('<div class="measure left">ms</div>');

// -- Copy code textarea -- //
// -- ------------------ -- //
	// Changes the textarea background and prevents overwritting.
	$('#arm4slide_render_code').find('textarea')
			.css('background','#EEE')
			.keydown(function(event) {
				if( !( event.ctrlKey && event.which == 67 ) ) {
					event.preventDefault();
				}
			});

$('form').show();
	
}
	
});

// MooTools scripts
window.addEvent('domready', function() {

	anim_selects = $$('#movetype-out,#movetype-in,#titletype,#contenttype');

	anim_selects.addEvent('change', function(event) {
		event.stop();
		ball = this.getNext();
		anim = this.get('value');
		fx = new Fx.Tween( ball, {
			duration: 'long',
			transition: anim,
			link: 'cancel',
			property: 'left'
		});
		fx.start(0,300)
			.wait(200)
			.chain(function(){
				fx.start(300,0);
		});
	});
	
});
