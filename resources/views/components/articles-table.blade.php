<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th scope="col">#</th>
            <th scope="col">Title</th>
            <th scope="col">Subtitle</th>
            <th scope="col">Author</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($articles as $article)
            <tr>
                <th scope="row">{{$article->id}}</th>
                <td>{{$article->title}}</td>
                <td>{{$article->subtitle}}</td>
                <td>{{$article->user->name}}</td>
                <td>
                    @if (is_null($article->is_accepted))
                        <a href="{{route('articles.show', $article)}}" class="btn btn-secondary">Read</a>
                    @else
                        <form action="{{route('revisor.undoArticle', $article)}}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-secondary">Back to review</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>