@extends('layouts.app')

@section('content')
<div class="container" style="background:url('https://i.pinimg.com/736x/c2/01/1d/c2011d11739b9672541548e786d79f65--milk-cans-milk-jug.jpg');">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <center>
                <a class="btn btn-danger btn-lg" href="{{ url('/admin/key/add') }}">
                    <div class="col-xs-6" style="margin-top:13px;">
                        Add Key
                    </div>
                    <div class="col-xs-6">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true" style="font-size:60px; float:right; margin-top:-10px"></span>
                    </div>
                </a>
            </center>
            <br />
            <div class="panel panel-default">
                <div class="panel-heading">Keys</div>
                <div class="panel-body">
                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-danger">
                                {{ session('status') }}
                            </div>
                        @endif
                    </div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <td><strong>#</strong></td>
                                <td><strong>Key</strong></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($keys as $key)
                                <tr>
                                    <td>{{ $key->id }}</td>
                                    <td>{{ $key->key }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            Fuck you, Ian, you milk drinking bastard.
        </div>
    </div>
</div>
@endsection
