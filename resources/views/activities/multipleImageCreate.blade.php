@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Schedule
                </div>
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-danger">
                            {!! session('status') !!}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ url('/activities/add/multiple') }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="file[]" style="font-weight:bold; font-size:20px;">Add Images</label>
                                <input type="file" name="file[]" multiple="true">
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <button type="submit" style="float:right;" class="btn btn-primary">Upload Images</button>
                        </div>
                    </form>
                    <form action="{{ url('/activities/add/multiple/schedule') }}" method="POST">
                        {{ csrf_field() }}
                        <br /><br /><br />
                        <hr />
                        <div class="col-xs-12">
                            <label style="font-weight:bold; font-size:20px;">Select Accounts:</label>
                        </div>
                        <div class="col-xs-12">
                            @foreach($user->accounts as $account)
                                <div class="col-xs-3">
                                    <div class="checkbox">
                                        <label>
                                            <input name="enabled[{{ $account->id }}]" type="checkbox" {{ (is_array(old('enabled')) && array_key_exists($account->id, old('enabled'))) ? 'checked="true"' : '' }}> {{$account->username}}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-xs-12">
                            <hr />
                        </div>
                        <div class="col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-striped" style="min-width:650px">
                                    <thead>
                                        <tr style="border-bottom:1.5px solid black;">
                                            <td>
                                                <strong>Thumbnails</strong>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                  <label for="caption_all">Caption All</label>
                                                  <textarea class="form-control" rows="2" id="caption_all" style="resize:vertical;"></textarea>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                  <label for="datetime_all">Time All &nbsp;
                                                      <span style="font-size:9px;" class="timezone_display"></span>
                                                  </label>
                                                  <input autocomplete='off' class="form-control" type="text" id="datetime_all" placeholder="12/31/2020 11:50 pm" />
                                                </div>
                                            </td>
                                            <td>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($files as $file)
                                            <?php $key = base64_encode($file[0]); ?>
                                            <tr>
                                                <td>
                                                    <a href="{{ asset('/storage/'.$file[0]) }}" target="_BLANK">
                                                    @if($file[1])
                                                        <?php
                                                            $ar = explode('_', $file[0]);
                                                            echo end($ar);
                                                        ?>
                                                    @else
                                                        <img src="{{ asset('/storage/'.$file[0]) }}" style="max-width:100px;"></img>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                      <textarea class="form-control caption" rows="2" name="caption[{{$key}}]" style="resize:vertical;">{{ (is_array(old('caption')) && array_key_exists($key, old('caption'))) ? old('caption')[$key] : '' }}</textarea>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                      <input autocomplete='off' class="form-control datetime" type="text" name="datetime[{{ $key }}]" placeholder="12/31/2020 11:50 pm" value="{{ (is_array(old('datetime')) && array_key_exists($key, old('datetime'))) ? old('datetime')[$key] : '' }}"/>
                                                      <input autocomplete='off' type="text" value="{{ (is_array(old('utc_time')) && array_key_exists($key, old('utc_time'))) ? old('utc_time')[$key] : '' }}" name="utc_time[{{$key}}]" style="display:none;" class="utc_time" />
                                                    </div>
                                                </td>
                                                <td>
                                                    <a class="btn btn-danger btn-xs" href="{{ url('/activities/add/multiple/abortFile?file=').$key }}">Remove</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <center><button type="submit" class="btn btn-danger">Schedule</button></center>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function populate_utc_time(obj, content){
        var target = $(obj).parent().children('.utc_time');
        content = content.toLowerCase();
        content = content.replace('am', ' am');
        content = content.replace('pm', ' pm');
        var d = new Date(content);
        if(content == "" || content == "now" || content == "od right now"){
            d = new Date();
        }
        target.val(d.getTime()/1000);
    }
    // [BUG] $(document).on('input') might not trigger on selection of browser suggestion
    $(document).on('input', "#caption_all", function(e){
        var content = $(this).val();
        $('.caption').val(content);
    });
    $(document).on('input', "#datetime_all", function(e){
        var content = $(this).val();
        $('.datetime').each(function(){
            $(this).val(content);
            populate_utc_time($(this), content);
        });
    });
    $(document).on('input', ".datetime", function(e){
        var content = $(this).val();
        populate_utc_time($(this), content);
    });
    $(document).on('change', "#enable_all", function(e){
        var content = $(this).prop('checked');
        $('.enable').prop('checked', content);
    });
    $(document).ready(function(){
        $("#timezone").val(new Date().getTimezoneOffset());
        var val = -1 * (new Date().getTimezoneOffset())/60;
        if (val > -1){
            val = "+" + val;
        }
        $(".timezone_display").text(" (in UTC "+val+").");
        $('.datetime').each(function(){
            populate_utc_time($(this), "");
        });
    });
</script>

@endsection
