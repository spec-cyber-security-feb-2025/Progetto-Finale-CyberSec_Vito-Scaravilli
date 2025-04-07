<x-layout>
    <div class="container-fluid p-5 bg-secondary-subtle text-center">
        <div class="row justify-content-center">
            <div class="col-12">
                <h1 class="display-1">Sign In</h1>
            </div>
        </div>
    </div>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <form action="{{route('login')}}" method="POST" class="card p-5 shadow">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}">
                        @error('email')
                            <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                        @error('password')
                            <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="mt-3 d-flex justify-content-center flex-column align-items-center">
                        <button type="submit" class="btn btn-outline-secondary">Sign In</button>
                        <p class="mt-2">Don't you have an account yet? <a href="{{route('register')}}" class="text-secondary">Register here</a></p>
                    </div>
                </form>
            </div>
            <div class="col-12 col-md-4">
                <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Email</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th scope="row">1</th>
                        <td>user@aulab.it</td>
                      </tr>
                      <tr>
                        <th scope="row">2</th>
                        <td>writer@aulab.it</td>
                      </tr>
                      <tr>
                        <th scope="row">3</th>
                        <td>revisor@aulab.it</td>
                      </tr>
                      <tr>
                        <th scope="row">4</th>
                        <td>admin@aulab.it</td>
                      </tr>
                      <tr>
                        <th scope="row">5</th>
                        <td>super.admin@aulab.it</td>
                      </tr>
                      <tr>
                        <th scope="row">5</th>
                        <td>kvrs@gmail.com</td>
                      </tr>
                    </tbody>
                </table>
                <div>
                    Instructions:
                    <ul>
                        <li>Set aliases in hosts file [read documentation]</li>
                        <li>For user/writer/revisor go to <a href="http://cyber.blog:8000">http://cyber.blog:8000</a></li>
                        <li>For admin/super admin go to <a href="http://internal.admin:8000">http://internal.admin:8000</a></li>
                        <li>Passowrd is <b>password</b> for every user</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-layout>