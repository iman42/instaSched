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
                            {{ session('status') }}
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
                    <form action="{{ url('/activities/add/single') }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="file" style="font-weight:bold; font-size:20px;">File input</label>
                                <input type="file" id="file" name="file">
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <button style="float:right;" type="submit" class="btn btn-primary">Submit</button>
                        </div>
                        <br />
                        <div class="col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-striped" style="min-width:650px">
                                    <thead>
                                        <tr style="border-bottom:1.5px solid black;">
                                            <td>
                                                <label class="checkbox-inline" style="margin-left:10px;">
                                                  <input type="checkbox" id="enable_all" > <strong>Enable All</strong>
                                                </label>
                                            </td>
                                            <td>
                                                <strong>Account</strong>
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
                                            <!-- <td>
                                                <div class="form-group">
                                                    <label for="time_all">Time All &nbsp;
                                                        <span style="font-size:9px;" class="timezone_display"></span>
                                                    </label>
                                                    <input class="form-control" type="text" id="time_all" placeholder="11:50 pm" />
                                                </div>
                                            </td> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user->accounts as $account)
                                            <tr>
                                                <td>
                                                    <div class="checkbox" style="margin-left:10px; margin-top: 2px; margin-bottom: -6px;">
                                                      <label>
                                                        <input type="checkbox" class="enable" name="enable[{{$account->id}}]" value="{{ $account->id }}" aria-label="..." {{ (is_array(old('enable')) && in_array($account->id, old('enable'))) ? 'checked="true"' : '' }}>
                                                      </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{$account->username}}
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                      <textarea class="form-control caption" rows="2" name="caption[{{$account->id}}]" style="resize:vertical;">{{ (is_array(old('caption')) && array_key_exists($account->id, old('caption'))) ? old('caption')[$account->id] : '' }}</textarea>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                      <input autocomplete='off' class="form-control datetime" type="text" name="datetime[{{ $account->id }}]" placeholder="12/31/2020 11:50 pm" value="{{ (is_array(old('datetime')) && array_key_exists($account->id, old('datetime'))) ? old('datetime')[$account->id] : '' }}"/>
                                                      <input autocomplete='off' type="text" value="{{ (is_array(old('utc_time')) && array_key_exists($account->id, old('utc_time'))) ? old('utc_time')[$account->id] : '' }}" name="utc_time[{{$account->id}}]" style="display:none;" class="utc_time" />
                                                    </div>
                                                </td>
                                                <!-- <td>
                                                    <div class="form-group">
                                                        <input class="form-control time" type="text" name="time[{{$account->id}}]" placeholder="11:50 pm" value="{{ (is_array(old('time')) && array_key_exists($account->id, old('time'))) ? old('time')[$account->id] : '' }}" />
                                                    </div>
                                                </td> -->
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function populate_utc_time(obj, content){
        var target = $(obj).parent().children('.utc_time');
        var d = new Date(content);
        if(content == "" || content == "now" || content == "OD RIGHT NOW"){
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
