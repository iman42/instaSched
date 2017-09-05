@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <center>
                <a class="btn btn-primary btn-lg" href="{{ url('/activities/add') }}">
                    <div class="col-xs-6" style="margin-top:13px;">
                        Schedule Post
                    </div>
                    <div class="col-xs-6">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true" style="font-size:60px; float:right; margin-top:-10px"></span>
                    </div>
                </a>
            </center>
            <br />
            <div class="panel panel-default">
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif


                    <div class="table-responsive">
                        <table class="table table-striped" style="min-width:650px">
                            <thead>
                                <tr>
                                    <th>Thumbnail</th>
                                    <th>Caption</th>
                                    <th>Account</th>
                                    <th>Scheduled Time</th>
                                    <th>Errors</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks as $task)
                                    <tr>
                                        <td><img src="{{ asset('/storage/'.$task->filepath) }}" style="max-width:100px;"></img></td>
                                        <td>{{ $task->caption }}</td>
                                        <td>{{ $task->account_name }}</td>
                                        <td><span class="utc_time">{{ $task->timestamp }}</span></td>
                                        <td>{{ $task->error }}</td>
                                        <td><a class="btn btn-danger btn-xs" href="{{ url('/delete/activity/' . $task->id ) }}" role="button">Remove</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $(".utc_time").each(function(obj){
            var rawTime = parseInt($(this).text());
            var d = new Date(rawTime * 1000);
            $(this).text(d.toLocaleString());
        });
    });
</script>

@endsection
