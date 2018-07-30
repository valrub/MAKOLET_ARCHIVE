$(function () {

	// Navigate to hash on page-reload
	var url = document.location.toString();
	if (url.match('#')) {
		$('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show');
	} 

	// Change hash for page-reload
	$('.nav-tabs a').on('shown.bs.tab', function (e) {
		window.location.hash = e.target.hash;
	})

	// Initialize the tooltip
	$('[data-toggle="tooltip"]').tooltip();

	// Smooth scroll
	$('nav a[href*="#"]:not([href="#"])').click(function() {
		if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
			var target = $(this.hash);
			target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
			if (target.length) {
				var navHeight = $('body > nav').height();
				var sectionHeaderHeight = $('.section-header').height();
				$('html, body').stop().animate({
					scrollTop: target.offset().top - navHeight + sectionHeaderHeight
				}, 1000);
				return false;
			}
		}
	});

	// Rating stars
	var selectedRating = 0;

	$('.feedback-stars label').mouseover(function() {

		var hoverRating = $(this).attr('data-rating');
		$('.feedback-stars label').each(function() {
			if (hoverRating < $(this).attr('data-rating')) {
				$('i', this).addClass('star-white');
			} else {
				$('i', this).removeClass('star-white');
			}
		});

	}).mouseout(function() {
		$('.feedback-stars input').each(function() {
			if ($(this).is(':checked')) {
				selectedRating = $(this).attr('data-rating');
			}
		});
		
		if (selectedRating) {
			console.log(selectedRating);
			$('.feedback-stars label').each(function() {
				if (selectedRating < $(this).attr('data-rating')) {
					$('i', this).addClass('star-white');
				} else {
					$('i', this).removeClass('star-white');
				}
			});
		} else {
			$('.feedback-stars label').each(function() {
				$('i', this).addClass('star-white');
			});
		}
	});

	// Add/Remove goods
	var goodsAmount = $("#goods tr").length;

	$(".btn.add-line").click(function() {
		if ($('#goods tbody tr').length < 15) {
			$('#goods tbody').append(
				'<tr>' +
					'<td><input type="text" name="goods[]" value=""></td>' +
					/*'<td><input type="number" name="quantities[]" min="1" max="99" value="1"></td>' +*/
				'</tr>'
			);
		}
		toggleAddRemove();
	});

	$(".btn.remove-line").click(function() {
		if ($('#goods tbody tr').length > 1) {
			$('#goods tbody tr:last').remove();
		}
		toggleAddRemove();
	});

	function toggleAddRemove() {
		if ($('#goods tbody tr').length <= 1) {
			$(".btn.remove-line").addClass("disabled");
		} else if ($('#goods tbody tr').length >= 15) {
			$(".btn.add-line").addClass("disabled");
		} else {
			$(".btn.add-line").removeClass("disabled");
			$(".btn.remove-line").removeClass("disabled");
		}
	}

	////////////////// AJAX

	// Get the CSRF token
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	// Accept/Decline proposal
	$('button[data-proposal][data-proposal-accept]').click(function() {
		
		// Get properties
		var accept = $(this).attr('data-proposal-accept');
		var proposal = $(this).attr('data-proposal');

		// Set classes
		// $(this).siblings().andSelf().addClass('disabled');
		// $(this).addClass('btn-loading');

		// Create data object
		var data = {
			accept: accept,
			proposal: proposal
		};

		$.ajax({
			type: 'POST',
			url: '/proposals/accept',
			data: data
		}).done(function() {
			window.location.replace(document.location.pathname);
		}).fail(function() {
			alert('fail');
		});

	});

	// Notifications

	if (!$("#notifier").length && $(".user-name").length) {

		var notificationsInterval = setInterval(function() {
			$.ajax({
				type: 'GET',
				url: '/notifications'
			}).done(function(data) {
				if (data) {
					$("body").append('<a href="' + window.location.origin + '/orders/' + data.order_id + '" id="notifier" data-toggle="tooltip" data-placement="top" data-notification="' + data.id + '" title="' + data.message + '"><i class="fa fa-bell-o" aria-hidden="true"></i></a>');
					$('[data-toggle="tooltip"]').tooltip();
					clearInterval(notificationsInterval);
				}
			}).fail(function() {
				console.log('Didn\'t succeed to get the notifications.');
			});
		}, 30000);

	}

	// ADMIN

	// Toggle Well

	$('.search-button').click(function() {
		$('.search-well').toggle();
		$('#search-field').focus();
	});

	// New lines in the notes

	$('.note p').each(function() {
		$(this).html($(this).html().replace(/(?:\r\n|\r|\n)/g, '<br />'));
	});


	////////////////// TEMP

	// Hide Adv-Header
	$('.adv-close').click(function() {
		$('.adv-header').hide();
		$('body > .container').css({'margin-top': '68px'});
	});

	$('div > a[href="#shops"]').click(function() {
		$('li').each(function() {
			$(this).removeClass('active');
		});
		$('a[href="#shops"]').parent().addClass('active');
	});

	// Time Range Slider

	$(".weekday .slider-range").each(function() {
		$(this).slider({
			range: true,
			min: 0,
			max: 1440,
			step: 10,
			values: [540, 1020],
			slide: function (e, ui) {
				
				var hours1 = Math.floor(ui.values[0] / 60);
				var minutes1 = ui.values[0] - (hours1 * 60);

				if (hours1.toString().length == 1) hours1 = '0' + hours1;
				if (minutes1.toString().length == 1) minutes1 = '0' + minutes1;
				if (minutes1 == 0) minutes1 = '00';

				var time1 = hours1 + ':' + minutes1;

				$(this).parent().parent().find('.input-open').val(time1);
				$(this).parent().parent().find('.slider-open').html(time1);

				var hours2 = Math.floor(ui.values[1] / 60);
				var minutes2 = ui.values[1] - (hours2 * 60);

				if (hours2.toString().length == 1) hours2 = '0' + hours2;
				if (minutes2.toString().length == 1) minutes2 = '0' + minutes2;
				if (minutes2 == 0) minutes2 = '00';
				
				var time2 = hours2 + ':' + minutes2;

				$(this).parent().parent().find('.input-close').val(time2);
				$(this).parent().parent().find('.slider-close').html(time2);

			}
		});
	});

	// Toggle working days

	$(".weekday input[type=checkbox]").change(function() {
		console.log($(this).is(":checked"));
	});

});

function generatePassword() {
	var newPassword = "";
	var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	for (var i = 0; i < 8; i++)
	newPassword += possible.charAt(Math.floor(Math.random() * possible.length));
	$('input[name=password]').val(newPassword);
}