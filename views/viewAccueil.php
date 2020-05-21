<?php
if (session_status() == PHP_SESSION_NONE)
	{
		session_start();
	}
	$t = "Camagru accueil";
	ob_start();
	if(!empty($_SESSION["page_courante"]))
	{
		$current_page = ($_SESSION["page_courante"]);
	}

?>
<script>

	window.onload = function ()
	{	
		// Get the modal
		var modal = [];
		var tab = [];
		var spantest = [];
		for(var i = 0; i < 6 ; i++)
		{
			if(document.getElementById("myBtn"+i))
			{
				tab[i] = document.getElementById("myBtn"+i);
			}

			if(document.getElementById("myModal"+i))
			{
				modal[i] = document.getElementById("myModal"+i);
			}

			if(document.getElementById("close"+i))
			{
				spantest[i] = document.getElementById("close"+i);
			}
		}

		function display_modal(event, i)
		{
			event.preventDefault();
			modal[i].style.display ="block";
		};

		function close_modal(event, i)
		{
			event.preventDefault();
			modal[i].style.display="none";
		}


		/*
			LISTENER MODAL
		*/

		for(i = 0; i < 6 ; i++)
		{
			if(tab[i])
			{
				tab[i] = document.getElementById("myBtn"+i);
				tab[i].addEventListener("click", function(event)
				{
					display_modal(event, ((this.id).slice(-1)));
				}, true);

				spantest[i] = document.getElementById("close"+i);
				spantest[i].addEventListener("click", function(event){
					close_modal(event, ((this.id).slice(-1)));
				}, true);
			}
		}


		/*
			COMMENT FUNCTIONS PART
		*/

		var id_photo;	
		var form = [];
		var form_modal = [];
		var see_other_comments = [];
		var link_comment = [];
		var link_likes = [];
		var like_home = [];
		var like_home_modal = [];
		var count = 0;
		var curr_user = <?php if(!empty($_SESSION["loggued_on"])) { echo json_encode($_SESSION["loggued_on"]); } else echo (-1); ?>;
		var URL = <?php echo json_encode(URL); ?>;
		
		function send_id_photo(event, id, modal)
		{
			event.preventDefault();
			if(curr_user == -1)
			{
				document.getElementById('alert').innerHTML = "<div class='alert alert-dark' role='alert'>Pour intéragir aves les autres utilisateurs veuillez vous connectez svp !</div>";
				return ;
			}
			id_photo = id.slice(-1);
			var curr_page = new URLSearchParams(window.location.search);
			curr_page = curr_page.toString();
			curr_page = curr_page.split('=')[1];
			if(curr_page == null)
				curr_page = 0;
			if(modal == 1)
			{
				var commentaire = document.getElementById("text-modal" +id_photo).value;
				document.getElementById("text-modal" +id_photo).value = "";
			}
			else{
				var commentaire = document.getElementById("text" +id_photo).value;
				document.getElementById("text" +id_photo).value = "";
			}
			var data_post= {
			'id_photo' : id_photo,
			'curr_page' : curr_page,
			'user': curr_user,
			'commentaire' : commentaire,
			};
			var dataString = JSON.stringify(data_post);
			$.ajax({
				url: 'Accueil',
				data:{myData: dataString},
				type: 'POST',
				success:function (result)
				{
					document.getElementById('alert').innerHTML = "<div class='alert alert-dark' role='alert'>"+result+"</div>";
					get_last_comment(event, id_photo);
				},
				error: function(xhr, status, error)
				{
					console.log('erreur' + status + 'message:' + error);
				}
			});
		}

		function get_last_comment(event,id)
		{
			event.preventDefault();
			
			count = 0;
			var pos = id;
			var curr_page = new URLSearchParams(window.location.search);
			curr_page = curr_page.toString();
			curr_page = curr_page.split('=')[1];
			if(curr_page == null)
				curr_page = '0';
			
			var data_post = {
				'pos_photo' :pos,
				'curr_page' : curr_page,
				'count' : count,
				'user' : curr_user,
			};
			var dataString = JSON.stringify(data_post);
			$.ajax({
				url: 'Accueil',
				data: {myData3: dataString},
				type:'POST',
				success:function (result)
				{
					var tab = JSON.parse(result);
					var len = tab.length;
					var curr_picture = (data_post['pos_photo']);
					var comment = [];
					var img = [];

					if(len >=1)
					{
						for(var i = 0 ; i < len -1 ; i ++)
						{
							if(document.getElementById('commentaire'+ curr_picture +i))
							{
								comment[i] = document.getElementById('commentaire'+ curr_picture +i);
								comment[i].style.display = "block";
								comment[i].getElementsByTagName('img')[0].src = tab[i]['id_photo'];
								comment[i].getElementsByClassName('pseudo_commentaire')[0].innerHTML = tab[i]['id_user'];
								comment[i].getElementsByClassName('commentaire')[0].innerHTML = tab[i]['commentaire'];
							}
						}
					}
				},
				error: function(xhr, status, error)
				{
					console.log('erreur' + status + 'message:' + error);
				}
			});
		}

		function ask_new_comment(event)
		{
			event.preventDefault();
			
			count++;
			var pos = this.id;
			if(pos)
				pos = pos.slice(-1);
			else
				pos = 0;
			var curr_page = new URLSearchParams(window.location.search);
			curr_page = curr_page.toString();
			curr_page = curr_page.split('=')[1];
			if(curr_page == null)
				curr_page = '0';

			var data_post={
				'pos_photo': pos,
				'curr_page' : curr_page,
				'count' : count,
				'user' : curr_user,
			};

			var dataString = JSON.stringify(data_post);
			$.ajax({
				url: 'Accueil',
				data: {myData2: dataString},
				type: 'POST',
				success:function (result)
				{
					var tab = JSON.parse(result);

					var len = tab.length;
					var curr_picture = pos;
					var comment = [];
					var img = [];
					for(var i = 0 ; i < len -1 ; i ++)
					{
						if(document.getElementById('commentaire'+ curr_picture +i))
						{
							comment[i] = document.getElementById('commentaire'+ curr_picture +i);
							comment[i].getElementsByTagName('img')[0].src = tab[i]['id_photo'];
							comment[i].getElementsByClassName('pseudo_commentaire')[0].innerHTML = tab[i]['id_user'];
							comment[i].getElementsByClassName('commentaire')[0].innerHTML = tab[i]['commentaire'];
						}
					}
									
				},
				error: function(xhr, status, error)
				{
					console.log('erreur' + status + 'message:' + error);
				}
			});
		}

		function update_like(photo, curr_page)
		{
			var data_post = 
			{
				'pos_photo': photo,
				'curr_page': curr_page,
			};

			var dataString = JSON.stringify(data_post);
			$.ajax({
				url: 'Accueil',
				data: {myData6: dataString},
				type: 'POST',
				success: function (result)
				{
					var x = document.getElementById('likes'+photo);
					x.innerHTML = result + " likes";
					var y = document.getElementById('likes_modal'+photo);
					y.innerHTML = result + " likes";
				},
				error: function(xhr, status, error)
				{
					console.log('erreur' + status + 'message:' + error);
				}
			});
		}

		function delete_like(photo, curr_page)
		{
			var data_post = 
			{
				'pos_photo': photo,
				'curr_page':curr_page,
			};
			var dataString = JSON.stringify(data_post);
			$.ajax({
				url: 'Accueil',
				data: {myData5: dataString},
				type: 'POST',
				success: function (result)
				{
				},
				error: function(xhr, status, error)
				{
					console.log('erreur' + status + 'message:' + error);
				}
			});

		}

		function add_like(event, id, modal)
		{
			event.preventDefault();
			if(curr_user == -1)
			{
				document.getElementById('alert').innerHTML = "<div class='alert alert-dark' role='alert'>Pour intéragir aves les autres utilisateurs veuillez vous connectez svp !</div>";
				return ;
			}
			var pos = id;
			var curr_page = new URLSearchParams(window.location.search);
			curr_page = curr_page.toString();
			curr_page = curr_page.split('=')[1];
			if(curr_page == null)
				curr_page = '0';

			var data_post =
			{
				'pos_photo' : pos,
				'curr_page' : curr_page,
			};

			var dataString = JSON.stringify(data_post);
			$.ajax({
				url: 'Accueil',
				data : {myData4: dataString},
				type: 'POST',
				success:function (result)
				{
					var img_like = document.getElementById('like_home'+id);
					img_like = img_like.getElementsByTagName('img')[0];
					var img_like_modal = document.getElementById('like_home_modal'+id)
					img_like_modal = img_like_modal.getElementsByTagName('img')[0];
					if(modal == 1)
					{
						if(img_like_modal.src == (URL + "views/images/like.png"))
						{
							img_like_modal.src = URL + "views/images/like_plein.png";
							img_like.src = URL + "views/images/like_plein.png"	
						}
						else
						{
							delete_like(pos, curr_page)
							img_like_modal.src = URL + "views/images/like.png";
							img_like.src = URL + "views/images/like.png";
						}
					}
					else
					{
						if(img_like.src == (URL + "views/images/like.png"))					
						{
							img_like_modal.src = URL + "views/images/like_plein.png";
							img_like.src = URL + "views/images/like_plein.png"	
						}
						else
						{
							delete_like(pos, curr_page);
							img_like_modal.src = URL + "views/images/like.png";
							img_like.src = URL + "views/images/like.png";
						}
					}
					update_like(pos, curr_page);
				},
				error: function(xhr, status, error)
				{
					console.log('erreur' + status + 'message:' + error);
				}
			});
		}

		/*
			COMMENTS LISTENER 
		*/

		for(var i = 0; i < 6 ; i++)
		{

			if(document.getElementById("form"+i))
			{
				form[i] = document.getElementById("form"+i);
				form[i].addEventListener("submit", function (event)
				{
					send_id_photo(event ,((this.id)),0)
				}, true);
			}
			

			if(document.getElementById("comment" + i))
			{
				see_other_comments[i] = document.getElementById("comment" + i);
				see_other_comments[i].addEventListener("click", ask_new_comment, true);
			}

			if(document.getElementById("comments_home" + i))
			{
				link_likes[i] = document.getElementById("comments_home" + i);
				link_likes[i].addEventListener("click", function (event)
				{
					display_modal(event, ((this.id).slice(-1)));
				}, true);
			}
		

			if(document.getElementById("form-modal"+i))
			{
				form_modal[i] = document.getElementById("form-modal"+i);
				form_modal[i].addEventListener("submit", function (event)
				{
					send_id_photo(event ,((this.id)), 1)
				}, true);
			}			
			
		}

		/*
			LIKES LISTENER
		*/
		for(var i = 0; i < 6 ; i++)
		{

			if(document.getElementById("like_home"+i))
			{
				like_home[i] = document.getElementById("like_home"+i);
				like_home[i].addEventListener("click", function (event)
				{
					add_like(event ,((this.id).slice(-1)),0)
				}, true);
			}

			if(document.getElementById("like_home_modal"+i))
			{
				like_home[i] = document.getElementById("like_home_modal"+i);
				like_home[i].addEventListener("click", function (event)
				{
					add_like(event ,((this.id).slice(-1)),1)
				}, true);
			}	

		}

	}
		
	</script>

<!-- Modal content -->
<div id="alert">
</div>

	<div class="container_photos_pagination_home">
		<div class="container_photos_home">

		
<?php	

	$content = ob_get_clean();
	require_once('template.php');

	
?>
