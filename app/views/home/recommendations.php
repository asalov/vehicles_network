	<?php $searchResults = $this->get('recommendations'); ?>

	<?php if($searchResults !== null): ?>
	<div class="video">
		<h1>Video recommendations for: <span class="search-query"><?php echo esc($searchResults->searchQuery); ?></span></h1>
		<div class="pull-right map-link">
			<a href="<?php echo PATH; ?>driver/map">Last driven path</a>
		</div>			
		<ul class="list-group">
		<?php foreach($this->get('recommendations') as $video): ?>
		<li class="list-group-item">
			<a href="https://youtube.com/watch?v=<?php echo esc($video->id->videoId); ?>" target="_blank">
				<h4><?php echo esc($video->snippet->title); ?></h4>
				<div class="row">
					<div class="col-xs-12 col-md-5">
						<img class="img-responsive" src="<?php echo esc($video->snippet->thumbnails->medium->url); ?>" 
						alt="Video thumbnail">
					</div>
					<div class="desc col-xs-12 col-md-6"><?php echo esc($video->snippet->description); ?></div>
				</div>
			</a>
		</li>	
		<?php endforeach; ?>
		</ul>
		<nav>
			<ul class="pager">
				<?php if($searchResults->prevPageToken): ?>
					<li><a href="<?php echo PATH . 'driver/index/' .esc($searchResults->prevPageToken); ?>">Previous page</a></li>
				<?php endif; ?>
				<li><a href="<?php echo PATH . 'driver/index/' . esc($searchResults->nextPageToken); ?>">Next page</a></li>
			</ul>
		</nav>		
	</div>
	<?php else: ?>
		<div class="alert alert-info">You have no assigned vehicles.</div>
	<?php endif; ?>