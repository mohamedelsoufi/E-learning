@extends('layouts.admin')

@section('title', 'levels')


@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>levels</h1>

            <ol class="breadcrumb">
                <li> <a href="{{url('admins/levels')}}"><i class="fa fa-dashboard"></i>dashboard</a>
                </li>
                <li class="active"><i class="fa fa-users"></i>levels</li>
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
                                @if (auth('admin')->user()->isAbleTo('create-levels'))
                                    <a href="{{url('admins/levels/create')}}"
                                    class="btn btn-primary"><i class="fa fa-plus"></i>add
                                    </a>
                                @else
                                    <button class="btn btn-primary"disabled><i class="fa fa-plus"></i>Add </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div> {{-- end of box header --}}

                <div class="box-body">

                    <table class="table table-hover">

                        <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>name</th>
                                    <th>description</th>
                                    <th>status</th>
                                    <th>action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($levels as $level)
                                    <tr>
                                        <td>{{$level->id}}</td>
                                        <td>{{$level->translate('en')->name}}</td>
                                        <td>{{$level->Curriculum->Country->translate('en')->name}} -> {{$level->Curriculum->translate('en')->name}}</td>
                                        <td>{{$level->getStatus()}}</td>
                                        <td>
                                            {{-- edit --}}
                                            @if (auth('admin')->user()->isAbleTo('update-levels'))
                                                <a href="{{url('admins/levels/edit/' . $level->id)}}" style="color: #fff;
                                                    background-color: #17a2b8;
                                                    border-color: #17a2b8;" rel="tooltip" title="" class="btn btn-info btn-sm "
                                                        data-original-title="edit">
                                                        <i class="fa fa-edit">edit</i>
                                                </a>
                                            @else
                                                <button class="btn btn-info btn-sm"type="submit" value="" disabled>
                                                    <i class="fa fa-edit">edit</i>
                                                </button>
                                            @endif

                                            {{-- delete --}}
                                            @if (auth('admin')->user()->isAbleTo('delete-levels'))
                                                <a href="{{url('admins/levels/delete/' . $level->id)}}" tyle="color:#fff!important;" rel="tooltip" title="" class="btn btn-danger  btn-sm">
                                                    <i class="fa fa-1x fa-trash">delete</i>
                                                </a> 
                                            @else
                                                <button class="btn btn-danger btn-sm"type="submit" value="" disabled>
                                                    <i class="fa fa-trash">delete</i>
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