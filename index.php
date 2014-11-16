<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Othyn Fall">
	<meta name="keywords" content="Othyn - Media Production, Programming, Web Development, Gaming and Entertainment." />
	<meta name="author" content="Othyn">
	<link rel="shortcut icon" href="favicon.png">
	<title>Airz23 Story Time</title>
	<!-- Bootstrap core CSS -->
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<!-- Custom sheet -->
	<link href="css/base.css" rel="stylesheet">
	<!-- Book sheets -->
	<link rel="stylesheet" type="text/css" href="css/component2.css" />
	
	<!-- The 3D Bookshelf itself isn't made by me, but is made by Mary Lou over at codrops, check it out below, amazing work! -->
	<!-- http://tympanus.net/codrops/2013/01/08/3d-book-showcase/ -->
	<!-- Although I have modified quite a few things to make it fit into the project, so it may not look exactly the same -->
	
	<!-- Credit for the bookshelf design goes to: -->
	<!-- http://medialoot.com/item/free-vector-shelves/ -->
	<!-- I've tweaked the design slightly, mainly the width. I also used those assets to create the sign at the top, to maintain uniformity -->
	
	<!-- The implementation (idea & design to coding & logic) was all me, with a little help from archived threads on stackexchange (to get the gist of a few things) and other forums (mainly for the regex, in which these site are awesome for testing regex: http://www.regexr.com/ and http://www.phpliveregex.com/) and the PHP function reference sheets for refreshing my memory on the syntax -->
</head>

<?php
	//die();
	
	//error_reporting(E_ALL);
	
	function create_speech($speech) {
		return $speech[0].'"<br>';
	}
	
	date_default_timezone_set("Europe/London");
	$string_reddit = file_get_contents("http://www.reddit.com/user/airz23/submitted/.json");
	$json = json_decode($string_reddit, true);
	$feed = $json['data']['children'];
	$time = time();
	$time_minus_day = strtotime('-1 day', $time);
	$i=0;
	foreach($feed as $story){
		$airz_stories[$i] = array(
			'id' 		  => $story['data']['id'],
			'url'		  => $story['data']['url'],
			'subreddit'   => $story['data']['subreddit'],
			'score'       => $story['data']['score'],
			'title' 	  => (strlen($story['data']['title'])>40?substr($story['data']['title'],0,40).'...':$story['data']['title']),
			'full_title'  => $story['data']['title'],
			//'html_story'  => strip_tags(html_entity_decode($story['data']['selftext_html'])),
			//'_html_story' => str_split(strip_tags(html_entity_decode($story['data']['selftext_html'])),350),
			'submit_date' => date('d/m/Y H:s', $story['data']['created']),
			'is_new'	  => ($story['data']['created']>$time_minus_day?'new-story':''),
			'is_new_text' => ($story['data']['created']>$time_minus_day?'<i>NEW - </i>':''),
			'increment'	  => $i
		);
		
		// Start story content
		//-------------------------
		$html_story = html_entity_decode($story['data']['selftext_html']);
		$html_story = preg_replace("/<a [a-zA-Z]+.+<\/a>/", "", $html_story); //remove the previous / next links, and any other links
		$html_story = preg_replace("/:\s/", ":&nbsp;&quot;", $html_story); //To be more accurate that it is a quote, I could use a backreference, but the problem comes when there are mutliple quotes within one set of blockquote tags.
		$html_story = strip_tags($html_story);
		$html_story = preg_replace_callback("/&nbsp;&quot;[a-zA-Z]+.+/", 'create_speech', $html_story); //Could also use a backreference and then append the quote onto that, but ah well
		$html_story = '<div class="bk-content">'.wordwrap($html_story, 300, '</div><div class="bk-content">').'</div>';
		//$html_story = preg_replace( '/((?:\S*?\s){25})/', "<div class=\"bk-content\">$1</div>", $html_story);
		//$html_story = preg_split("/((?:\S*?\s){25})/", $html_story, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
		//couldn't use regex thanks to it not being able to include the final wording in the string, so it would miss the tail off the strings as they didn't match the regex fed into it
		$airz_stories[$i]['_html_story'] = $html_story;
		//-------------------------
		// End story content	-- How it works:
		//	- encodes the html tags back into a useable state
		//	- then, it adds quote marks for formatting to characters speech
		//	- I then remove the html tags from the string as I don't need them, could have done this in the same line as the html_entity_decode but I was using the <blockquote> tags as a reference before I realised that wasn't going to work
		//	- it then finds where the opening quote marks are to the characters speech and appends the closing quote mark to the string
		//	- once its processed the string with the relevant formatting, I break it down into the book pages, wrapping it in the relevant div tags that the book requires to define pages
		//	- then pass that to the book contents array to be displayed to the user
		
		$i++;
	}
?>

<body>

	<div class="container">
		<div class="row">
			
			<div class="col-xs-12 col-md-12 col-lg-12">
				
				<div class="main">
					<div class="sign"></div>
					<div style="margin-left:40px;">
						<ul id="bk-list" class="bk-list clearfix">
					
						<?php foreach($airz_stories as $key => $val): ?>
							<li>
								<div class="bk-book book-1 <?=$val['is_new'];?>">
									<div class="bk-front">
										<div class="bk-cover">
											<h2>
												<span style="font-size:8pt;"><?=$val['title'];?></span>
											</h2>
										</div>
										<div class="bk-cover-back"></div>
									</div>
									<div class="bk-page">
										<div class="bk-content bk-content-current book-chapter-title">
											<?=$val['full_title']?>
											<br />
											<p class="book-chapter-title-author">- Airz23, <?=$val['submit_date'];?></p>
										</div>
										<?=$val['_html_story'];?>
									</div>
									<div class="bk-back">
									</div>
									<div class="bk-right"></div>
									<div class="bk-left">
										<h2>
											<span style="font-size:8pt;"><?=$val['is_new_text'];?><?=$val['title'];?></span>
										</h2>
									</div>
									<div class="bk-top"></div>
									<div class="bk-bottom"></div>
								</div>
							</li>
							<?php endforeach; ?>
							
						</ul>
					</div>
					<div class="bookshelf"></div>
				</div>
				
			</div>
		</div>
	</div>

	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	
	<!-- Book JS -->
	<script src="js/modernizr.custom.js"></script>
	<script src="js/books2.js"></script>
	<script>
		$(function() {

			Books.init();

		});
	</script>

</body>

</html>