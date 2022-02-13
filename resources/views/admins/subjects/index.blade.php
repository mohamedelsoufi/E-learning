@extends('layouts.admin')

@section('title', 'subjects')


@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>subject</h1>

            <ol class="breadcrumb">
                <li> <a href="{{url('admins/subjects')}}"><i class="fa fa-dashboard"></i>dashboard</a>
                </li>
                <li class="active"><i class="fa fa-users"></i>subjects</li>
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
                                @if (auth('admin')->user()->isAbleTo('create-subjects'))
                                    <a href="{{url('admins/subjects/create')}}"
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
                                @foreach ($subjects as $subject)
                                    <tr>
                                        <td>{{$subject->id}}</td>
                                        <td>{{$subject->translate('en')->name}}</td>
                                        <td>
                                            {{$subject->Term->Year->Level->Curriculum->Country->translate('en')->name}} ->
                                            {{$subject->Term->Year->Level->Curriculum->translate('en')->name}} ->
                                            {{$subject->Term->Year->Level->translate('en')->name}} ->
                                            {{$subject->Term->Year->translate('en')->name}} ->
                                            {{$subject->Term->translate('en')->name}}
                                        </td>
                                        <td>{{$subject->getStatus()}}</td>
                                        <td>
                                            {{-- edit --}}
                                            @if (auth('admin')->user()->isAbleTo('update-subjects'))
                                                <a href="{{url('admins/subjects/edit/' . $subject->id)}}" style="color: #fff;
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
                                            @if (auth('admin')->user()->isAbleTo('delete-subjects'))
                                                <a href="{{url('admins/subjects/delete/' . $subject->id)}}" tyle="color:#fff!important;" rel="tooltip" title="" class="btn btn-danger  btn-sm">
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