<x-layout>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h3 class="mb-0">Il tuo profilo</h3>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <h4>Informazioni personali</h4>
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Aggiorna profilo</button>
                        </form>
                        
                        <hr class="my-4">
                        
                        <h4>Cambia password</h4>
                        <form action="{{ route('profile.update-password') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Password attuale</label>
                                <input type="password" class="form-control" id="current_password" name="current_password">
                                @error('current_password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Nuova password</label>
                                <input type="password" class="form-control" id="password" name="password">
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Conferma nuova password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Aggiorna password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>