<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th scope="col">#</th>
            <th scope="col">Title</th>
            <th scope="col">Subtitle</th>
            <th scope="col">Category</th>
            <th scope="col">Tags</th>
            <th scope="col">Created at</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($articles as $article)
            <tr>
                <th scope="row">{{$article->id}}</th>
                <td>{{$article->title}}</td>
                <td>{{$article->subtitle}}</td>
                <td>{{$article->category->name ?? 'Nessuna categoria'}}</td>
                <td>
                    @foreach ($article->tags as $tag)
                        #{{ $tag->name }}
                    @endforeach
                </td>
                <td>{{$article->created_at->format('d/m/Y')}}</td>
                <td>
                    <a href="{{route('articles.show', $article)}}" class="btn btn-secondary">Read</a>
                    <a href="{{route('articles.edit', $article)}}" class="btn btn-warning text-white">Edit</a>
                    <form action="{{route('articles.destroy', $article)}}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>