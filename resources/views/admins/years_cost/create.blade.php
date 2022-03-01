@extends('layouts.admin')

@section('title', "add year cost")


@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>add</h1>

            <ol class="breadcrumb">
                <li> <a href="#"><i class="fa fa-dashboard"></i>dashboard</a></li>
                <li> <a href="#"><i class="fa fa-users"></i>years cost</a></li>
                <li class="active"><i class="fa fa-plus"></i>add</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">
                    <h1 class="box-title">add</h1>
                </div> {{-- end of box header --}}

                <div class="box-body">

                    {{-- @include('admins.partials._errors') --}}
                    <form action="" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row" style="margin: 0 !important;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>cost</label>
                                <input type="number" step="0.01" class="form-control  @error('cost') is-invalid @enderror" name="cost"
                                    placeholder="cost" value="{{ old('cost') }}" required autocomplete="off">
                                @error('cost')
                                    <small class=" text text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>year</label>
                                <select name="year_id" class="form-control">
                                    @foreach ($years as $year)
                                        <option value="{{$year->id}}">{{$year->Level->Curriculum->translate('en')->name}} => {{$year->Level->translate('en')->name}} => {{$year->translate('en')->name}}</option>
                                    @endforeach
                                </select>
                                @error('year_id')
                                    <small class=" text text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </small>
                                @enderror
                            </div>
                        </div>

                        <div class="row" style="margin: 0 !important;">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i>
                                        add</button>
                                </div>
                            </div>
                        </div>

                    </form> {{-- end of form --}}

                </div> {{-- end of box body --}}

            </div> {{-- end of box --}}

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection
