<div>
	@foreach ($blogs as $id => $blog)
		{{ $blog['date_created'] }}
		{{ HTML::link("blog/view/{$id}",  $blog['title']); }}
	@endforeach
</div>
