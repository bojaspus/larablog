<div>
	{{ Form::open('/blog/new', 'POST'); }}
		{{ Form::label('title', 'Title:'); }}
		{{ Form::text('title') }}

		{{ Form::label('body', 'Body:'); }}
		{{ Form::textarea('body'); }}

		{{ Form::submit('Create'); }}
	{{  Form::close(); }}
</div>
