<?php
if(!defined("SUBREAD")){die();}

$subreddit = "";

if(isset($_GET['r'])){
	$subreddit = strtolower($_GET['r']);
} else {
	$subreddit = $config['defaultsubreddit'];
}

$file = __DIR__."/../json/".$subreddit.".json";
$data = '';
$mod = "";
if(file_exists($file)){
	//Check if we should download new file:
	//date('Y-m-d h:i',$post['data']['created_utc'])
	if(time()-filemtime($file) > $config['refresh'] || isset($_GET['force'])){
		file_put_contents($file, file_get_contents('https://www.reddit.com/r/'.$subreddit.'/new.json?sort=new&limit='.$config['max']));
	}
	$data = json_decode(file_get_contents($file), true);
} else {
	//OOPS
	//Grab latest data NOW.
	file_put_contents($file, file_get_contents('https://www.reddit.com/r/'.$subreddit.'/new.json?sort=new&limit='.$config['max']));
	if(file_exists($file)){
		$data = json_decode(file_get_contents($file), true);
	} else {
		echo "<h3>".$t["fail.grabdata"]."</h3>";
	}
}
$posts = $data["data"]["children"];
?>
<p><?= sprintf($t["dataupdated"],date($t["date"],filemtime($file)),count($posts)) ?></p>
<div class="row">
<?php
$i = 0;
foreach($posts as $post){
	if($i == 3){
		echo '</div><div class="row">';
		$i = 0;
	}
	?>
	<div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 grid-margin stretch-card">
		<div class="card card-statistics bg-secondary">
			<div class="card-body">
				<div class="clearfix">
					<div class="float-left">
						<p class="mb-0">
							<?php
							//<span class="badge badge-info">Info</span>
							if(!empty(trim($post['data']['link_flair_text']))){
								echo '<span class="badge badge-info">'.$post['data']['link_flair_text'].'</span>';
							}
							?>
							<b><?php echo $post['data']['title'];?></b></p>
						<div class="fluid-container">
							<br>
							<center>
							<?php
							if(isset($post['data']['thumbnail_width'])){
								if(!empty(trim($post['data']['thumbnail_width'])) && $post['data']['spoiler'] == false){
									?><img class="align-center img-responsive" src="<?php echo $post['data']['thumbnail']; ?>"><?php
								}
							}elseif($post['data']['spoiler'] == true){
								?>
								<span class="badge badge-danger"><?= $t["spoilerAlert"] ?></span>
								<?php
							}
							?>
							<p><a class="align-bottom" target="_blank" href="https://reddit.com<?php echo $post['data']['permalink'];?>">Permalink</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class="align-bottom" target="_blank" href="<?php echo $post['data']['url'];?>"><?= $t["externalURL"] ?></a></p>
							</center>
							<br>
						</div>
					</div>
				</div>
			</div>
			<div class="card-footer"><?= sprintf($t["posted"],date($t["date"],$post['data']['created_utc']));?></div>
		</div>
	</div>
	<?php
	$i++;
}
?>