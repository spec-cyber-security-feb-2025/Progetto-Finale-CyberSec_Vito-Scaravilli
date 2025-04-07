<div class="card" style="width: 18rem;">
    <img src="{{ Storage::url($article->image) }}" class="card-img-top" alt="Immagine dell'articolo: {{ $article->title }}">
    <div class="card-body">
        <h5 class="card-title">{{ $article->title }}</h5>
        <p class="card-subtitle">{{ $article->subtitle }}</p>
        @if ($article->category)
            <p class="small text-muted">Category:
                <a href="{{route('articles.byCategory', $article->category)}}" class="text-capitalize text-muted">
                    {{ $article->category->name }}
                </a>
            </p>
        @else
            <p class="small text-muted">No category</p>
        @endif
        <p class="small text-muted my-0">
            @foreach ($article->tags as $tag)
                #{{ $tag->name }}
            @endforeach
        </p>
        <p class="card-subtitle text-muted fst-italic small">Reading time {{ $article->readDuration() }} min</p>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <p>Created at {{$article->created_at->format('d/m/Y')}} <br>
            By <a class="text-muted" href="{{ route('articles.byUser', $article->user) }}">{{$article->user->name}}</a>
        </p>
        <a href="{{route('articles.show', $article)}}" class="btn btn-outline-secondary">Read more</a>
    </div>
</div>