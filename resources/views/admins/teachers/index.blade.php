@extends('layouts.admin')

@section('title', 'teachers')


@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>teachers</h1>

            <ol class="breadcrumb">
                <li> <a href="{{url('admins')}}"><i class="fa fa-dashboard"></i>dashboard</a>
                </li>
                <li class="active"><i class="fa fa-users"></i>teachers</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">
                    <form action="#" method="get">

                        <div class="row">

                            <div class="col-md-4">
                                <input type="text" name="search" value="search" class="form-control"
                                    placeholder="search">
                            </div>

                            <div class="col-md-4">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i>
                                    search
                                </button>
                                {{-- @if (auth('admin')->user()->isAbleTo('create-teachers'))
                                    <a href="{{url('admins/teachers/create')}}"
                                    class="btn btn-primary"><i class="fa fa-plus"></i>add
                                    </a>
                                @else
                                    <button class="btn btn-primary"disabled><i class="fa fa-plus"></i>Add </button>
                                @endif --}}
                            </div>
                        </div>
                    </form>
                </div> {{-- end of box header --}}

                <div class="box-body">

                    <table class="table table-hover">

                        <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>teacher</th>
                                    <th>phone</th>
                                    <th>country</th>
                                    <th>gender</th>
                                    <th>balance</th>
                                    <th>status</th>
                                    <th>action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($teachers as $teacher)
                                    <tr>
                                        <td>{{$teacher->id}}</td>
                                        <td>{{$teacher->username}}</td>
                                        <td>{{$teacher->dialing_code}} => {{$teacher->phone}}</td>
                                        <td>{{$teacher->Country->translate('en')->name}}</td>
                                        <td>{{$teacher->getGender()}}</td>
                                        <td>{{$teacher->balance}}</td>
                                        <td>{{$teacher->getStatus()}}</td>
                                        <td>
                                            {{-- block --}}
                                            @if (auth('admin')->user()->isAbleTo('delete-teachers'))
                                                <a href="{{url('admins/teachers/block/' . $teacher->id)}}" tyle="color:#fff!important;" rel="tooltip" title="" @if ($teacher->status == 1) class="btn  btn-danger  btn-sm" @else class="btn  btn-success  btn-sm" @endif >
                                                    <i class="fa fa-1x fa-trash">{{$teacher->changeStatus()}}</i>
                                                </a>
                                            @else
                                                <button class="btn btn-danger btn-sm"type="submit" value="" disabled>
                                                    <i class="fa fa-1x fa-trash">{{$teacher->changeStatus()}}</i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                
                            </tbody>

                        </table> {{-- end of table --}}

                </div> {{-- end of box body --}}

            </div> {{-- end of box --}}

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection
