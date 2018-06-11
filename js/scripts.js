$(document).ready(function()
{

	function HTMLAlert(text, style, autoclose = true)
	{
		$('#info .container').append('<div class="alert alert-' + style + ' alert-dismissible fade show '+ (autoclose ? 'alert-autoclose' : '') + '" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span>' + text + '</span></div>');

		window.setTimeout(function()
		{
			
			$(".alert.alert-autoclose").fadeTo(1000, 0).slideUp(1000, function()
			{
       			$(this).remove(); 
			});

			
		}, 5000);

	}

	//HTMLAlert('Test', 'info', false);

	// Walidacja rejestracji i logowania

	$.extend( $.validator.messages, {
		required: "To pole jest wymagane.",
		email: "To nie jest prawidłowy adres email.",
		url: "Podaj prawidłowy URL.",
		date: "Podaj prawidłową datę.",
		number: "Podaj prawidłową liczbę.",
		equalTo: "Hasła nie są takie same.",
		maxlength: $.validator.format( "Podaj maksymalnie {0} znaków." ),
		minlength: $.validator.format( "Podaj przynajmniej {0} znaków." ),
		rangelength: $.validator.format( "Proszę o podanie wartości o długości od {0} do {1} znaków." ),
		range: $.validator.format( "Proszę o podanie wartości z przedziału od {0} do {1}." ),
		max: $.validator.format( "Podaj wartość mniejszą bądź równą {0}." ),
		min: $.validator.format( "Podaj wartość większą bądź równą {0}." ),
		pattern: $.validator.format( "Pole zawiera niedozwolone znaki." ),
		accept: $.validator.format( "Plik jest wymagany" )
	} );





	$("#registerForm").validate(
	{
		submitHandler: function(form)
		{
			$.ajax(
			{
				url: 'register.php',
				type: 'post',
				data: $(form).serialize(),
				success: function(response)
				{
					if(response == 'true')
					{
						HTMLAlert('Konto utworzone. Możesz się zalogować.', 'ok');
						$('#registerForm')[0].reset();
					}
					else
					{
						HTMLAlert('Wystąpił błąd. Skontaktuj się z administratorem', 'error');
					}
				}
            });
		},
		rules: 
		{
			password1:
			{
				required: 'Proszę wpisać hasło',
				minlength: 6
			},
			password2:
			{
				required: true,
				equalTo: "#registerPassword1"
			},
			username:
			{
				required: true,
				remote:
				{
					url: "register.php",
					type: "post",
					data:
					{
						checkName: function()
						{
							return $('#registerUsername').val();
						}
					}
				}
			},
			email:
			{
				required: true,
				remote:
				{
					url: "register.php",
					type: "post",
					data:
					{
						checkEmail: function()
						{
							return $('#registerEmail').val();
						}
					}
				}
			}
			
		}
	});



	$("#loginForm").validate(
	{
		submitHandler: function(form)
		{
			$.ajax(
			{
				url: 'login.php',
				type: 'post',
				data: $(form).serialize(),
				success: function(data)
				{
					var response = JSON.parse(data);

					if(response['code'] == '1')
					{
						location.reload();
					}
					else
					{
						HTMLAlert(response['message'], 'error');
					}

						
				}
            });
		}
	});



	/***************************************************************/

	$('button.rate-post').click(function()
	{
		var postID = $(this).attr("data-id");
		$.post('ajax/rate.php', 
		{
			id: postID, 
			mark: $(this).attr("data-mark"),
			type: 'post'
		}, function (data)
		{
			var response = JSON.parse(data);
			if (response['rate'])
				$('.rating[data-id=' + postID  + ']').html(response['rate']);
			
			HTMLAlert(response['text'], 'info');
		});
	});

	$(document).on('click', 'button.rate-comment', function()
	{
		var commentID = $(this).attr("data-id");
		$.post('ajax/rate.php', 
		{
			id: commentID, 
			mark: $(this).attr("data-mark"),
			type: 'comment'
		}, function (data)
		{
			var response = JSON.parse(data);
			if (response['rate'])
				$('.commentRate[data-id=' + commentID  + ']').html(response['rate']);
			
			HTMLAlert(response['text'], 'info');
		});
	});




	$('div.postContent > button').click(function()
	{
		$(this).parent().css("max-height", "none");
		$(this).hide();
	});

	function checkIMGsize()
	{
		$('div.postContent').each(function()
		{
			if($(this).children('img').height() > 800)
			{
				$(this).children('button').show();
			}
		});
		
	}
	checkIMGsize();



	function loadComments(postID)
	{
		$('a.reloadComments[data-id=' + postID + ']').children('i').addClass('fa-spin');
		$.post('ajax/comments.php', {id: postID, action: 'show'}, function (data)
		{
			var comments = JSON.parse(data);
			var container = '#comments' + postID + ' > div.c';
			$(container).empty();
			$.each(comments, function(index, value)
			{

				$(container).append('<div class="comment row"><div class="col-1"><img class="avatar" src="' + value['avatar'] +'"></div><div class="col-11"><div class="row header"><div class="col-8 padding-0"><strong>'+ value['username'] + '</strong> <span class="diff" title="' + value['date'] + '">' + value['diff'] + '</span>'+(value['admin'] ? ' <a title="Usuń komentarz" class="removeComment" data-id="' + value['id'] + '" data-post="'+postID+'"><i class="fa fa-trash" aria-hidden="true"></i></a>' : '')+'</div><div class="col-4 padding-0"><button class="btn btn-plus border-0 btn-xs rate-comment" data-id="' + value['id'] + '" data-mark="+"><i class="fa fa-plus" aria-hidden="true"></i></button><span class="commentRate" data-id="' + value['id'] + '">'+ value['rate'] + '</span><button class="btn btn-minus border-0 btn-xs rate-comment" data-id="' + value['id'] + '" data-mark="-"><i class="fa fa-minus" aria-hidden="true"></i></button></div></div><div class="padding-0 content">' + value['text'] + '</div></div></div>');

				$('.showComments .badge[data-id=' + postID + ']').html(index+1);

			});


			if(comments.length == 0)
			{   
				$(container).append('Brak komentarzy');
			}
			$('a.reloadComments[data-id=' + postID + ']').children('i').removeClass('fa-spin');
		});
	}




	$('a.showComments').click(function()
	{
		var postID = $(this).attr('data-id');
		loadComments(postID);

		
		$(this).unbind();
	});


	$('a.reloadComments').click(function()
	{
		var postID = $(this).attr('data-id');
		loadComments(postID);
	});


	$('.addComment textarea').keyup(function()
	{
		$('span[data-type=commentLenght][data-id=' + $(this).attr('data-id') + ']').html($(this).val().length + '/500');
	});


	/* Dodawanie komentarzy */
	$('.addComment button').click(function()
	{
		var id = $(this).attr('data-id');
		var text = $('.addComment textarea[data-id=' + id + ']').val();
		if(text.length >= 3 && text.length <= 500)
		{
			$.post('ajax/comments.php', {id: id, text: text, action: 'add'}, function(data)
			{
				$('.addComment textarea[data-id=' + id + ']').val('');
				loadComments(id);

				var response = JSON.parse(data);
				if(response['code'] == 0)
				{
					HTMLAlert(response['message'], 'error', true);
				}
			});
		}
		else
		{
			$('span[data-type=commentLenght][data-id=' + $(this).attr('data-id') + ']').html('Wpisz minimum 3 znaki');
		}
	});



	$('#postText').keyup(function()
	{
		$('span#textLength').html($(this).val().length + '/10000');
	});


	/* DODAWANIE POSTA */

	$('#toggleAddPost').click(function()
	{
		$(this).parent().children('div').slideToggle();

		if ($(this).children('a').children('i').hasClass('fa-minus-circle'))
		{		
			$(this).children('a').children('i').addClass('fa-plus-circle');
			$(this).children('a').children('i').removeClass('fa-minus-circle');
		}
		else
		{
			$(this).children('a').children('i').addClass('fa-minus-circle');
			$(this).children('a').children('i').removeClass('fa-plus-circle');
		}
		
	});



	function makeTag()
	{
		var tag = $('#addTags').val().substr(0, $('#addTags').val().length);

		tag = tag.replace(' ', '');
		tag = tag.replace(',', '');
		tag = tag.replace('#', '');

		if(tag.length == 0 || $('.tagLabel').length == 5)
		{
			$('#addTags').val('');
			return;
		}

		var unique = true;

		$('.tagLabel .t').each(function()
		{
			if($(this).html() == tag)
			{
				unique = false;
				return false; 
			}
		});

		if(unique)
		{	
			$('#tagsLabels').append('<span class="tagLabel bg-blue">#<span class="t">' + tag + '</span> <a>&times;</a></span>');
			$('#addTags').val('');
		}
		else
		{
			$('#addTags').val(tag);
		}
	}




	$('#addTags').keyup(function(event)
	{
		if(event.which == 188 || event.which == 32)	// Wciśnięcie ','
		{
			var tag = $(this).val().substr(0, $(this).val().length);
			
			makeTag();

		}
	});
	$(document).on('click', '.tagLabel a', function()
	{
		$(this).parent().remove();
	});


	$('#addPostButton span i').hide();
	


	$('#addPostButton').click(function()
	{
		if($('#addTags').val().length > 0)
			makeTag();

		
		var tags = '';
		$('.tagLabel .t').each(function()
		{
			tags += $(this).html() + ',';
		});
		if(tags.length > 0)
		{
			tags = tags.substr(0, tags.length-1);
		}
		$('#addTags2').val(tags);


		$(this).children('i').show();

	});


	$("#addPostForm").validate(
	{
		submitHandler: function(form)
		{
			if($("#file").val())
			{
				if($("#file")[0].files[0].size > 10000000)
				{
					HTMLAlert('Maksymalny rozmiar pliku wynosi 10MB', 'info', false);
					return false;
				}
			}

			$('#addPostForm').ajaxSubmit(
			{
				url: 'ajax/addPost.php',
				type: 'POST',
				data: $('#addPostForm').serialize(),
				cache: false,
				contentType: false,
				processData: false,
				beforeSubmit: function()
				{
					$('#addPostButton span i').show();
				},
				success: function(data)
				{
					var response = JSON.parse(data);
					if(response['code'] == 1)
					{
						window.location.href=response['message'];
					}
					else
					{
						HTMLAlert(response['message'], 'error', false);
					}
					
					$('#addPostButton span i').hide();
				}
			});



		},
		rules: 
		{
			file:
			{
				required: false,
				accept: "image/png, image/gif, image/jpeg, video/mp4"
			}
		}
	});


	$("#addFromYT").click(function()
	{
		$("#fdiv").hide();
		$("#tdiv").removeClass("col-10");
		$("#tdiv").addClass("col-12");
		$("#addFromYT").hide();
		$("#yt").show();

	});





	$("#changePasswordForm").validate(
	{
		submitHandler: function(form)
		{
			$.ajax(
			{
				url: 'ajax/changePassword.php',
				type: 'post',
				data: $(form).serialize(),
				success: function(data)
				{
					var response = JSON.parse(data);
					if(response['code'] == 1)
					{
						HTMLAlert(response['message'], 'ok', true);
					}
					else
					{
						HTMLAlert(response['message'], 'error', false);
					}
					$('#changePasswordForm')[0].reset();
				}
            });
		},
		rules: 
		{
			password:
			{
				required: true,
			},
			newpassword:
			{
				required: true,
				minlength: 6
			},
			newpassword2:
			{
				required: true,
				equalTo: "#newpassword"
			},
		
		}
	});

	



	$("#changeEmailForm").validate(
	{
		submitHandler: function(form)
		{
			$.ajax(
			{
				url: 'ajax/changeEmail.php',
				type: 'post',
				data: $(form).serialize(),
				success: function(data)
				{
					var response = JSON.parse(data);
					if(response['code'] == 1)
					{
						HTMLAlert(response['message'], 'ok', true);
					}
					else
					{
						HTMLAlert(response['message'], 'error', false);
					}
				}
            });
		}
	});





	$("#removeAvatar").click(function()
	{
		$.ajax(
		{
			url: 'ajax/changeAvatar.php',
			type: "POST",
			data: {remove: true},
			success: function(data)
			{
				var response = JSON.parse(data);
				if(response['code'] == 1)
				{
					$("#actavatar").attr("src", response['message']);
					$("#miniavatar").attr("src", response['message']);
					HTMLAlert('Przywrócono domyślny awatar', 'info', true);
				}
			}

		});
	});

	$("#avatar").change(function()
	{

		if($("#avatar").val())
		{
			if($("#avatar")[0].files[0].size > 5000000)
			{
				HTMLAlert('Maksymalny rozmiar awatara wynosi 5MB', 'info', false);
				return false;
			}
		}



		$('#updateAvatarForm').ajaxSubmit(
		{
			url: 'ajax/changeAvatar.php',
			type: 'POST',
			data: $('#updateAvatarForm').serialize(),
			cache: false,
			contentType: false,
			processData: false,
			beforeSubmit: function()
			{

				$("#updateAvatarForm i.fa-spinner").show();
			},
			success: function(data)
			{
				var response = JSON.parse(data);
				if(response['code'] == 1)
				{
					$("#actavatar").attr("src", response['message']);
					$("#miniavatar").attr("src", response['message']);
				}
				else
				{
					HTMLAlert(response['message'], 'error', false);
				}
				
				$("#updateAvatarForm i.fa-spinner").hide();
			}
		});

	});


	$("#showResetForm").click(function()
	{
		$("#resetPasswordForm").slideDown();
		$("#loginForm").slideUp();
	});


	$("#cancelResetPasswordButton").click(function()
	{
		$("#loginForm").slideDown();
		$("#resetPasswordForm").slideUp();
	});



	$("#resetPasswordForm").validate(
	{
		submitHandler: function(form)
		{
			$.ajax(
			{
				url: 'ajax/resetPassword.php',
				type: 'post',
				data: $(form).serialize(),
				success: function(data)
				{
					var response = JSON.parse(data);
					if(response['code'] == 1)
					{
						HTMLAlert(response['message'], 'info', false);
						$("#resetCode").slideDown();
						$("#resetCode").prop('disabled', false);
						
					}
					else if(response['code'] == 2)
					{
						HTMLAlert(response['message'], 'ok', false);
						$("#resetCode").slideUp();
						$("#resetCode").prop('disabled', true);
						$("#resetPasswordForm")[0].reset();

						$("#loginForm").slideDown();
						$("#resetPasswordForm").slideUp();
						
					}
					else
					{
						HTMLAlert(response['message'], 'error', false);
					}
				}
            });
		},
		rules: 
		{
			email:
			{
				required: true
			}
		}
	});


	$("#contactForm").validate(
	{
		submitHandler: function(form)
		{
			$.ajax(
			{
				url: 'ajax/contact.php',
				type: 'post',
				data: $(form).serialize(),
				success: function(data)
				{
					var response = JSON.parse(data);

					HTMLAlert(response['message'], 'info', false);
					$("#contactForm")[0].reset();
				}
            });
		}
	});



	$("a.changeFavorite").click(function()
	{
		var id = $(this).attr('data-id');
		$.ajax(
		{
			url: 'ajax/favorites.php',
			type: 'post',
			data: {id: id, set: true},
			success: function(data)
			{
				var response = JSON.parse(data);

				if(response['status'] == true)
				{
					$("a.changeFavorite[data-id=" + id + "] i").removeClass("fa-star-o");
					$("a.changeFavorite[data-id=" + id + "] i").addClass("fa-star");
				}
				else
				{
					$("a.changeFavorite[data-id=" + id + "] i").removeClass("fa-star");
					$("a.changeFavorite[data-id=" + id + "] i").addClass("fa-star-o");
				}

			}
		});
	});




	$("#followTag").click(function()
	{
		var tag = $(this).attr('data-tag');
		$.ajax(
		{
			url: 'ajax/followTag.php',
			type: 'post',
			data: {tag: tag, set: true},
			success: function(data)
			{
				var response = JSON.parse(data);

				if(response['status'] == true)
				{
					$("#followTag i").removeClass("fa-star-o");
					$("#followTag i").addClass("fa-star");
				}
				else
				{
					$("#followTag i").removeClass("fa-star");
					$("#followTag i").addClass("fa-star-o");
				}

			}
		});
	});

	$("#showFollowedTags").click(function()
	{
		$("#listFollowedTags").slideToggle(100);
	});


	$("#moveToCategory").change(function()
	{
		$("#buttonMoveToCategory").attr("data-to", this.value);
	});

	$(".adminAction button").click(function()
	{
		var id = $(this).parent().attr('data-id');
		var action = $(this).attr('data-action');
		var to = $(this).attr('data-to');
		$.ajax(
		{
			url: 'ajax/adminAction.php',
			type: 'post',
			data: {id: id, action: action, to: to},
			success: function(data)
			{
				var response = JSON.parse(data);

				HTMLAlert(response['message'], 'info', true);

			}
		});
	});


	$(document).on('click', 'a.removeComment', function()
	{
		var commentID = $(this).attr("data-id");
		var postID = $(this).attr("data-post");
		$.post('ajax/comments.php', 
		{
			id: commentID, 
			action: 'delete'
		}, function (data)
		{
			var response = JSON.parse(data);
			loadComments(postID);
			HTMLAlert(response['message'], 'info', true);

		});
	});

	$(".share").click(function()
	{
		var id = $(this).attr('data-id');
		$(".shareLink[data-id=" + id +"]").toggle();
	});




	$("a.usersControl").click(function()
	{
		var id = $(this).attr("data-id");
		var action = $(this).attr("data-action");
		var html = $(this).html();
		$.post('ajax/users.php', 
		{
			id: id, 
			action: action
		}, function (data)
		{
			var response = JSON.parse(data);

			
			HTMLAlert(response['message'], 'info', true);

			if(response['code'] == 1)
			{
				if(action == 'admin')
				{		
					$("a.usersControl[data-id="+id+"][data-action="+action+"]").html((html == '<strong class="text-success">TAK</strong>' ? 'NIE' : '<strong class="text-success">TAK</strong>'));
				}
				else
				{
					$("a.usersControl[data-id="+id+"][data-action="+action+"]").html((html == 'TAK' ? 'NIE' : 'TAK'));
				}
			}

		});
	});





});

