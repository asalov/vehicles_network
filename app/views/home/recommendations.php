	<?php $searchResults = $this->get('recommendations'); ?>

	<h1>Video recommendations for: <span class="search-query"><?php echo esc($searchResults->searchQuery); ?></span></h1>
	<?php if($searchResults !== null): ?>
		<ul class="list-group">
		<?php foreach($this->get('recommendations') as $video): ?>
		<li class="list-group-item">
			<a href="https://youtube.com/watch?v=<?php echo esc($video->id->videoId); ?>" target="_blank">
				<h4><?php echo esc($video->snippet->title); ?></h4>
				<img class="img-responsive" src="<?php echo esc($video->snippet->thumbnails->medium->url); ?>" alt="Video thumbnail">
				<div><?php echo esc($video->snippet->description); ?></div>
			</a>
		</li>	
		<?php endforeach; ?>
		</ul>
		<nav>
			<ul class="pager">
				<?php if($searchResults->prevPageToken): ?>
					<li><a href="<?php echo PATH . 'role/driver/' .esc($searchResults->prevPageToken); ?>">Previous page</a></li>
				<?php endif; ?>
				<li><a href="<?php echo PATH . 'role/driver/' . esc($searchResults->nextPageToken); ?>">Next page</a></li>
			</ul>
		</nav>
	<?php else: ?>
		<div class="alert alert-info">You have no assigned vehicles.</div>
	<?php endif; ?>