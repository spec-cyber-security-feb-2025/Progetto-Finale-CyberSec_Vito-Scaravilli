<x-layout>
    <div class="container-fluid p-5 bg-secondary-subtle text-center">
        <div class="row justify-content-center">
            <div class="col-12">
                <h1 class="display-1">Work with us</h1>
            </div>
        </div>
    </div>
    <div class="container my-5">
        <div class="row">
            <div class="col-12 col-md-6">
                <form action="{{route('careers.submit')}}" method="POST" class="card p-5 shadow">
                    @csrf
                    <div class="mb-3">
                        <label for="role" class="form-label">Which job position are you applying for?</label>
                        <select name="role" id="role" class="form-control">
                            <option value="" selected disabled>Select Job position</option>
                            @if (!Auth::user()->is_admin)
                                <option value="admin">Administrator</option>
                            @endif
                            @if (!Auth::user()->is_revisor)
                                <option value="revisor">Revisor</option>
                            @endif
                            @if (!Auth::user()->is_writer)
                                <option value="writer">Writer</option>
                            @endif
                        </select>
                        @error('role')
                            <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{old('email') ?? Auth::user()->email}}">
                        @error('email')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Why would we accept you? Tell us!</label>
                        <textarea name="message" id="message" cols="30" rows="7" class="form-control">{{old('message')}}</textarea>
                        @error('message')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="mt-3 d-flex justify-content-center">
                        <button type="submit" class="btn btn-outline-secondary">Send</button>
                    </div>
                </form>
            </div>
            <div class="col-12 col-md-6 p-5">
                <h2>Work as administrator</h2>
                <p>If you choose to be administrator you'll be part of our board, managing every aspect of our business. High responsability!</p>
                <h2>Work as revisor</h2>
                <p>f you choose to be revisor you'll be able to get an extra money helping us to care our community, reviewing articles.</p>
                <h2>Work as writer</h2>
                <p>f you choose to be writer you can unleash your potential and be one of the best writer of our community.</p>
            </div>
        </div>
    </div>
</x-layout>