<x-layout>
    <div class="container-fluid p-5 bg-secondary-subtle text-center">
        <div class="row justify-content-center">
            <div class="col-12">
                <h1 class="display-1 text-capitalize">{{ $user->name }}</h1>
            </div>
        </div>
    </div>
    <div class="container my-5">
        <div class="row justify-content-evenly">
            @foreach ($articles as $article)
                <div class="col-12 col-md-3">
                    <div class="card" style="width: 18rem;">
                        <img src="{{ Storage::url($article->image) }}" class="card-img-top" 
                            alt="Immagine dell'articolo: {{ $article->title }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $article->title }}</h5>
                            <p class="card-subtitle">{{ $article->subtitle }}</p>
                            @if ($article->category)
                                <p class="small text-muted">Category: 
                                    <a href="{{route('articles.byCategory', $article->category)}}" class="text-capitalize text-muted">{{ $article->category->name }}</a> 
                                </p>
                            @else
                                <p class="small text-muted">No category</p>
                            @endif
                            <p class="small text-muted my-0">
                                @foreach ($article->tags as $tag)
                                    #{{ $tag->name }}
                                @endforeach
                            </p>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <p>Created at {{$article->created_at->format('d/m/Y')}}</p>
                            <a href="{{route('articles.show', $article)}}" class="btn btn-outline-secondary">Read more</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layout>